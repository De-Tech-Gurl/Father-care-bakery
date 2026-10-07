<?php

namespace App\Services;

use App\Enums\DeliveryType;
use App\Enums\InventoryReason;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Events\NotificationRequested;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        protected CartService $cart,
        protected InventoryService $inventory,
    ) {}

    /**
     * @param  array{delivery_type: string, payment_method: string, phone: string, delivery_address?: string|null, notes?: string|null, payment_receipt?: string|null}  $data
     */
    public function place(User $user, array $data): Order
    {
        $order = DB::transaction(function () use ($user, $data) {
            $lines = $this->cart->lines();

            if ($lines->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Your cart is empty.',
                ]);
            }

            $paymentMethod = PaymentMethod::from($data['payment_method']);
            $deliveryType = DeliveryType::from($data['delivery_type']);

            if ($paymentMethod === PaymentMethod::BANK_TRANSFER && blank($data['payment_receipt'] ?? null)) {
                throw ValidationException::withMessages([
                    'payment_receipt' => 'Please upload your bank transfer receipt before placing the order.',
                ]);
            }

            $total = 0;
            $preparedItems = [];

            foreach ($lines as $line) {
                $product = Product::query()
                    ->whereKey($line['product']->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (! $product->is_active || $product->stock_quantity < $line['quantity']) {
                    throw ValidationException::withMessages([
                        'cart' => "{$product->name} does not have enough stock.",
                    ]);
                }

                $lineTotal = (float) $product->price * $line['quantity'];
                $total += $lineTotal;
                $preparedItems[] = [
                    'product' => $product,
                    'quantity' => $line['quantity'],
                    'price' => $product->price,
                    'total' => $lineTotal,
                ];
            }

            $orderNumber = $this->generateOrderNumber();

            $order = Order::query()->create([
                'user_id' => $user->id,
                'order_number' => $orderNumber,
                'total_amount' => $total,
                'status' => OrderStatus::PENDING,
                'delivery_type' => $deliveryType,
                'delivery_address' => $deliveryType === DeliveryType::DELIVERY ? $data['delivery_address'] : null,
                'phone' => $data['phone'],
                'notes' => $data['notes'] ?? null,
                'payment_method' => $paymentMethod,
                'payment_status' => PaymentStatus::AWAITING_PAYMENT,
                'payment_reference' => $paymentMethod->usesPaystack()
                    ? $this->generatePaymentReference($orderNumber)
                    : null,
                'payment_receipt' => $data['payment_receipt'] ?? null,
                'paid_at' => null,
            ]);

            foreach ($preparedItems as $item) {
                $order->items()->create([
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);

                $this->inventory->record(
                    $item['product'],
                    -$item['quantity'],
                    InventoryReason::SALE,
                    $order->order_number,
                    $user->id,
                );
            }

            $user->update([
                'phone' => $data['phone'],
                'address' => $deliveryType === DeliveryType::DELIVERY
                    ? $data['delivery_address']
                    : $user->address,
            ]);

            $this->cart->clear();

            return $order->load('items.product');
        });

        event(new NotificationRequested(
            'order.placed',
            'Order received',
            'Your order '.$order->order_number.' has been received.',
            'user',
            $user->id,
            route('customer.orders.show', $order),
            'ph-receipt',
            ['order_id' => $order->id, 'order_number' => $order->order_number],
        ));

        event(new NotificationRequested(
            'order.placed',
            'New order placed',
            $order->order_number.' was placed by '.$user->name.'.',
            'staff',
            url: route('admin.orders.show', $order),
            icon: 'ph-receipt',
            context: ['order_id' => $order->id, 'order_number' => $order->order_number],
        ));

        return $order;
    }

    public function markPaid(Order $order, ?string $reference = null): Order
    {
        if ($order->payment_status === PaymentStatus::PAID) {
            return $order->fresh(['items.product', 'user']);
        }

        $previousStatus = $order->status;
        $paidOrder = DB::transaction(function () use ($order, $reference) {
            if ($order->payment_status === PaymentStatus::PAID) {
                return $order->fresh(['items.product', 'user']);
            }

            $order->update([
                'payment_status' => PaymentStatus::PAID,
                'payment_reference' => $reference ?: $order->payment_reference,
                'paid_at' => now(),
                'status' => $order->status === OrderStatus::PENDING
                    ? OrderStatus::CONFIRMED
                    : $order->status,
            ]);

            return $order->fresh(['items.product', 'user']);
        });

        event(new NotificationRequested(
            'payment.received',
            'Payment received',
            'Payment for order '.$paidOrder->order_number.' has been received.',
            'user',
            $paidOrder->user_id,
            route('customer.orders.show', $paidOrder),
            'ph-check-circle',
            ['order_id' => $paidOrder->id, 'order_number' => $paidOrder->order_number],
        ));

        if ($previousStatus !== $paidOrder->status) {
            $this->notifyOrderStatusChanged($paidOrder);
        }

        return $paidOrder;
    }

    public function markPaymentFailed(Order $order): Order
    {
        if ($order->payment_status === PaymentStatus::PAID) {
            return $order;
        }

        if ($order->payment_status === PaymentStatus::FAILED) {
            return $order->fresh(['items.product', 'user']);
        }

        $order->update([
            'payment_status' => PaymentStatus::FAILED,
        ]);

        $failedOrder = $order->fresh(['items.product', 'user']);

        event(new NotificationRequested(
            'payment.failed',
            'Payment was not completed',
            'Payment for order '.$failedOrder->order_number.' was not completed. You can try again.',
            'user',
            $failedOrder->user_id,
            route('customer.orders.show', $failedOrder),
            'ph-warning-circle',
            ['order_id' => $failedOrder->id, 'order_number' => $failedOrder->order_number],
        ));

        return $failedOrder;
    }

    public function updatePaymentStatus(Order $order, PaymentStatus $status): Order
    {
        if ($status === PaymentStatus::PAID) {
            return $this->markPaid($order);
        }

        if ($status === PaymentStatus::FAILED) {
            return $this->markPaymentFailed($order);
        }

        if ($order->payment_status === $status) {
            return $order->fresh(['items.product', 'user']);
        }

        $order->update(['payment_status' => $status]);
        $updatedOrder = $order->fresh(['items.product', 'user']);

        event(new NotificationRequested(
            'payment.status_changed',
            'Payment status updated',
            'Payment for order '.$updatedOrder->order_number.' is now '.$status->label().'.',
            'user',
            $updatedOrder->user_id,
            route('customer.orders.show', $updatedOrder),
            'ph-credit-card',
            ['order_id' => $updatedOrder->id, 'order_number' => $updatedOrder->order_number, 'payment_status' => $status->value],
        ));

        return $updatedOrder;
    }

    public function updateStatus(Order $order, OrderStatus $status): Order
    {
        $previousStatus = $order->status;
        $updatedOrder = DB::transaction(function () use ($order, $status) {
            if ($status === OrderStatus::CANCELLED && $order->status !== OrderStatus::CANCELLED) {
                $this->restoreStock($order);
            }

            $order->update(['status' => $status]);

            return $order->fresh(['items.product', 'user']);
        });

        if ($previousStatus !== $updatedOrder->status) {
            $this->notifyOrderStatusChanged($updatedOrder);
        }

        return $updatedOrder;
    }

    public function cancel(Order $order): Order
    {
        if (! $order->canBeCancelled()) {
            throw ValidationException::withMessages([
                'status' => 'This order can no longer be cancelled.',
            ]);
        }

        return $this->updateStatus($order, OrderStatus::CANCELLED);
    }

    private function restoreStock(Order $order): void
    {
        $order->loadMissing('items.product');

        foreach ($order->items as $item) {
            if (! $item->product) {
                continue;
            }

            $this->inventory->record(
                $item->product,
                $item->quantity,
                InventoryReason::ORDER_CANCELLED,
                $order->order_number,
            );
        }
    }

    private function notifyOrderStatusChanged(Order $order): void
    {
        event(new NotificationRequested(
            'order.status_changed',
            'Order status updated',
            'Order '.$order->order_number.' is now '.$order->status->label().'.',
            'user',
            $order->user_id,
            route('customer.orders.show', $order),
            'ph-package',
            ['order_id' => $order->id, 'order_number' => $order->order_number, 'status' => $order->status->value],
        ));
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'FCB-'.now()->format('Ymd').'-'.strtoupper(Str::random(4));
        } while (Order::query()->where('order_number', $number)->exists());

        return $number;
    }

    private function generatePaymentReference(string $orderNumber): string
    {
        return strtoupper(str_replace('-', '', $orderNumber)).Str::upper(Str::random(6));
    }
}

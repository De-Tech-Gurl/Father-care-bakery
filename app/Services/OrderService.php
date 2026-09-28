<?php

namespace App\Services;

use App\Enums\DeliveryType;
use App\Enums\InventoryReason;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
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
        return DB::transaction(function () use ($user, $data) {
            $lines = $this->cart->lines();

            if ($lines->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Your cart is empty.',
                ]);
            }

            $paymentMethod = PaymentMethod::from($data['payment_method']);
            $deliveryType = DeliveryType::from($data['delivery_type']);

            if ($paymentMethod === PaymentMethod::CASH_ON_DELIVERY && $deliveryType !== DeliveryType::DELIVERY) {
                throw ValidationException::withMessages([
                    'payment_method' => 'Cash on delivery is only available for home delivery orders.',
                ]);
            }

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
    }

    public function markPaid(Order $order, ?string $reference = null): Order
    {
        return DB::transaction(function () use ($order, $reference) {
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
    }

    public function markPaymentFailed(Order $order): Order
    {
        if ($order->payment_status === PaymentStatus::PAID) {
            return $order;
        }

        $order->update([
            'payment_status' => PaymentStatus::FAILED,
        ]);

        return $order->fresh(['items.product', 'user']);
    }

    public function updateStatus(Order $order, OrderStatus $status): Order
    {
        return DB::transaction(function () use ($order, $status) {
            if ($status === OrderStatus::CANCELLED && $order->status !== OrderStatus::CANCELLED) {
                $this->restoreStock($order);
            }

            $order->update(['status' => $status]);

            return $order->fresh(['items.product', 'user']);
        });
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

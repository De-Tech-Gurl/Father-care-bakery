<?php

namespace App\Http\Controllers\Customer;

use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CheckoutRequest;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PaystackService;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    use ImageUploadTrait;

    public function __construct(
        protected CartService $cart,
        protected OrderService $orders,
        protected PaystackService $paystack,
    ) {}

    public function create(): View|RedirectResponse
    {
        if ($this->cart->isEmpty()) {
            return redirect()
                ->route('customer.cart.index')
                ->with('error', 'Your cart is empty.');
        }

        return view('customer.checkout.create', [
            'lines' => $this->cart->lines(),
            'subtotal' => $this->cart->subtotal(),
            'paymentMethods' => PaymentMethod::cases(),
            'paystackReady' => $this->paystack->isConfigured(),
            'bank' => config('bakery.bank'),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('payment_receipt');

        if (
            $request->input('payment_method') === PaymentMethod::BANK_TRANSFER->value
            && $request->hasFile('payment_receipt')
        ) {
            $data['payment_receipt'] = $this->uploadImage(
                $request->file('payment_receipt'),
                'receipts',
            );
        }

        $order = $this->orders->place($request->user(), $data);
        $method = $order->payment_method;

        if ($method?->usesPaystack()) {
            if (! $this->paystack->isConfigured()) {
                return redirect()
                    ->route('customer.orders.show', $order)
                    ->with('error', 'Online payment is not available yet. Please choose bank transfer or contact the bakery.');
            }

            try {
                $payment = $this->paystack->initialize(
                    $order,
                    (string) $request->user()->email,
                    $method->paystackChannels(),
                );

                return redirect()->away($payment['authorization_url']);
            } catch (Throwable $e) {
                $this->orders->markPaymentFailed($order);

                report($e);

                return redirect()
                    ->route('customer.orders.show', $order)
                    ->with('error', 'We could not start the payment. You can try again from your order page.');
            }
        }

        $message = match ($method) {
            PaymentMethod::BANK_TRANSFER => 'Order placed with your transfer receipt. We will confirm payment shortly.',
            PaymentMethod::CASH_ON_DELIVERY => 'Order placed. Please pay cash when your order is delivered.',
            default => 'Order placed successfully.',
        };

        return redirect()
            ->route('customer.orders.show', $order)
            ->with('success', $message);
    }
}

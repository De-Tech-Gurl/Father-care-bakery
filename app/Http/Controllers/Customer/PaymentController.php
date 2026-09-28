<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaystackService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class PaymentController extends Controller
{
    public function __construct(
        protected OrderService $orders,
        protected PaystackService $paystack,
    ) {}

    public function callback(Request $request): RedirectResponse
    {
        $reference = (string) $request->query('reference', '');

        if ($reference === '') {
            return redirect()
                ->route('customer.orders.index')
                ->with('error', 'Missing payment reference.');
        }

        $order = Order::query()
            ->where('payment_reference', $reference)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $order) {
            return redirect()
                ->route('customer.orders.index')
                ->with('error', 'We could not find that payment.');
        }

        try {
            $result = $this->paystack->verify($reference);
        } catch (Throwable $e) {
            report($e);
            $this->orders->markPaymentFailed($order);

            return redirect()
                ->route('customer.orders.show', $order)
                ->with('error', 'Payment verification failed. You can try again.');
        }

        $amountPaid = ((int) ($result['amount'] ?? 0)) / 100;
        $expected = (float) $order->total_amount;

        if (($result['status'] ?? null) === 'success' && abs($amountPaid - $expected) < 0.01) {
            $this->orders->markPaid($order, $reference);

            return redirect()
                ->route('customer.orders.show', $order)
                ->with('success', 'Payment received. Thank you!');
        }

        $this->orders->markPaymentFailed($order);

        return redirect()
            ->route('customer.orders.show', $order)
            ->with('error', 'Payment was not completed. You can try again.');
    }

    public function retry(Order $order): RedirectResponse
    {
        abort_unless($order->user_id === auth()->id(), 403);

        if (! $order->needsOnlinePayment()) {
            return redirect()
                ->route('customer.orders.show', $order)
                ->with('error', 'This order does not need an online payment.');
        }

        if (! $this->paystack->isConfigured()) {
            return redirect()
                ->route('customer.orders.show', $order)
                ->with('error', 'Online payment is not available yet.');
        }

        try {
            $payment = $this->paystack->initialize(
                $order,
                (string) auth()->user()->email,
                $order->payment_method?->paystackChannels(),
            );

            return redirect()->away($payment['authorization_url']);
        } catch (Throwable $e) {
            report($e);
            $this->orders->markPaymentFailed($order);

            return redirect()
                ->route('customer.orders.show', $order)
                ->with('error', 'We could not restart the payment. Please try again shortly.');
        }
    }
}

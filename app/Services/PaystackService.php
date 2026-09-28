<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PaystackService
{
    public function isConfigured(): bool
    {
        return filled(config('services.paystack.secret_key'))
            && filled(config('services.paystack.public_key'));
    }

    /**
     * @param  list<string>|null  $channels
     * @return array{authorization_url: string, access_code: string, reference: string}
     */
    public function initialize(Order $order, string $email, ?array $channels = null): array
    {
        $this->ensureConfigured();

        $payload = [
            'email' => $email,
            'amount' => (int) round(((float) $order->total_amount) * 100),
            'reference' => $order->payment_reference,
            'currency' => config('services.paystack.currency', 'NGN'),
            'callback_url' => route('customer.payments.callback'),
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_method' => $order->payment_method?->value,
            ],
        ];

        if ($channels) {
            $payload['channels'] = $channels;
        }

        $response = Http::withToken((string) config('services.paystack.secret_key'))
            ->acceptJson()
            ->post('https://api.paystack.co/transaction/initialize', $payload);

        if (! $response->successful() || ! ($response->json('status') === true)) {
            throw new RuntimeException($response->json('message') ?? 'Unable to start Paystack payment.');
        }

        /** @var array{authorization_url: string, access_code: string, reference: string} $data */
        $data = $response->json('data');

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    public function verify(string $reference): array
    {
        $this->ensureConfigured();

        $response = Http::withToken((string) config('services.paystack.secret_key'))
            ->acceptJson()
            ->get('https://api.paystack.co/transaction/verify/'.rawurlencode($reference));

        if (! $response->successful() || ! ($response->json('status') === true)) {
            throw new RuntimeException($response->json('message') ?? 'Unable to verify Paystack payment.');
        }

        /** @var array<string, mixed> $data */
        $data = $response->json('data');

        return $data;
    }

    private function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Paystack is not configured. Add PAYSTACK_PUBLIC_KEY and PAYSTACK_SECRET_KEY to your .env file.');
        }
    }
}

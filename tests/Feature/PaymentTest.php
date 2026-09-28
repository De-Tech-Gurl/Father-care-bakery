<?php

namespace Tests\Feature;

use App\Enums\DeliveryType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_card_checkout_redirects_to_paystack_when_configured(): void
    {
        config([
            'services.paystack.public_key' => 'pk_test_example',
            'services.paystack.secret_key' => 'sk_test_example',
        ]);

        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'message' => 'Authorization URL created',
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/test-auth',
                    'access_code' => 'access_code',
                    'reference' => 'ignored-by-us',
                ],
            ], 200),
        ]);

        $user = User::factory()->create(['email' => 'buyer@example.com']);
        $product = Product::factory()->create(['price' => 1500, 'stock_quantity' => 5]);

        $this->actingAs($user)->postJson(route('customer.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertOk();

        $this->actingAs($user)->post(route('customer.checkout.store'), [
            'delivery_type' => DeliveryType::PICKUP->value,
            'payment_method' => PaymentMethod::CARD->value,
            'phone' => '08139502961',
        ])->assertRedirect('https://checkout.paystack.com/test-auth');

        $order = Order::query()->first();
        $this->assertNotNull($order);
        $this->assertSame(PaymentMethod::CARD, $order->payment_method);
        $this->assertSame(PaymentStatus::AWAITING_PAYMENT, $order->payment_status);
        $this->assertNotNull($order->payment_reference);
    }

    public function test_paystack_callback_marks_order_as_paid(): void
    {
        config([
            'services.paystack.public_key' => 'pk_test_example',
            'services.paystack.secret_key' => 'sk_test_example',
        ]);

        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'total_amount' => 2000,
            'payment_method' => PaymentMethod::CARD,
            'payment_status' => PaymentStatus::AWAITING_PAYMENT,
            'payment_reference' => 'FCBTESTREF123',
            'status' => OrderStatus::PENDING,
        ]);

        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'message' => 'Verification successful',
                'data' => [
                    'status' => 'success',
                    'amount' => 200000,
                    'reference' => 'FCBTESTREF123',
                ],
            ], 200),
        ]);

        $this->actingAs($user)
            ->get(route('customer.payments.callback', ['reference' => 'FCBTESTREF123']))
            ->assertRedirect(route('customer.orders.show', $order));

        $order->refresh();
        $this->assertSame(PaymentStatus::PAID, $order->payment_status);
        $this->assertSame(OrderStatus::CONFIRMED, $order->status);
        $this->assertNotNull($order->paid_at);
    }

    public function test_admin_can_mark_bank_transfer_order_as_paid(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create([
            'payment_method' => PaymentMethod::BANK_TRANSFER,
            'payment_status' => PaymentStatus::AWAITING_PAYMENT,
            'status' => OrderStatus::PENDING,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.orders.payment', $order), [
                'payment_status' => PaymentStatus::PAID->value,
            ])
            ->assertRedirect();

        $order->refresh();
        $this->assertSame(PaymentStatus::PAID, $order->payment_status);
        $this->assertSame(OrderStatus::CONFIRMED, $order->status);
    }

    public function test_bank_transfer_order_shows_payment_instructions(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'payment_method' => PaymentMethod::BANK_TRANSFER,
            'payment_status' => PaymentStatus::AWAITING_PAYMENT,
        ]);

        $this->actingAs($user)
            ->get(route('customer.orders.show', $order))
            ->assertOk()
            ->assertSee('Bank transfer')
            ->assertSee(config('bakery.bank.account_number'));
    }
}

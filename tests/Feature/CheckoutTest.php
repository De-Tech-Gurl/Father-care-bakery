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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_history_is_private_to_authenticated_users(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $owner->id]);

        $this->get(route('customer.orders.index'))
            ->assertRedirect(route('login'));

        $this->actingAs($owner)
            ->get(route('customer.orders.show', $order))
            ->assertOk();

        $this->actingAs($otherUser)
            ->get(route('customer.orders.show', $order))
            ->assertForbidden();
    }

    public function test_guests_are_redirected_from_checkout(): void
    {
        $this->get(route('customer.checkout.create'))
            ->assertRedirect(route('login'));
    }

    public function test_customer_can_place_a_pickup_order_with_bank_receipt(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $product = Product::factory()->create([
            'price' => 500,
            'stock_quantity' => 10,
        ]);

        $this->actingAs($user)
            ->postJson(route('customer.cart.add'), [
                'product_id' => $product->id,
                'quantity' => 2,
            ])->assertOk();

        $response = $this->actingAs($user)->post(route('customer.checkout.store'), [
            'delivery_type' => DeliveryType::PICKUP->value,
            'payment_method' => PaymentMethod::BANK_TRANSFER->value,
            'phone' => '08139502961',
            'notes' => 'Please pack separately',
            'payment_receipt' => UploadedFile::fake()->image('transfer-receipt.jpg', 800, 1000),
        ]);

        $order = Order::query()->where('user_id', $user->id)->first();

        $response->assertRedirect(route('customer.orders.show', $order));
        $this->assertNotNull($order);
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => OrderStatus::PENDING->value,
            'total_amount' => 1000,
            'phone' => '08139502961',
            'payment_method' => PaymentMethod::BANK_TRANSFER->value,
            'payment_status' => PaymentStatus::AWAITING_PAYMENT->value,
        ]);
        $this->assertNotNull($order->payment_receipt);
        Storage::disk('public')->assertExists($order->payment_receipt);
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2,
            'product_name' => $product->name,
        ]);
        $this->assertSame(8, $product->fresh()->stock_quantity);
        $this->assertEmpty(session('cart', []));
    }

    public function test_bank_transfer_requires_a_receipt(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user)->postJson(route('customer.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->actingAs($user)->post(route('customer.checkout.store'), [
            'delivery_type' => DeliveryType::PICKUP->value,
            'payment_method' => PaymentMethod::BANK_TRANSFER->value,
            'phone' => '08139502961',
        ])->assertSessionHasErrors('payment_receipt');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_delivery_orders_require_an_address(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user)->postJson(route('customer.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->actingAs($user)->post(route('customer.checkout.store'), [
            'delivery_type' => DeliveryType::DELIVERY->value,
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY->value,
            'phone' => '08139502961',
        ])->assertSessionHasErrors('delivery_address');
    }

    public function test_cash_on_delivery_requires_home_delivery(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user)->postJson(route('customer.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->actingAs($user)->post(route('customer.checkout.store'), [
            'delivery_type' => DeliveryType::PICKUP->value,
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY->value,
            'phone' => '08139502961',
        ])->assertSessionHasErrors('payment_method');
    }

    public function test_checkout_requires_a_payment_method(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user)->postJson(route('customer.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->actingAs($user)->post(route('customer.checkout.store'), [
            'delivery_type' => DeliveryType::PICKUP->value,
            'phone' => '08139502961',
        ])->assertSessionHasErrors('payment_method');
    }
}

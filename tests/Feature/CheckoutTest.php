<?php

namespace Tests\Feature;

use App\Enums\DeliveryType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\BakeryNotification;
use Illuminate\Contracts\Notifications\Dispatcher as NotificationDispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
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

    public function test_customer_can_place_a_pickup_order_with_cash_on_delivery(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user)->postJson(route('customer.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user)->post(route('customer.checkout.store'), [
            'delivery_type' => DeliveryType::PICKUP->value,
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY->value,
            'phone' => '08139502961',
        ]);

        $order = Order::query()->where('user_id', $user->id)->firstOrFail();

        $response->assertRedirect(route('customer.orders.show', $order))
            ->assertSessionHas('success', 'Order placed. Please pay cash when you collect it at the bakery.');

        $this->assertSame(DeliveryType::PICKUP, $order->delivery_type);
        $this->assertSame(PaymentMethod::CASH_ON_DELIVERY, $order->payment_method);
        $this->assertSame(PaymentStatus::AWAITING_PAYMENT, $order->payment_status);

        $this->actingAs($user)
            ->get(route('customer.orders.show', $order))
            ->assertOk()
            ->assertSee('Pay at pickup')
            ->assertSee('Pickup at the bakery');
    }

    public function test_order_placement_notifies_customer_admin_and_staff(): void
    {
        $customer = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $product = Product::factory()->create();

        $this->actingAs($customer)->postJson(route('customer.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertOk();

        Notification::fake();

        $this->actingAs($customer)->post(route('customer.checkout.store'), [
            'delivery_type' => DeliveryType::PICKUP->value,
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY->value,
            'phone' => '08139502961',
        ])->assertRedirect();

        $isOrderPlaced = fn (BakeryNotification $notification): bool => $notification->type === 'order.placed';

        Notification::assertSentTo($customer, BakeryNotification::class, $isOrderPlaced);
        Notification::assertSentTo($admin, BakeryNotification::class, $isOrderPlaced);
        Notification::assertSentTo($staff, BakeryNotification::class, $isOrderPlaced);
    }

    public function test_notification_dispatch_failure_is_logged_without_failing_order_placement(): void
    {
        $customer = User::factory()->create();
        User::factory()->admin()->create();
        User::factory()->staff()->create();
        $product = Product::factory()->create();

        $this->actingAs($customer)->postJson(route('customer.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertOk();

        $this->instance(NotificationDispatcher::class, new class implements NotificationDispatcher
        {
            public function send($notifiables, $notification)
            {
                throw new RuntimeException('Mail transport unavailable.');
            }

            public function sendNow($notifiables, $notification, ?array $channels = null)
            {
                throw new RuntimeException('Mail transport unavailable.');
            }
        });

        Log::shouldReceive('error')
            ->times(3)
            ->with('Unable to queue notification delivery.', \Mockery::type('array'));

        $response = $this->actingAs($customer)->post(route('customer.checkout.store'), [
            'delivery_type' => DeliveryType::PICKUP->value,
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY->value,
            'phone' => '08139502961',
        ]);

        $order = Order::query()->where('user_id', $customer->id)->firstOrFail();

        $response->assertRedirect(route('customer.orders.show', $order));
        $this->assertDatabaseCount('orders', 1);
    }

    public function test_delivery_cash_on_delivery_shows_when_payment_is_due(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'delivery_type' => DeliveryType::DELIVERY,
            'payment_method' => PaymentMethod::CASH_ON_DELIVERY,
            'payment_status' => PaymentStatus::AWAITING_PAYMENT,
        ]);

        $this->actingAs($user)
            ->get(route('customer.orders.show', $order))
            ->assertOk()
            ->assertSee('Pay on delivery')
            ->assertSee('Home delivery');
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

    public function test_orders_summary_counts_all_open_orders_as_in_progress(): void
    {
        $user = User::factory()->create();
        Order::factory()->count(11)->for($user)->create();
        Order::factory()->for($user)->create(['status' => OrderStatus::COMPLETED]);

        $this->actingAs($user)
            ->get(route('customer.orders.index'))
            ->assertOk()
            ->assertSee('<span class="summary-label">In progress</span><strong>11</strong>', false)
            ->assertDontSee('On the way');
    }
}

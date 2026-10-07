<?php

namespace Tests\Feature;

use App\Events\NotificationRequested;
use App\Listeners\DispatchNotification;
use App\Models\Product;
use App\Models\User;
use App\Models\UserNotification;
use App\Notifications\BakeryNotification;
use App\Services\InventoryService;
use Database\Seeders\NotificationDemoSeeder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class NotificationSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_customer_pages_render_with_the_notification_schema_installed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('customer.cart.index'))
            ->assertOk()
            ->assertSee('aria-label="Notifications"', false);
    }

    public function test_notification_channels_default_to_in_app_and_respect_user_preferences(): void
    {
        $user = User::factory()->make();
        $notification = new BakeryNotification('order.status_changed', 'Order updated', 'Your order is ready.');

        $this->assertSame(['database', 'mail'], $notification->via($user));
        $this->assertInstanceOf(ShouldQueue::class, $notification);

        $user->notification_preferences = [
            'order.status_changed' => ['database' => false, 'mail' => true],
            'payment.failed' => ['database' => false, 'mail' => false],
            'inventory.low_stock' => ['database' => true, 'mail' => false],
        ];

        $this->assertSame(['mail'], $notification->via($user));
        $this->assertSame([], (new BakeryNotification('payment.failed', 'Payment failed', 'Retry payment.'))->via($user));
        $this->assertSame(['database'], (new BakeryNotification('inventory.low_stock', 'Low stock', 'Product is low.'))->via($user));
    }

    public function test_order_emails_are_enabled_by_default_for_customers_admins_and_staff(): void
    {
        $customer = User::factory()->make();
        $admin = User::factory()->admin()->make();
        $staff = User::factory()->staff()->make();
        $orderConfirmation = new BakeryNotification('order.placed', 'Order received', 'Your order was received.');
        $orderUpdate = new BakeryNotification('order.status_changed', 'Order updated', 'Your order is ready.');

        $this->assertSame(['database', 'mail'], $orderConfirmation->via($customer));
        $this->assertSame(['database', 'mail'], $orderConfirmation->via($admin));
        $this->assertSame(['database', 'mail'], $orderConfirmation->via($staff));
        $this->assertSame(['database', 'mail'], $orderUpdate->via($customer));
    }

    public function test_staff_alerts_are_not_sent_to_customers(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $customer = User::factory()->create();
        Notification::fake();

        app(DispatchNotification::class)->handle(new NotificationRequested(
            'inventory.low_stock',
            'Low stock attention',
            'Bread has 2 remaining.',
            'staff',
        ));

        Notification::assertSentTo($admin, BakeryNotification::class, fn (BakeryNotification $notification): bool => $notification->type === 'inventory.low_stock');
        Notification::assertSentTo($staff, BakeryNotification::class, fn (BakeryNotification $notification): bool => $notification->type === 'inventory.low_stock');
        Notification::assertNotSentTo($customer, BakeryNotification::class);
    }

    #[DataProvider('notificationTypeAudiences')]
    public function test_configured_notification_types_dispatch_to_the_expected_audience(string $type, string $audience): void
    {
        $recipient = $audience === 'staff'
            ? User::factory()->admin()->create()
            : User::factory()->create();
        Notification::fake();

        app(DispatchNotification::class)->handle(new NotificationRequested(
            $type,
            'Test notification',
            'Test message.',
            $audience,
            $audience === 'user' ? $recipient->id : null,
        ));

        Notification::assertSentTo($recipient, BakeryNotification::class, fn (BakeryNotification $notification): bool => $notification->type === $type);
    }

    public static function notificationTypeAudiences(): array
    {
        return [
            'new order to staff' => ['order.placed', 'staff'],
            'new order to customer' => ['order.placed', 'user'],
            'order status to customer' => ['order.status_changed', 'user'],
            'payment received to customer' => ['payment.received', 'user'],
            'payment failed to customer' => ['payment.failed', 'user'],
            'payment status update to customer' => ['payment.status_changed', 'user'],
            'low stock to staff' => ['inventory.low_stock', 'staff'],
            'out of stock to staff' => ['inventory.out_of_stock', 'staff'],
            'restock to staff' => ['inventory.restocked', 'staff'],
            'product created to staff' => ['product.created', 'staff'],
            'product price changed to staff' => ['product.price_changed', 'staff'],
            'product deactivated to staff' => ['product.deactivated', 'staff'],
            'daily report to staff' => ['report.daily_summary', 'staff'],
            'weekly report to staff' => ['report.weekly_ready', 'staff'],
            'registration to staff' => ['user.registered', 'staff'],
            'role changed to user' => ['user.role_changed', 'user'],
            'role changed to staff' => ['user.role_changed', 'staff'],
            'password changed to user' => ['user.password_changed', 'user'],
            'failed login to staff' => ['security.failed_login', 'staff'],
            'scheduled failure to staff' => ['system.scheduled_task_failed', 'staff'],
            'queue failure to staff' => ['system.queue_job_failed', 'staff'],
            'backup result to staff' => ['system.backup_result', 'staff'],
        ];
    }

    public function test_notification_center_only_shows_and_mutates_the_signed_in_users_records(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $ownNotification = $this->createNotification($user, 'Your order is ready.');
        $otherNotification = $this->createNotification($otherUser, 'Private customer update.');

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Your order is ready.')
            ->assertDontSee('Private customer update.');

        $this->actingAs($user)
            ->patch(route('notifications.read', $ownNotification))
            ->assertRedirect();

        $this->assertNotNull($ownNotification->fresh()->read_at);

        $this->actingAs($user)
            ->delete(route('notifications.destroy', $otherNotification))
            ->assertForbidden();

        $this->assertModelExists($otherNotification->fresh());
    }

    public function test_unread_count_and_mark_all_read_are_scoped_to_the_signed_in_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $unreadNotification = $this->createNotification($user, 'Unread update.');
        $readNotification = $this->createNotification($user, 'Read update.');
        $readNotification->markAsRead();
        $otherUnreadNotification = $this->createNotification($otherUser, 'Other unread update.');

        $this->actingAs($user)
            ->getJson(route('notifications.unread-count'))
            ->assertOk()
            ->assertJsonPath('unread_count', 1);

        $this->actingAs($user)
            ->patch(route('notifications.read-all'))
            ->assertRedirect();

        $this->assertNotNull($unreadNotification->fresh()->read_at);
        $this->assertNull($otherUnreadNotification->fresh()->read_at);
    }

    public function test_notification_open_redirects_to_live_products_and_falls_back_for_deleted_products(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create();
        $notification = $this->createProductNotification($admin, $product->id);

        $this->actingAs($admin)
            ->get(route('notifications.open', $notification))
            ->assertRedirect(route('admin.products.edit', $product));

        $product->delete();
        $staleNotification = $this->createProductNotification($admin, $product->id);

        $this->actingAs($admin)
            ->get(route('notifications.open', $staleNotification))
            ->assertRedirect(route('notifications.index'));
    }

    public function test_user_can_save_per_type_channel_preferences_and_disable_a_notification(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('notifications.preferences.update'), [
                'preferences' => [
                    'order.placed' => ['database' => '0', 'mail' => '1'],
                    'payment.failed' => ['database' => '0', 'mail' => '0'],
                ],
            ])
            ->assertRedirect(route('notifications.index'));

        $user->refresh();

        $this->assertSame(['mail'], $user->notificationChannelsFor('order.placed'));
        $this->assertSame([], $user->notificationChannelsFor('payment.failed'));
        $this->assertSame(['database'], $user->notificationChannelsFor('inventory.low_stock'));
    }

    public function test_low_stock_alert_is_deduplicated_until_the_stock_quantity_changes(): void
    {
        $product = Product::factory()->create([
            'stock_quantity' => 6,
            'low_stock_threshold' => 5,
        ]);

        DB::table('products')->where('id', $product->id)->update(['stock_quantity' => 5]);
        $product->refresh();
        $inventory = app(InventoryService::class);

        $this->assertTrue($inventory->alertIfLowStock($product));
        $this->assertFalse($inventory->alertIfLowStock($product->refresh()));
        $this->assertSame(5, $product->fresh()->low_stock_alerted_quantity);

        DB::table('products')->where('id', $product->id)->update(['stock_quantity' => 4]);

        $this->assertTrue($inventory->alertIfLowStock($product->refresh()));
        $this->assertSame(4, $product->fresh()->low_stock_alerted_quantity);

        DB::table('products')->where('id', $product->id)->update(['stock_quantity' => 8]);

        $this->assertFalse($inventory->alertIfLowStock($product->refresh()));
        $this->assertNull($product->fresh()->low_stock_alerted_quantity);
    }

    public function test_failed_login_notifies_staff_without_authenticating_the_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        Notification::fake();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'incorrect-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
        Notification::assertSentTo($admin, BakeryNotification::class, fn (BakeryNotification $notification): bool => $notification->type === 'security.failed_login');
    }

    public function test_demo_seeder_creates_sample_notifications_only_once_per_user(): void
    {
        User::factory()->create();

        $this->seed(NotificationDemoSeeder::class);
        $this->seed(NotificationDemoSeeder::class);

        $this->assertDatabaseCount('notifications', 2);
    }

    private function createNotification(User $user, string $message): UserNotification
    {
        return UserNotification::factory()->create([
            'id' => (string) Str::uuid(),
            'notifiable_type' => $user->getMorphClass(),
            'notifiable_id' => $user->id,
            'data' => [
                'type' => 'order.status_changed',
                'title' => 'Order status changed',
                'message' => $message,
                'url' => route('customer.orders.index'),
                'icon' => 'ph-receipt',
                'context' => [],
            ],
        ]);
    }

    private function createProductNotification(User $user, int $productId): UserNotification
    {
        return UserNotification::factory()->create([
            'id' => (string) Str::uuid(),
            'notifiable_type' => $user->getMorphClass(),
            'notifiable_id' => $user->id,
            'data' => [
                'type' => 'inventory.low_stock',
                'title' => 'Low stock attention',
                'message' => 'Product stock is low.',
                'url' => route('admin.products.index'),
                'icon' => 'ph-warning',
                'context' => ['product_id' => $productId],
            ],
        ]);
    }
}

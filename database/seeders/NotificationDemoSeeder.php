<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Models\UserNotification;
use App\Notifications\BakeryNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NotificationDemoSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->orderBy('id')->chunkById(100, function ($users): void {
            foreach ($users as $user) {
                if ($user->notifications()->exists()) {
                    continue;
                }

                $product = Product::query()->first();
                $samples = $user->isCustomer()
                    ? [
                        ['order.status_changed', 'Order status updated', 'Your order is being prepared.', 'ph-package', route('customer.orders.index')],
                        ['payment.received', 'Payment received', 'Payment for your order has been received.', 'ph-check-circle', route('customer.orders.index')],
                    ]
                    : [
                        ['inventory.low_stock', 'Low stock attention', 'A product needs a stock check.', 'ph-warning', $product ? route('admin.products.edit', $product) : route('admin.inventory.index')],
                        ['report.daily_summary', 'Daily sales summary', 'Yesterday sales summary is ready.', 'ph-chart-line-up', route('admin.reports.index')],
                        ['system.queue_job_failed', 'Queue job failed', 'A queued job failed. Check the application logs.', 'ph-warning-circle', route('admin.dashboard')],
                    ];

                foreach ($samples as $index => [$type, $title, $message, $icon, $url]) {
                    UserNotification::factory()->create([
                        'id' => (string) Str::uuid(),
                        'type' => BakeryNotification::class,
                        'notifiable_type' => $user->getMorphClass(),
                        'notifiable_id' => $user->id,
                        'data' => compact('type', 'title', 'message', 'url', 'icon') + ['context' => []],
                        'read_at' => $index === 0 ? null : now()->subMinutes($index * 15),
                    ]);
                }
            }
        });
    }
}

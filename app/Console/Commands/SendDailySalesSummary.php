<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Events\NotificationRequested;
use App\Models\Order;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

#[Signature('reports:notify-daily')]
#[Description('Notify staff with the previous day sales summary')]
class SendDailySalesSummary extends Command
{
    public function handle(): int
    {
        $from = Carbon::yesterday()->startOfDay();
        $to = Carbon::yesterday()->endOfDay();
        $orders = Order::query()
            ->whereBetween('created_at', [$from, $to])
            ->where('status', '!=', OrderStatus::CANCELLED);
        $total = (clone $orders)->sum('total_amount');
        $count = (clone $orders)->count();
        $currency = config('services.paystack.currency', 'NGN');

        event(new NotificationRequested(
            'report.daily_summary',
            'Daily sales summary',
            sprintf('Yesterday: %d orders with %s %s in sales.', $count, $currency, number_format((float) $total, 2)),
            'staff',
            url: route('admin.reports.index'),
            icon: 'ph-chart-line-up',
            context: ['order_count' => $count, 'sales_total' => (float) $total],
        ));

        $this->info('Daily sales summary notification queued.');

        return self::SUCCESS;
    }
}

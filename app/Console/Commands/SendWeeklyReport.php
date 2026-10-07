<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Events\NotificationRequested;
use App\Models\Order;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

#[Signature('reports:notify-weekly')]
#[Description('Notify staff when the previous full week sales report is ready')]
class SendWeeklyReport extends Command
{
    public function handle(): int
    {
        $to = Carbon::now()->startOfWeek(Carbon::MONDAY)->subSecond();
        $from = $to->copy()->startOfWeek(Carbon::MONDAY);
        $orders = Order::query()
            ->whereBetween('created_at', [$from, $to])
            ->where('status', '!=', OrderStatus::CANCELLED);
        $total = (clone $orders)->sum('total_amount');
        $count = (clone $orders)->count();
        $currency = config('services.paystack.currency', 'NGN');

        event(new NotificationRequested(
            'report.weekly_ready',
            'Weekly report ready',
            sprintf('Last week: %d orders with %s %s in sales.', $count, $currency, number_format((float) $total, 2)),
            'staff',
            url: route('admin.reports.index', ['from' => $from->toDateString(), 'to' => $to->toDateString()]),
            icon: 'ph-chart-bar',
            context: ['order_count' => $count, 'sales_total' => (float) $total],
        ));

        $this->info('Weekly report notification queued.');

        return self::SUCCESS;
    }
}

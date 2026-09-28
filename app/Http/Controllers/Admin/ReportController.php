<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()->toDateString()))->endOfDay();

        $orders = Order::query()
            ->whereBetween('created_at', [$from, $to])
            ->where('status', '!=', OrderStatus::CANCELLED);

        $salesTotal = (clone $orders)->sum('total_amount');
        $orderCount = (clone $orders)->count();

        $topProducts = OrderItem::query()
            ->selectRaw('product_name, SUM(quantity) as quantity_sold, SUM(total) as revenue')
            ->whereHas('order', function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to])
                    ->where('status', '!=', OrderStatus::CANCELLED);
            })
            ->groupBy('product_name')
            ->orderByDesc('revenue')
            ->limit(8)
            ->get();

        $lowStock = Product::query()
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->orderBy('stock_quantity')
            ->get();

        return view('admin.reports.index', compact(
            'from',
            'to',
            'salesTotal',
            'orderCount',
            'topProducts',
            'lowStock',
        ));
    }
}

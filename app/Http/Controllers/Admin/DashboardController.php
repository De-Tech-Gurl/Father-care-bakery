<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $todaySales = Order::query()
            ->whereDate('created_at', today())
            ->where('status', '!=', OrderStatus::CANCELLED)
            ->sum('total_amount');

        $monthlySales = Order::query()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', '!=', OrderStatus::CANCELLED)
            ->sum('total_amount');

        $stats = [
            'products' => Product::query()->count(),
            'low_stock' => Product::query()
                ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                ->count(),
            'pending_orders' => Order::query()->where('status', OrderStatus::PENDING)->count(),
            'today_sales' => $todaySales,
            'monthly_sales' => $monthlySales,
            'customers' => User::query()->where('role', UserRole::CUSTOMER)->count(),
            'total_orders' => Order::query()->count(),
        ];

        $recentOrders = Order::query()
            ->with('user')
            ->latest()
            ->limit(6)
            ->get();

        $lowStockProducts = Product::query()
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->orderBy('stock_quantity')
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'lowStockProducts'));
    }
}

@extends('layouts.admin')

@section('title', 'Reports — Father Care Bakery')

@section('content')
    <div class="page-header fade-in">
        <div>
            <h1>Sales & Inventory Reports</h1>
            <p>Analyze business revenue, top performing bakery items, and inventory status</p>
        </div>
    </div>

    {{-- Filter card --}}
    <form method="GET" class="stat-card mb-4 fade-in fade-in-1">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">From Date</label>
                <input type="date" name="from" value="{{ $from->toDateString() }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">To Date</label>
                <input type="date" name="to" value="{{ $to->toDateString() }}" class="form-control">
            </div>
            <div class="col-md-4">
                <button class="btn btn-success" type="submit" style="height:42px; width:100%;">
                    <i class="ph ph-funnel"></i> Filter Report
                </button>
            </div>
        </div>
    </form>

    {{-- KPI summary --}}
    <div class="row g-3 mb-4 fade-in fade-in-2">
        <div class="col-md-6">
            <div class="stat-card" style="border-left:4px solid var(--brand-choco);">
                <div class="text-muted" style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em;">Total Period Sales</div>
                <div class="fs-3 fw-bold" style="color:var(--brand-ink); margin-top:4px;">₦{{ number_format($salesTotal, 2) }}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card" style="border-left:4px solid var(--brand-choco);">
                <div class="text-muted" style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em;">Orders Placed</div>
                <div class="fs-3 fw-bold" style="color:var(--brand-ink); margin-top:4px;">{{ $orderCount }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4 fade-in fade-in-3">
        <div class="col-lg-7">
            <div class="panel">
                <div class="panel-header">
                    <h2><i class="ph ph-star"></i> Top Selling Products</h2>
                </div>
                <div style="overflow-x:auto;">
                    <table class="saas-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity Sold</th>
                                <th style="text-align:right;">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $row)
                                <tr>
                                    <td>
                                        <strong style="color:var(--brand-ink);">{{ $row->product_name }}</strong>
                                    </td>
                                    <td>
                                        <span class="chip" style="background:#F5EDE6; color:var(--brand-choco); border:1px solid #D8C7B0;">
                                            {{ $row->quantity_sold }} sold
                                        </span>
                                    </td>
                                    <td class="amount-cell" style="text-align:right;">₦{{ number_format($row->revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">
                                        <div class="empty-state">
                                            <i class="ph ph-chart-bar"></i>
                                            No sales recorded for this date period.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="panel">
                <div class="panel-header" style="border-bottom-color:#D8C7B0;">
                    <h2 style="color:var(--brand-choco);"><i class="ph ph-warning" style="color:var(--brand-choco);"></i> Low Stock Attention</h2>
                </div>
                <div style="padding:16px 20px;">
                    @forelse($lowStock as $product)
                        <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #D8C7B0;">
                            <span style="font-weight:600; color:var(--brand-ink); font-size:.86rem;">{{ $product->name }}</span>
                            <span class="chip chip-cancelled">
                                {{ $product->stock_quantity }} remaining
                            </span>
                        </div>
                    @empty
                        <div class="empty-state" style="color:var(--brand-choco);">
                            <i class="ph ph-check-fat" style="color:var(--brand-choco);"></i>
                            All inventory quantities are above thresholds!
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

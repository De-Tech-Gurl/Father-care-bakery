@extends('layouts.admin')

@section('title', 'Reports — Father Care Bakery')

@section('content')
    <div class="report-dashboard">
        <div class="page-header fade-in">
            <div>
                <h1>Sales & Inventory Reports</h1>
                <p>Analyze business revenue, top performing bakery items, and inventory status</p>
            </div>
        </div>

        <form method="GET" class="report-filter mb-4 fade-in fade-in-1">
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
                    <button class="btn btn-success report-btn" type="submit">
                        <i class="ph ph-funnel"></i> Filter Report
                    </button>
                </div>
            </div>
        </form>

        @php
            $avgOrderValue = $orderCount > 0 ? $salesTotal / $orderCount : 0;
            $itemsSold = $topProducts->sum('quantity_sold');
            $lowStockCount = $lowStock->count();
        @endphp

        <div class="report-kpis mb-4 fade-in fade-in-2">
            <div class="report-metric report-metric--primary">
                <div class="report-metric__top">
                    <span class="report-metric__badge report-metric__badge--primary"><i class="ph ph-currency-ngn"></i></span>
                    <span class="report-metric__chip">Period</span>
                </div>
                <span class="report-metric__label">Total Period Sales</span>
                <strong class="report-metric__value">₦{{ number_format($salesTotal, 2) }}</strong>
            </div>

            <div class="report-metric report-metric--secondary">
                <div class="report-metric__top">
                    <span class="report-metric__badge report-metric__badge--secondary"><i class="ph ph-receipt"></i></span>
                    <span class="report-metric__chip">Orders</span>
                </div>
                <span class="report-metric__label">Orders Placed</span>
                <strong class="report-metric__value">{{ $orderCount }}</strong>
            </div>

            <div class="report-metric report-metric--tertiary">
                <div class="report-metric__top">
                    <span class="report-metric__badge report-metric__badge--tertiary"><i class="ph ph-wallet"></i></span>
                    <span class="report-metric__chip">Average</span>
                </div>
                <span class="report-metric__label">Avg. Order Value</span>
                <strong class="report-metric__value">₦{{ number_format($avgOrderValue, 2) }}</strong>
            </div>

            <div class="report-metric report-metric--quaternary">
                <div class="report-metric__top">
                    <span class="report-metric__badge report-metric__badge--quaternary"><i class="ph ph-package"></i></span>
                    <span class="report-metric__chip">Items</span>
                </div>
                <span class="report-metric__label">Low Stock Items</span>
                <strong class="report-metric__value">{{ $lowStockCount }}</strong>
            </div>
        </div>

        <div class="report-grid row g-4 fade-in fade-in-3">
            <div class="col-lg-7">
                <div class="report-panel">
                    <div class="panel-header">
                        <h2><i class="ph ph-star"></i> Top Selling Products</h2>
                    </div>
                    <div class="report-table-wrap">
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
                                        <td><strong style="color:var(--brand-ink);">{{ $row->product_name }}</strong></td>
                                        <td>
                                            <span class="chip chip-pending">
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
                <div class="report-panel report-panel--side">
                    <div class="panel-header">
                        <h2 style="color:var(--brand-choco);"><i class="ph ph-warning" style="color:var(--brand-choco);"></i> Low Stock Attention</h2>
                    </div>
                    <div class="report-stock-list">
                        @forelse($lowStock as $product)
                            <div class="report-stock-item">
                                <span class="report-stock-item__name">{{ $product->name }}</span>
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
    </div>

    @push('styles')
        <style>
            .report-dashboard {
                display: block;
                width: 100%;
            }

            .page-header {
                margin-bottom: 26px;
            }

            .page-header h1 {
                font-size: clamp(2.3rem, 3vw, 4rem);
                letter-spacing: -.06em;
                font-family: var(--font-serif);
                line-height: 0.95;
                margin: 0 0 8px;
            }

            .page-header p {
                font-size: 1.05rem;
                color: var(--brand-muted);
                line-height: 1.5;
            }

            .report-filter {
                background: #fff;
                border: 1px solid var(--card-border);
                border-radius: 18px;
                padding: 18px 20px 16px;
                box-shadow: var(--card-shadow);
                margin-bottom: 22px;
            }

            .report-filter .row {
                display: grid;
                grid-template-columns: 1fr 1fr minmax(220px, 260px);
                gap: 18px;
                align-items: end;
                width: 100%;
                margin: 0;
            }

            .report-filter .form-label {
                margin-bottom: 8px;
                font-size: .74rem;
                letter-spacing: .12em;
            }

            .report-filter .form-control {
                min-height: 58px;
                border-radius: 12px;
                font-size: 1.1rem;
                background: #fff;
                padding: 12px 14px;
            }

            .report-btn {
                width: 100%;
                min-height: 58px;
                border-radius: 12px;
                font-size: 1.05rem;
                font-weight: 700;
            }

            .report-kpis {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 18px;
                margin-bottom: 24px;
                align-items: stretch;
            }

            .report-metric {
                background: #ffffff;
                border: 1px solid var(--card-border);
                border-radius: 18px;
                box-shadow: var(--card-shadow);
                min-height: 170px;
                padding: 18px 20px 20px;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                position: relative;
                overflow: hidden;
            }

            .report-metric::before {
                content: "";
                position: absolute;
                left: 0;
                right: 0;
                top: 0;
                height: 4px;
                background: linear-gradient(90deg, rgba(107,62,31,0.92), rgba(188,155,128,0.8));
            }

            .report-metric--secondary::before {
                background: linear-gradient(90deg, rgba(120,82,54,0.9), rgba(186,152,122,0.8));
            }

            .report-metric--tertiary::before {
                background: linear-gradient(90deg, rgba(92,64,48,0.9), rgba(196,164,132,0.8));
            }

            .report-metric--quaternary::before {
                background: linear-gradient(90deg, rgba(61,43,31,0.9), rgba(188,155,128,0.8));
            }

            .report-metric__top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
            }

            .report-metric__badge {
                width: 42px;
                height: 42px;
                border-radius: 12px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
                font-weight: 700;
                border: 1px solid rgba(107,62,31,0.18);
            }

            .report-metric__badge--primary { background: #f5ede6; color: var(--brand-choco); }
            .report-metric__badge--secondary { background: #f7efe8; color: #785236; }
            .report-metric__badge--tertiary { background: #f4ebdf; color: #5c4030; }
            .report-metric__badge--quaternary { background: #f2e8dd; color: #3d2b1f; }

            .report-metric__chip {
                border-radius: 999px;
                background: #f4ebdf;
                color: var(--brand-choco);
                font-size: 0.68rem;
                font-weight: 700;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                padding: 6px 10px;
            }

            .report-metric__label {
                display: block;
                font-size: 0.68rem;
                font-weight: 700;
                letter-spacing: 0.12em;
                text-transform: uppercase;
                color: var(--brand-muted);
                margin-top: 6px;
            }

            .report-metric__value {
                font-size: clamp(1.5rem, 2vw, 2.5rem);
                line-height: 1.05;
                letter-spacing: -.05em;
                color: var(--brand-ink);
                font-weight: 800;
                margin-top: 8px;
            }

            .report-grid {
                display: grid;
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
                gap: 24px;
                align-items: stretch;
                width: 100%;
            }

            .report-grid > .col-lg-5,
            .report-grid > .col-lg-7 {
                min-width: 0;
                width: 100%;
            }

            .report-panel {
                background: #ffffff;
                border: 1px solid var(--card-border);
                border-radius: 18px;
                overflow: hidden;
                box-shadow: var(--card-shadow);
                height: 100%;
            }

            .panel-header {
                background: #f4ebdf;
                border-bottom: 1px solid var(--card-border);
            }

            .panel-header h2 {
                font-size: 1.05rem;
                letter-spacing: -.02em;
                display: flex;
                align-items: center;
                gap: 10px;
                margin: 0;
            }

            .report-panel--side {
                background: #f9f5f0;
                border: 1px solid #dcc8b1;
                border-radius: 18px;
                overflow: hidden;
                box-shadow: var(--card-shadow);
                width: 100%;
                max-width: 100%;
                min-width: 0;
                margin: 0 auto;
            }

            .report-panel--side .panel-header {
                background: #efe1ce;
                padding: 14px 18px;
                border-bottom: 1px solid #d8c4a5;
            }

            .report-panel--side .panel-header h2 {
                justify-content: flex-start;
                font-size: 1.05rem;
                line-height: 1.1;
                letter-spacing: .02em;
                text-align: left;
                color: var(--brand-choco);
                display: flex;
                align-items: center;
                gap: 10px;
                margin: 0;
                font-weight: 800;
                text-transform: none;
                white-space: nowrap;
            }

            .report-table-wrap {
                overflow-x: auto;
            }

            .saas-table th {
                padding-top: 16px;
                padding-bottom: 16px;
                background: #efe2d2;
            }

            .report-stock-list {
                padding: 14px;
                background: #f8f3ee;
                max-height: 320px;
                overflow-y: auto;
            }

            .report-stock-item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 14px;
                padding: 16px 14px;
                border: 1px solid #e1d2bb;
                border-radius: 12px;
                background: rgba(255,255,255,0.5);
                min-width: 0;
                width: 100%;
            }

            .report-stock-item:last-child {
                border-bottom: none;
            }

            .report-stock-item__name {
                flex: 1 1 auto;
                min-width: 0;
                max-width: 100%;
                font-weight: 800;
                color: var(--brand-ink);
                font-size: clamp(1.05rem, 1.4vw, 1.45rem);
                line-height: 1.2;
                letter-spacing: -.02em;
                word-break: normal;
                overflow-wrap: normal;
                white-space: normal;
            }

            .report-stock-item .chip {
                flex-shrink: 0;
                white-space: nowrap;
                padding: 8px 12px;
                border-radius: 999px;
                font-size: .75rem;
                font-weight: 700;
                min-width: 96px;
                text-align: center;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: #f4efe9;
                border: 1px solid #d9c5a3;
                color: var(--brand-ink);
            }

            @media (max-width: 1200px) {
                .report-kpis {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media (max-width: 991.98px) {
                .report-filter .row,
                .report-grid {
                    display: flex;
                    flex-direction: column;
                    grid-template-columns: none;
                }

                .report-kpis {
                    grid-template-columns: 1fr;
                }

                .report-metric {
                    min-height: 120px;
                }

                .report-stock-item {
                    flex-direction: row;
                    align-items: center;
                    width: 100%;
                }

                .report-stock-item .chip {
                    min-width: 0;
                }

                .report-btn {
                    width: 100%;
                }
            }
        </style>
    @endpush
@endsection

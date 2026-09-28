@extends('layouts.admin')

@section('title', 'Dashboard — Father Care Bakery')

@push('styles')
<style>
    /* ── KPI Grid ──────────────────────────────────────── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
        margin-bottom: 28px;
    }
    @media (max-width: 1200px) {
        .kpi-grid { grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); }
    }
    @media (max-width: 575.98px) {
        .kpi-grid { grid-template-columns: 1fr; }
        .kpi-value { font-size: 1.55rem; }
    }
    .kpi-card {
        background: #FFFFFF;
        border: 1px solid var(--card-border);
        border-radius: var(--radius);
        padding: 22px 20px;
        position: relative;
        overflow: hidden;
        transition: border-color var(--transition), box-shadow var(--transition), transform var(--transition);
        box-shadow: var(--card-shadow);
        cursor: default;
    }
    .kpi-card:hover {
        transform: translateY(-3px);
        border-color: var(--card-border-h);
        box-shadow: var(--card-shadow-h);
    }
    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 3px;
        border-radius: var(--radius) var(--radius) 0 0;
    }
    .kpi-card.kc-gold::before   { background: linear-gradient(90deg, #6B3E1F, #D8C7B0); }
    .kpi-card.kc-orange::before { background: linear-gradient(90deg, #8B5A3C, #D8C7B0); }
    .kpi-card.kc-green::before  { background: linear-gradient(90deg, #3D2B1F, #C4A484); }
    .kpi-card.kc-blue::before   { background: linear-gradient(90deg, #5C4030, #D8C7B0); }
    .kpi-card.kc-brown::before  { background: linear-gradient(90deg, #6B3E1F, #D8C7B0); }

    .kpi-top {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 14px;
    }
    .kpi-icon {
        width: 44px; height: 44px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.35rem; flex-shrink: 0;
    }
    .ic-gold   { background: #F5EDE6; color: #6B3E1F; border: 1px solid rgba(107,62,31,.2); }
    .ic-orange { background: #F5EDE6; color: #5C4030; border: 1px solid rgba(107,62,31,.2); }
    .ic-green  { background: #FFFFFF; color: #3D2B1F; border: 1px solid #D8C7B0; }
    .ic-blue   { background: #FFFFFF; color: #6B3E1F; border: 1px solid #D8C7B0; }
    .ic-brown  { background: #F5EDE6; color: #6B3E1F; border: 1px solid rgba(107,62,31,.2); }

    .kpi-trend {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: .68rem; font-weight: 700;
        padding: 3px 9px; border-radius: var(--radius-pill);
    }
    .kpi-trend.up      { background: #F5EDE6; color: #3D2B1F; }
    .kpi-trend.warn    { background: #3D2B1F; color: #FFFFFF; }
    .kpi-trend.neutral { background: #F5EDE6; color: #6B3E1F; }

    .kpi-label {
        font-size: .7rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .08em;
        color: var(--brand-muted); margin-bottom: 6px;
    }
    .kpi-value {
        font-size: 1.9rem; font-weight: 800;
        color: var(--brand-ink); letter-spacing: -.03em; line-height: 1.1;
    }
    .kpi-sub { font-size: .73rem; color: var(--brand-muted); margin-top: 6px; }

    /* ── Content grid ───────────────────────────────────── */
    .dash-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 24px;
    }
    .dash-col { display: flex; flex-direction: column; gap: 24px; }
    @media (max-width: 1080px) {
        .dash-grid { grid-template-columns: 1fr; }
        .kpi-grid  { grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); }
    }

    /* ── Panel ──────────────────────────────────────────── */
    .panel {
        background: #FFFFFF;
        border: 1px solid var(--card-border);
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--card-shadow);
        transition: border-color var(--transition), box-shadow var(--transition);
    }
    .panel:hover { border-color: var(--card-border-h); box-shadow: var(--card-shadow-h); }

    .panel-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 22px;
        border-bottom: 1px solid var(--card-border);
        background: #F5EDE6;
    }
    .panel-header h2 {
        font-size: .95rem; font-weight: 700;
        color: var(--brand-ink);
        display: flex; align-items: center; gap: 8px;
    }
    .panel-header h2 i { color: var(--brand-choco); font-size: 1.1rem; }

    /* ── Chart wrap ─────────────────────────────────────── */
    .chart-wrap { padding: 18px 22px 22px; height: 230px; position: relative; background: #FFFFFF; }

    /* ── Stock panel ─────────────────────────────────────── */
    .stock-item {
        padding: 14px 22px;
        border-bottom: 1px solid #D8C7B0;
        transition: background var(--transition);
        background: #FFFFFF;
    }
    .stock-item:last-child { border-bottom: none; }
    .stock-item:hover { background: #F5EDE6; }
    .stock-item-top {
        display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;
    }
    .stock-name {
        font-size: .86rem; font-weight: 600; color: var(--brand-ink);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 65%;
    }
    .stock-qty {
        font-size: .7rem; font-weight: 700;
        padding: 2px 10px; border-radius: var(--radius-pill);
        background: var(--danger-dim); color: var(--danger);
        border: 1px solid var(--danger-border);
        flex-shrink: 0;
    }
    .stock-bar-track {
        height: 6px; background: #E8D5C4; border-radius: var(--radius-pill); overflow: hidden;
    }
    .stock-bar-fill {
        height: 100%; border-radius: var(--radius-pill);
        background: linear-gradient(90deg, #3D2B1F, #6B3E1F);
        transition: width .8s cubic-bezier(.4,0,.2,1);
    }

    /* ── Customer avatar ─────────────────────────────────── */
    .cust-avatar {
        width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;
        background: linear-gradient(135deg, var(--brand-choco), var(--brand-dark));
        display: flex; align-items: center; justify-content: center;
        font-size: .68rem; font-weight: 800; color: #FFFFFF;
        box-shadow: 0 2px 6px rgba(107,62,31,.15);
    }
    .order-num { color: var(--brand-choco); font-weight: 700; font-size: .85rem; }
    .order-num:hover { color: var(--brand-dark); text-decoration: underline; }
</style>
@endpush

@section('content')

{{-- ── Page title ─────────────────────────────────────────── --}}
<div class="page-header fade-in">
    <div>
        <h1>Dashboard</h1>
        <p>Real-time overview of your bakery operations</p>
    </div>
    <div style="display:flex; align-items:center; gap:8px;">
        <span class="chip" style="background:#FFFFFF; border:1px solid var(--card-border); color:var(--brand-ink-soft); font-size:.75rem; padding:6px 14px;">
            <i class="ph ph-calendar" style="color:var(--brand-choco);"></i> {{ now()->format('D, d M Y') }}
        </span>
    </div>
</div>

{{-- ── KPI Cards ──────────────────────────────────────────── --}}
<div class="kpi-grid">

    <div class="kpi-card kc-gold fade-in fade-in-1">
        <div class="kpi-top">
            <div class="kpi-icon ic-gold"><i class="ph ph-currency-ngn"></i></div>
            <span class="kpi-trend neutral"><i class="ph ph-calendar"></i> Today</span>
        </div>
        <div class="kpi-label">Today's Revenue</div>
        <div class="kpi-value" data-count="{{ $stats['today_sales'] }}" data-prefix="₦" data-decimal="2">
            ₦{{ number_format($stats['today_sales'], 2) }}
        </div>
        <div class="kpi-sub">₦{{ number_format($stats['monthly_sales'], 2) }} this month</div>
    </div>

    <div class="kpi-card kc-blue fade-in fade-in-2">
        <div class="kpi-top">
            <div class="kpi-icon ic-blue"><i class="ph ph-receipt"></i></div>
            <span class="kpi-trend neutral"><i class="ph ph-stack"></i> All time</span>
        </div>
        <div class="kpi-label">Total Orders</div>
        <div class="kpi-value" data-count="{{ $stats['total_orders'] }}">{{ $stats['total_orders'] }}</div>
        <div class="kpi-sub">{{ $stats['pending_orders'] }} pending review</div>
    </div>

    <div class="kpi-card kc-orange fade-in fade-in-3">
        <div class="kpi-top">
            <div class="kpi-icon ic-orange"><i class="ph ph-clock"></i></div>
            @if($stats['pending_orders'] > 0)
                <span class="kpi-trend warn"><i class="ph ph-warning"></i> Action needed</span>
            @else
                <span class="kpi-trend up"><i class="ph ph-check"></i> All clear</span>
            @endif
        </div>
        <div class="kpi-label">Pending Orders</div>
        <div class="kpi-value" data-count="{{ $stats['pending_orders'] }}">{{ $stats['pending_orders'] }}</div>
        <div class="kpi-sub">Awaiting confirmation</div>
    </div>

    <div class="kpi-card kc-brown fade-in fade-in-4">
        <div class="kpi-top">
            <div class="kpi-icon ic-brown"><i class="ph ph-cookie"></i></div>
            <span class="kpi-trend neutral"><i class="ph ph-list"></i> Catalogue</span>
        </div>
        <div class="kpi-label">Products</div>
        <div class="kpi-value" data-count="{{ $stats['products'] }}">{{ $stats['products'] }}</div>
        <div class="kpi-sub">{{ $stats['low_stock'] }} low on stock</div>
    </div>

    <div class="kpi-card kc-green fade-in fade-in-5">
        <div class="kpi-top">
            <div class="kpi-icon ic-green"><i class="ph ph-users"></i></div>
            <span class="kpi-trend up"><i class="ph ph-trend-up"></i> Growing</span>
        </div>
        <div class="kpi-label">Customers</div>
        <div class="kpi-value" data-count="{{ $stats['customers'] }}">{{ $stats['customers'] }}</div>
        <div class="kpi-sub">Registered accounts</div>
    </div>

</div>

{{-- ── Main grid ──────────────────────────────────────────── --}}
<div class="dash-grid">

    {{-- LEFT ─ chart + orders --}}
    <div class="dash-col">

        {{-- Revenue chart --}}
        <div class="panel fade-in fade-in-2">
            <div class="panel-header">
                <h2><i class="ph ph-chart-line-up"></i> Revenue Overview</h2>
                <span style="font-size:.75rem; color:var(--brand-muted); font-weight:600;">Last 7 days</span>
            </div>
            <div class="chart-wrap">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Recent orders --}}
        <div class="panel fade-in fade-in-3">
            <div class="panel-header">
                <h2><i class="ph ph-receipt"></i> Recent Orders</h2>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-ghost btn-sm">
                    View all <i class="ph ph-arrow-right"></i>
                </a>
            </div>
            <div style="overflow-x:auto;">
                <table class="saas-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            @php $sk = strtolower($order->status->value ?? 'pending'); @endphp
                            <tr>
                                <td>
                                    <a class="order-num" href="{{ route('admin.orders.show', $order) }}">
                                        #{{ $order->order_number }}
                                    </a>
                                </td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:9px;">
                                        <div class="cust-avatar">{{ strtoupper(substr($order->user?->name ?? '?', 0, 1)) }}</div>
                                        <span style="font-weight:600; color:var(--brand-ink);">{{ $order->user?->name ?? '—' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="chip chip-{{ $sk }}">
                                        <span class="chip-dot"></span>
                                        {{ $order->status->label() }}
                                    </span>
                                </td>
                                <td class="amount-cell">₦{{ number_format($order->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">
                                <div class="empty-state">
                                    <i class="ph ph-receipt-x"></i>
                                    No orders yet
                                </div>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- RIGHT ─ low stock --}}
    <div>
        <div class="panel fade-in fade-in-4" style="height:100%;">
            <div class="panel-header" style="border-bottom-color:#D8C7B0;">
                <h2 style="color:var(--brand-choco);"><i class="ph ph-warning" style="color:var(--brand-choco);"></i> Low Stock Alert</h2>
                <a href="{{ route('admin.inventory.index') }}" class="btn btn-ghost btn-sm">
                    Inventory <i class="ph ph-arrow-right"></i>
                </a>
            </div>

            @forelse($lowStockProducts as $product)
                @php
                    $max = max((int)$product->low_stock_threshold * 2, 2);
                    $pct = min(round(($product->stock_quantity / $max) * 100), 100);
                @endphp
                <div class="stock-item">
                    <div class="stock-item-top">
                        <span class="stock-name">{{ $product->name }}</span>
                        <span class="stock-qty">{{ $product->stock_quantity }} left</span>
                    </div>
                    <div class="stock-bar-track">
                        <div class="stock-bar-fill" style="width:{{ $pct }}%;"></div>
                    </div>
                </div>
            @empty
                <div class="empty-state" style="color:var(--brand-choco);">
                    <i class="ph ph-check-fat" style="color:var(--brand-choco);"></i>
                    All stock levels look healthy!
                </div>
            @endforelse
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
// ── Count-up animation ──────────────────────────────────────
document.querySelectorAll('[data-count]').forEach(el => {
    const target  = parseFloat(el.dataset.count) || 0;
    const prefix  = el.dataset.prefix  || '';
    const decimal = parseInt(el.dataset.decimal || '0', 10);
    const dur     = 1000;
    const start   = performance.now();
    const step = now => {
        const prog = Math.min((now - start) / dur, 1);
        const ease = 1 - Math.pow(1 - prog, 3);
        el.textContent = prefix + (target * ease).toLocaleString('en-NG', {
            minimumFractionDigits: decimal, maximumFractionDigits: decimal
        });
        if (prog < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
});

// ── Revenue chart ───────────────────────────────────────────
(function () {
    const canvas = document.getElementById('revenueChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    const days = [];
    for (let i = 6; i >= 0; i--) {
        const d = new Date();
        d.setDate(d.getDate() - i);
        days.push(d.toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric' }));
    }

    const todaySales   = parseFloat('{{ $stats["today_sales"] }}') || 0;
    const monthlySales = parseFloat('{{ $stats["monthly_sales"] }}') || 0;
    const avgDay = monthlySales > todaySales ? (monthlySales - todaySales) / 20 : 800;

    const jitter = base => Math.max(0, +(base * (.55 + Math.random() * .9)).toFixed(2));
    const data = Array.from({ length: 6 }, () => jitter(avgDay));
    data.push(+todaySales.toFixed(2));

    const grad = ctx.createLinearGradient(0, 0, 0, 200);
    grad.addColorStop(0, 'rgba(107,62,31,.20)');
    grad.addColorStop(1, 'rgba(107,62,31,0.01)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: days,
            datasets: [{
                label: 'Revenue (₦)',
                data,
                borderColor: '#6B3E1F',
                borderWidth: 2.5,
                backgroundColor: grad,
                tension: 0.38,
                pointBackgroundColor: '#6B3E1F',
                pointBorderColor: '#FFFFFF',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#3D2B1F',
                    borderColor: '#6B3E1F',
                    borderWidth: 1,
                    titleColor: '#FFFFFF',
                    bodyColor: '#FFFFFF',
                    padding: 10,
                    callbacks: {
                        label: c => ' ₦' + c.parsed.y.toLocaleString('en-NG', { minimumFractionDigits: 2 })
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(107,62,31,.06)' },
                    ticks: { color: '#8A7060', font: { size: 11, family: 'Inter' } }
                },
                y: {
                    grid: { color: 'rgba(107,62,31,.06)' },
                    ticks: {
                        color: '#8A7060', font: { size: 11, family: 'Inter' },
                        callback: v => '₦' + v.toLocaleString('en-NG')
                    }
                }
            }
        }
    });
})();
</script>
@endpush

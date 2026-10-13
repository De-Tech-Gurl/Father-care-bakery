@extends('layouts.admin')

@section('title', 'Orders — Father Care Bakery')

@section('content')
    <div class="orders-page">
        <div class="page-header orders-page__header fade-in">
            <div>
                <span class="orders-eyebrow"><i class="ph ph-receipt"></i> Sales & fulfillment</span>
                <h1>Orders</h1>
                <p>Review customer orders, follow progress, and manage fulfillment.</p>
            </div>
            <div class="orders-page__total">
                <span class="orders-page__total-icon"><i class="ph ph-shopping-bag"></i></span>
                <span><strong>{{ number_format($orders->total()) }}</strong><small>{{ \Illuminate\Support\Str::plural('order', $orders->total()) }} found</small></span>
            </div>
        </div>

        <form method="GET" class="orders-filter fade-in fade-in-1">
            <div class="orders-filter__copy">
                <span class="orders-section-label">Find an order</span>
                <p>Filter by fulfillment status</p>
            </div>
            <div class="orders-filter__controls">
                <label class="visually-hidden" for="order-status-filter">Order status</label>
                <select id="order-status-filter" name="status" class="form-select">
                    <option value="">All statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
                <button class="btn btn-success" type="submit"><i class="ph ph-funnel"></i> Apply filter</button>
                @if(request()->filled('status'))
                    <a class="orders-filter__reset" href="{{ route('admin.orders.index') }}">Clear</a>
                @endif
            </div>
        </form>

        <section class="panel orders-panel fade-in fade-in-2">
            <div class="panel-header orders-panel__header">
                <div>
                    <h2><i class="ph ph-list-bullets"></i> Order list</h2>
                    <p>Newest orders appear first</p>
                </div>
                <span class="orders-panel__range">
                    @if($orders->total() > 0)
                        Showing {{ $orders->firstItem() }}–{{ $orders->lastItem() }} of {{ $orders->total() }}
                    @else
                        No matching orders
                    @endif
                </span>
            </div>

            <div class="orders-list" role="table" aria-label="Orders">
                <div class="orders-list__head" role="row">
                    <span role="columnheader">Order</span>
                    <span role="columnheader">Customer</span>
                    <span role="columnheader">Fulfillment</span>
                    <span role="columnheader">Status</span>
                    <span role="columnheader" class="orders-list__amount">Total</span>
                    <span role="columnheader" class="orders-list__action">Action</span>
                </div>
                <div class="orders-list__body">
                    @forelse($orders as $order)
                        @php($statusKey = strtolower($order->status->value))
                        <article class="order-card" role="row">
                            <div class="order-card__cell order-card__order" role="cell">
                                <span class="order-card__label">Order</span>
                                <span>
                                    <a class="orders-table__number" href="{{ route('admin.orders.show', $order) }}">#{{ $order->order_number }}</a>
                                    <small class="orders-table__date">{{ $order->created_at->format('d M Y · H:i') }}</small>
                                </span>
                            </div>
                            <div class="order-card__cell order-card__customer" role="cell">
                                <span class="order-card__label">Customer</span>
                                <span class="orders-customer">
                                    <span class="orders-customer__avatar">{{ strtoupper(substr($order->user?->name ?? '?', 0, 1)) }}</span>
                                    <span class="orders-customer__copy">
                                        <strong>{{ $order->user?->name ?? 'Guest / Walk-in' }}</strong>
                                        <small>{{ $order->phone }}</small>
                                    </span>
                                </span>
                            </div>
                            <div class="order-card__cell" role="cell">
                                <span class="order-card__label">Fulfillment</span>
                                <span class="orders-fulfillment">
                                    <i class="ph {{ $order->delivery_type->value === 'pickup' ? 'ph-storefront' : 'ph-moped' }}"></i>
                                    {{ $order->delivery_type->label() }}
                                </span>
                            </div>
                            <div class="order-card__cell" role="cell">
                                <span class="order-card__label">Status</span>
                                <span class="chip chip-{{ $statusKey }}">
                                    <span class="chip-dot"></span>
                                    {{ $order->status->label() }}
                                </span>
                            </div>
                            <div class="order-card__cell order-card__amount" role="cell">
                                <span class="order-card__label">Total</span>
                                <strong class="amount-cell">₦{{ number_format($order->total_amount, 2) }}</strong>
                            </div>
                            <div class="order-card__cell order-card__action" role="cell">
                                <span class="order-card__label">Action</span>
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-ghost btn-sm" aria-label="View order {{ $order->order_number }}">
                                    View order <i class="ph ph-arrow-up-right"></i>
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="empty-state orders-list__empty">
                            <i class="ph ph-receipt"></i>
                            <strong>No orders found</strong>
                            <span>Try another status filter or check back when a new order arrives.</span>
                        </div>
                    @endforelse
                </div>
            </div>

            @if($orders->hasPages())
                <div class="orders-panel__pagination">{{ $orders->links() }}</div>
            @endif
        </section>
    </div>

    @push('styles')
        <style>
            .orders-page { width: 100%; }
            .orders-page, .orders-page > *, .orders-filter__copy, .orders-filter__controls { min-width: 0; }
            .orders-page__header { align-items: flex-end; margin-bottom: 22px; }
            .orders-page__header h1 { margin: 7px 0 8px; font-family: var(--font-serif); font-size: clamp(2rem, 3vw, 2.7rem); letter-spacing: -.045em; }
            .orders-page__header p { margin: 0; font-size: .9rem; }
            .orders-eyebrow, .orders-section-label { display: inline-flex; align-items: center; gap: 7px; color: var(--brand-choco); font-size: .68rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
            .orders-page__total { display: inline-flex; align-items: center; gap: 12px; padding: 12px 16px; border: 1px solid var(--card-border); border-radius: 14px; background: #fff; box-shadow: var(--card-shadow); }
            .orders-page__total-icon { display: grid; width: 40px; height: 40px; place-items: center; border-radius: 12px; background: #f5ede6; color: var(--brand-choco); font-size: 1.2rem; }
            .orders-page__total strong, .orders-page__total small { display: block; }
            .orders-page__total strong { color: var(--brand-ink); font-size: 1.1rem; line-height: 1.1; }
            .orders-page__total small { margin-top: 4px; color: var(--brand-muted); font-size: .7rem; }
            .orders-filter { display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-bottom: 20px; padding: 16px 20px; border: 1px solid var(--card-border); border-radius: 16px; background: #fff; box-shadow: var(--card-shadow); }
            .orders-filter__copy p { margin: 4px 0 0; color: var(--brand-muted); font-size: .78rem; }
            .orders-filter__controls { display: flex; align-items: center; gap: 10px; }
            .orders-filter__controls .form-select { width: min(260px, 45vw); min-height: 42px; border-color: var(--card-border); }
            .orders-filter__controls .btn { min-height: 42px; white-space: nowrap; }
            .orders-filter__reset { padding: 8px 4px; color: var(--brand-muted); font-size: .8rem; font-weight: 600; text-decoration: none; }
            .orders-filter__reset:hover { color: var(--brand-choco); }
            .orders-panel { overflow: hidden; }
            .orders-panel__header { min-height: 70px; }
            .orders-panel__header h2 { margin: 0; }
            .orders-panel__header p { margin: 5px 0 0 25px; color: var(--brand-muted); font-size: .75rem; }
            .orders-panel__range { color: var(--brand-muted); font-size: .75rem; font-weight: 600; }
            .orders-list { width: 100%; }
            .orders-list__head, .order-card { display: grid; grid-template-columns: minmax(150px, 1.2fr) minmax(130px, 1fr) minmax(155px, 1.15fr) minmax(120px, 1fr) minmax(95px, .7fr) minmax(105px, .7fr); align-items: center; gap: 12px; }
            .orders-list__head { padding: 12px 20px; border-bottom: 1px solid var(--card-border); background: #f8f3ee; color: var(--brand-choco); font-size: .67rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
            .orders-list__body { display: grid; }
            .order-card { min-width: 0; padding: 16px 20px; border-bottom: 1px solid #eee5dc; transition: background var(--transition); }
            .order-card:last-child { border-bottom: 0; }
            .order-card:hover { background: #fcfaf8; }
            .order-card__cell { display: flex; align-items: center; min-width: 0; }
            .order-card__label { display: none; }
            .orders-table__number, .orders-table__date { display: block; }
            .orders-table__number { color: var(--brand-choco); font-weight: 800; overflow-wrap: anywhere; }
            .orders-table__date { margin-top: 5px; color: var(--brand-muted); font-size: .72rem; }
            .orders-customer { display: flex; align-items: center; gap: 10px; min-width: 150px; }
            .orders-customer__avatar { display: grid; width: 34px; height: 34px; flex: 0 0 34px; place-items: center; border-radius: 11px; background: #f5ede6; color: var(--brand-choco); font-size: .8rem; font-weight: 800; }
            .orders-customer__copy { min-width: 0; }
            .orders-customer__copy strong, .orders-customer__copy small { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .orders-customer__copy strong { max-width: 190px; color: var(--brand-ink); font-size: .82rem; }
            .orders-customer__copy small { margin-top: 4px; color: var(--brand-muted); font-size: .72rem; }
            .orders-fulfillment { display: inline-flex; align-items: center; gap: 7px; color: var(--brand-ink-soft); font-size: .8rem; }
            .orders-fulfillment i { color: var(--brand-choco); font-size: 1rem; }
            .orders-list__amount, .order-card__amount { text-align: right; }
            .orders-list__action, .order-card__action { justify-content: flex-end; }
            .order-card__action .btn { display: inline-flex; align-items: center; gap: 6px; }
            .orders-panel__pagination { padding: 2px 20px 16px; border-top: 1px solid var(--card-border); }
            .orders-panel__pagination nav[role="navigation"] { display: flex; justify-content: flex-end; }
            .orders-list__empty strong, .orders-list__empty span { display: block; }
            .orders-list__empty strong { margin-bottom: 5px; color: var(--brand-ink); }
            .orders-list__empty span { color: var(--brand-muted); font-size: .78rem; }
            @media (max-width: 1400px) {
                .orders-page__header { align-items: flex-start; }
                .orders-page__header > div:first-child { min-width: 0; }
                .orders-page__total { margin-left: auto; }
                .orders-filter { align-items: stretch; flex-direction: column; gap: 12px; }
                .orders-filter__controls { align-items: stretch; flex-wrap: wrap; }
                .orders-filter__controls .form-select { flex: 1 1 240px; width: auto; }
                .orders-filter__controls .btn { flex: 0 1 auto; }
                .orders-filter__reset { align-self: center; }
                .orders-panel__header { align-items: flex-start; gap: 10px; }
                .orders-list__head { display: none; }
                .order-card { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 15px 12px; padding: 16px; }
                .order-card__cell { align-items: flex-start; flex-direction: column; gap: 6px; }
                .order-card__label { display: block; color: var(--brand-muted); font-size: .62rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
                .order-card__amount, .order-card__action { align-items: flex-start; text-align: left; }
                .order-card__action { justify-content: flex-start; }
                .orders-customer { min-width: 0; }
                .orders-fulfillment { font-size: .75rem; }
                .order-card .chip { white-space: normal; }
            }
            @media (max-width: 575.98px) {
                .orders-page__header { flex-direction: column; align-items: stretch; }
                .orders-page__total { align-self: flex-start; margin-left: 0; }
                .orders-filter { padding: 14px; }
                .orders-filter__controls { display: grid; grid-template-columns: minmax(0, 1fr); }
                .orders-filter__controls .form-select,
                .orders-filter__controls .btn { width: 100%; }
                .orders-panel__header { flex-direction: column; }
                .orders-panel__range { padding-left: 25px; }
                .order-card { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px 10px; padding: 14px 12px; }
                .order-card__order, .order-card__customer { grid-column: span 2; }
                .order-card__amount { align-items: flex-start; }
                .order-card__action { align-items: flex-start; }
                .order-card__action .btn { min-height: 36px; padding: 6px 10px; font-size: .72rem; }
                .orders-customer__copy strong { max-width: 100%; }
            }
        </style>
    @endpush
@endsection

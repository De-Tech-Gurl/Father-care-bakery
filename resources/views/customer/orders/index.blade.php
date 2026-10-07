@extends('layouts.customer')

@section('title', 'My Orders - Father Care Bakery')

@section('content')
    <div class="orders-page-shell">
        <div class="container py-5">
            <div class="orders-header mb-4">
                <div>
                    <div class="section-eyebrow"><i class="ph ph-receipt"></i> Customer space</div>
                    <h1 class="orders-title">Your orders, at a glance.</h1>
                    <p class="orders-intro">Track every bake from confirmation to collection.</p>
                </div>
                <a href="{{ route('customer.products.index') }}" class="btn-sage orders-shop-link"><i class="ph ph-plus"></i> Start a new order</a>
            </div>

            <div class="orders-summary-row mb-4">
                <div class="orders-summary-card summary-primary"><span class="summary-icon"><i class="ph ph-package"></i></span><span><span class="summary-label">All orders</span><strong>{{ $orders->total() }}</strong></span></div>
                <div class="orders-summary-card"><span class="summary-icon summary-icon-warm"><i class="ph ph-timer"></i></span><span><span class="summary-label">In progress</span><strong>{{ $inProgressOrderCount }}</strong></span></div>
                <div class="orders-summary-card"><span class="summary-icon summary-icon-green"><i class="ph ph-chart-line-up"></i></span><span><span class="summary-label">This page total</span><strong>₦{{ number_format($orders->sum('total_amount'), 2) }}</strong></span></div>
            </div>

            <div class="orders-toolbar mb-4">
                <div class="orders-search"><i class="ph ph-magnifying-glass"></i><input id="orderSearch" type="search" placeholder="Search order number or delivery type" aria-label="Search orders"></div>
                <label class="orders-sort"><span>Sort</span><select id="orderSort" aria-label="Sort orders"><option value="newest">Newest first</option><option value="oldest">Oldest first</option><option value="highest">Highest total</option></select></label>
            </div>
            <div class="order-filters mb-3" role="tablist" aria-label="Filter orders">
                <button class="order-filter is-active" type="button" data-filter="all">All <span>{{ $orders->total() }}</span></button>
                <button class="order-filter" type="button" data-filter="open">In progress</button>
                <button class="order-filter" type="button" data-filter="completed">Completed</button>
                <button class="order-filter" type="button" data-filter="cancelled">Cancelled</button>
            </div>

            <div id="ordersList">
            @forelse($orders as $order)
                @php($statusValue = $order->status?->value ?? 'pending')
                <article class="order-card mb-3" data-order-card data-status="{{ $statusValue }}" data-open="{{ $order->status?->isOpen() ? 'true' : 'false' }}" data-search="{{ strtolower($order->order_number . ' ' . $order->delivery_type->label() . ' ' . $statusValue) }}" data-total="{{ $order->total_amount }}" data-date="{{ $order->created_at->timestamp }}">
                    <div class="order-card-head">
                        <div>
                            <div class="order-number"><span class="order-number-mark"><i class="ph ph-package"></i></span>{{ $order->order_number }}</div>
                            <div class="order-date">{{ $order->created_at->format('d M Y, h:i A') }}</div>
                        </div>
                        <span class="order-status status-{{ strtolower(str_replace('_', '-', $statusValue)) }}">
                            {{ $order->status->label() }}
                        </span>
                    </div>

                    <div class="order-card-body">
                        <div class="order-item-summary">
                            <span class="items-count">{{ $order->items->count() }} {{ \Illuminate\Support\Str::plural('item', $order->items->count()) }}</span>
                            <span class="order-meta"><i class="ph ph-map-pin"></i> {{ $order->delivery_type->label() }}</span>
                        </div>
                        <div class="order-price-block">
                            <span class="order-total-label">Total</span>
                            <strong>₦{{ number_format($order->total_amount, 2) }}</strong>
                        </div>
                    </div>

                    <div class="order-card-footer">
                        <button class="order-preview-trigger" type="button" data-preview-trigger aria-expanded="false"><span>Quick view</span><i class="ph ph-caret-down"></i></button>
                        <a href="{{ route('customer.orders.show', $order) }}" class="order-details-link">View full details <i class="ph ph-arrow-up-right"></i></a>
                    </div>
                    <div class="order-preview" data-preview>
                        <div><span>Payment</span><strong>{{ $order->payment_status?->label() ?? 'Pending' }}</strong></div>
                        <div><span>Delivery</span><strong>{{ $order->delivery_type->label() }}</strong></div>
                        <div><span>Items</span><strong>{{ $order->items->pluck('product.name')->filter()->join(', ') ?: 'Bakery order' }}</strong></div>
                    </div>
                </article>
            @empty
                <div class="empty-order-state">
                    <div class="empty-order-icon">
                        <i class="ph ph-bag"></i>
                    </div>
                    <h2>No orders yet</h2>
                    <p>You have not placed any orders yet. Start with our fresh bakery favourites.</p>
                    <a href="{{ route('customer.products.index') }}" class="btn-sage">
                        <i class="ph ph-shopping-bag"></i>
                        Browse products
                    </a>
                </div>
            @endforelse
            </div>
            <div id="ordersNoResults" class="orders-no-results" hidden><i class="ph ph-magnifying-glass"></i><h2>No matching orders</h2><p>Try another search or reset the filter to see your orders.</p></div>

            @if($orders->hasPages())
                <div class="mt-4 pagination-wrap">{{ $orders->links() }}</div>
            @endif
        </div>
    </div>

    @push('styles')
    <style>
        .orders-page-shell {
            background: linear-gradient(180deg, #fff 0%, #f9f4ef 100%);
            min-height: calc(100vh - 120px);
        }

        .orders-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .orders-title {
            margin: 0.5rem 0 0;
            font-size: clamp(2.2rem, 4vw, 3.2rem);
            letter-spacing: -0.04em;
            color: var(--ink);
        }

        .orders-intro { color: var(--muted); margin: .65rem 0 0; max-width: 32rem; }
        .orders-shop-link { white-space: nowrap; }

        .orders-summary-row {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }

        .orders-summary-card {
            display: flex;
            align-items: center;
            gap: .85rem;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 1.1rem 1.2rem;
            box-shadow: var(--shadow-xs);
        }

        .summary-icon { width: 2.7rem; height: 2.7rem; display: grid; place-items: center; border-radius: 14px; background: var(--sage-xlight); color: var(--sage); font-size: 1.2rem; flex: 0 0 auto; }
        .summary-icon-warm { background: #fff0dc; color: #a66728; }
        .summary-icon-green { background: #e8f2e9; color: #39704b; }
        .orders-toolbar { display: flex; align-items: center; gap: .8rem; justify-content: space-between; }
        .orders-search { display: flex; align-items: center; gap: .65rem; background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 0 .95rem; max-width: 31rem; flex: 1; color: var(--muted); }
        .orders-search input { border: 0; outline: 0; width: 100%; padding: .82rem 0; color: var(--ink); background: transparent; }
        .orders-sort { display: flex; align-items: center; gap: .55rem; color: var(--muted); font-size: .8rem; font-weight: 700; }
        .orders-sort select { border: 1px solid var(--border); background: #fff; border-radius: 12px; color: var(--ink); padding: .78rem 2rem .78rem .8rem; }
        .order-filters { display: flex; gap: .5rem; flex-wrap: wrap; }
        .order-filter { border: 1px solid transparent; border-radius: 999px; padding: .55rem .85rem; background: rgba(255,255,255,.65); color: var(--muted); font-weight: 700; font-size: .8rem; }
        .order-filter span { opacity: .65; margin-left: .2rem; }
        .order-filter:hover, .order-filter.is-active { background: var(--sage); color: #fff; }

        .summary-label {
            display: block;
            font-size: 0.76rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 0.45rem;
        }

        .orders-summary-card strong {
            font-size: clamp(1.2rem, 2vw, 1.8rem);
            color: var(--ink);
        }

        .order-card {
            display: block;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 1.3rem 1.4rem;
            box-shadow: 0 8px 20px rgba(61,43,31,0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(61,43,31,0.06);
            border-color: rgba(107,62,31,0.25);
        }

        .order-card-head,
        .order-card-body,
        .order-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .order-number {
            font-size: 1.08rem;
            font-weight: 700;
            color: var(--ink);
        }

        .order-number-mark { display: inline-grid; place-items: center; width: 2rem; height: 2rem; border-radius: 10px; background: var(--sage-xlight); color: var(--sage); margin-right: .55rem; font-size: .95rem; }

        .order-date {
            color: var(--muted);
            font-size: 0.82rem;
            margin-top: 0.2rem;
        }

        .order-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 0.8rem;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            border: 1px solid transparent;
        }

        .status-pending,
        .status-processing,
        .status-confirmed {
            background: rgba(107,62,31,0.08);
            color: var(--sage-dark);
            border-color: rgba(107,62,31,0.14);
        }

        .status-ready,
        .status-out-for-delivery,
        .status-shipped {
            background: rgba(58, 97, 69, 0.08);
            color: #2d5d38;
            border-color: rgba(58, 97, 69, 0.14);
        }

        .status-delivered {
            background: rgba(38, 125, 82, 0.1);
            color: #186c47;
            border-color: rgba(38, 125, 82, 0.16);
        }

        .status-cancelled,
        .status-failed {
            background: rgba(167, 48, 48, 0.08);
            color: #8b2d2d;
            border-color: rgba(167,48,48,0.14);
        }

        .order-card-body {
            margin: 1rem 0 0.9rem;
            padding-top: 0.9rem;
            border-top: 1px solid rgba(216,199,176,0.9);
        }

        .order-item-summary {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
            color: var(--muted);
            font-size: 0.92rem;
        }

        .order-price-block {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .order-total-label {
            color: var(--muted);
            font-size: 0.7rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .order-price-block strong {
            font-size: clamp(1.2rem, 2vw, 1.7rem);
            color: var(--ink);
        }

        .order-card-footer {
            padding-top: 0.9rem;
            border-top: 1px solid rgba(216,199,176,0.9);
            color: var(--sage);
            font-weight: 600;
        }

        .order-preview-trigger, .order-details-link { color: var(--sage); font-weight: 700; border: 0; background: transparent; padding: 0; text-decoration: none; display: inline-flex; gap: .4rem; align-items: center; }
        .order-preview-trigger i { transition: transform .2s ease; }
        .order-preview-trigger[aria-expanded="true"] i { transform: rotate(180deg); }
        .order-details-link { color: var(--muted); font-size: .85rem; }
        .order-details-link:hover { color: var(--sage); }
        .order-preview { display: none; grid-template-columns: repeat(3, 1fr); gap: 1rem; padding: 1rem 0 .1rem; color: var(--muted); font-size: .78rem; }
        .order-preview.is-visible { display: grid; }
        .order-preview span, .order-preview strong { display: block; }
        .order-preview span { text-transform: uppercase; letter-spacing: .08em; font-size: .65rem; margin-bottom: .25rem; }
        .order-preview strong { color: var(--ink); font-size: .85rem; font-weight: 700; }
        .orders-no-results { text-align: center; padding: 3.5rem 1rem; color: var(--muted); }
        .orders-no-results i { font-size: 2rem; color: var(--sage); }
        .orders-no-results h2 { color: var(--ink); margin: .7rem 0 .35rem; }

        .empty-order-state {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 26px;
            padding: 3rem 1.5rem;
            text-align: center;
            box-shadow: var(--shadow-xs);
        }

        .empty-order-icon {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: grid;
            place-items: center;
            background: rgba(107,62,31,0.08);
            color: var(--sage);
            font-size: 2.2rem;
        }

        .empty-order-state h2 {
            margin: 0 0 0.6rem;
            font-size: clamp(2rem, 4vw, 2.5rem);
        }

        .empty-order-state p {
            max-width: 540px;
            margin: 0 auto 1.5rem;
            color: var(--muted);
        }

        .pagination-wrap .pagination {
            justify-content: center;
            gap: 0.35rem;
            margin: 0;
        }

        .pagination-wrap .page-link {
            border-radius: 10px;
            color: var(--ink);
            border: 1px solid var(--border);
            padding: 0.6rem 0.8rem;
        }

        .pagination-wrap .page-item.active .page-link {
            background: var(--sage);
            border-color: var(--sage);
            color: #fff;
        }

        @media (max-width: 767.98px) {
            .orders-summary-row {
                grid-template-columns: 1fr;
            }

            .order-card-head,
            .order-card-body,
            .order-card-footer {
                align-items: flex-start;
            }

            .order-price-block {
                align-items: flex-start;
            }

            .orders-toolbar { align-items: stretch; flex-direction: column; }
            .orders-search { max-width: none; }
            .orders-sort { justify-content: space-between; }
            .orders-sort select { flex: 1; }
            .order-preview { grid-template-columns: 1fr 1fr; }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        (() => {
            const cards = [...document.querySelectorAll('[data-order-card]')];
            const list = document.querySelector('#ordersList');
            const empty = document.querySelector('#ordersNoResults');
            const search = document.querySelector('#orderSearch');
            const sort = document.querySelector('#orderSort');
            let filter = 'all';

            const render = () => {
                const query = search.value.trim().toLowerCase();
                const visible = cards.filter((card) => {
                    const matchesSearch = !query || card.dataset.search.includes(query);
                    const matchesFilter = filter === 'all' || (filter === 'open' && card.dataset.open === 'true') || (filter === 'completed' && card.dataset.status === 'completed') || (filter === 'cancelled' && card.dataset.status === 'cancelled');
                    card.hidden = !(matchesSearch && matchesFilter);
                    return matchesSearch && matchesFilter;
                });
                visible.sort((a, b) => sort.value === 'highest' ? Number(b.dataset.total) - Number(a.dataset.total) : sort.value === 'oldest' ? Number(a.dataset.date) - Number(b.dataset.date) : Number(b.dataset.date) - Number(a.dataset.date));
                visible.forEach((card) => list.appendChild(card));
                empty.hidden = visible.length !== 0;
            };

            search?.addEventListener('input', render);
            sort?.addEventListener('change', render);
            document.querySelectorAll('[data-filter]').forEach((button) => button.addEventListener('click', () => {
                filter = button.dataset.filter;
                document.querySelectorAll('[data-filter]').forEach((item) => item.classList.toggle('is-active', item === button));
                render();
            }));
            document.querySelectorAll('[data-preview-trigger]').forEach((button) => button.addEventListener('click', () => {
                const preview = button.closest('[data-order-card]').querySelector('[data-preview]');
                const expanded = button.getAttribute('aria-expanded') === 'true';
                button.setAttribute('aria-expanded', String(!expanded));
                preview.classList.toggle('is-visible', !expanded);
            }));
            render();
        })();
    </script>
    @endpush
@endsection

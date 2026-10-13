@extends('layouts.admin')

@section('title', 'Order #' . $order->order_number . ' — Father Care Bakery')

@section('content')
    @php
        $statusKey = strtolower($order->status->value);
        $paymentStatusLabel = $order->payment_status->label();

        if (
            $order->payment_status === \App\Enums\PaymentStatus::AWAITING_PAYMENT
            && $order->payment_method === \App\Enums\PaymentMethod::CASH_ON_DELIVERY
        ) {
            $paymentStatusLabel = $order->delivery_type === \App\Enums\DeliveryType::PICKUP
                ? 'Pay at pickup'
                : 'Pay on delivery';
        }
    @endphp

    <div class="order-detail-page">
        <div class="page-header order-detail-header fade-in">
            <div>
                <a href="{{ route('admin.orders.index') }}" class="order-detail-back"><i class="ph ph-arrow-left"></i> All orders</a>
                <span class="orders-eyebrow"><i class="ph ph-receipt"></i> Order details</span>
                <h1>#{{ $order->order_number }}</h1>
                <p>Placed {{ $order->created_at->format('d M Y \a\t H:i') }}</p>
            </div>
            <span class="chip chip-{{ $statusKey }} order-detail-header__status">
                <span class="chip-dot"></span>
                {{ $order->status->label() }}
            </span>
        </div>

        <div class="order-detail-summary fade-in fade-in-1">
            <article class="order-summary-card">
                <span class="order-summary-card__icon"><i class="ph ph-package"></i></span>
                <span class="order-summary-card__copy">
                    <small>Order status</small>
                    <strong><span class="chip chip-{{ $statusKey }}"><span class="chip-dot"></span>{{ $order->status->label() }}</span></strong>
                </span>
            </article>
            <article class="order-summary-card">
                <span class="order-summary-card__icon"><i class="ph ph-credit-card"></i></span>
                <span class="order-summary-card__copy">
                    <small>Payment</small>
                    <strong>{{ $paymentStatusLabel }}</strong>
                    <span>{{ $order->payment_method?->label() ?? 'Not set' }}</span>
                </span>
            </article>
            <article class="order-summary-card order-summary-card--total">
                <span class="order-summary-card__icon"><i class="ph ph-currency-ngn"></i></span>
                <span class="order-summary-card__copy">
                    <small>Order total</small>
                    <strong>₦{{ number_format($order->total_amount, 2) }}</strong>
                    <span>{{ $order->items->count() }} {{ \Illuminate\Support\Str::plural('line item', $order->items->count()) }}</span>
                </span>
            </article>
        </div>

        <div class="order-detail-grid fade-in fade-in-2">
            <main class="order-detail-main">
                <section class="panel order-detail-panel">
                    <div class="panel-header order-detail-panel__header">
                        <div>
                            <h2><i class="ph ph-basket"></i> Items ordered</h2>
                            <p>Products included in this order</p>
                        </div>
                    </div>
                    <div class="order-items-wrap">
                        <table class="saas-table order-items-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit price</th>
                                    <th class="order-items-table__total">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td><strong class="order-item-name">{{ $item->product_name }}</strong></td>
                                        <td><span class="order-item-quantity">{{ $item->quantity }} ×</span></td>
                                        <td class="order-item-unit">₦{{ number_format($item->price, 2) }}</td>
                                        <td class="amount-cell order-items-table__total">₦{{ number_format($item->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3">Total</td>
                                    <td class="order-items-table__total">₦{{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>

                <section class="panel order-detail-panel">
                    <div class="panel-header order-detail-panel__header">
                        <div>
                            <h2><i class="ph ph-user"></i> Customer & delivery</h2>
                            <p>Contact details and fulfillment destination</p>
                        </div>
                    </div>
                    <div class="order-info-grid">
                        <div class="order-info-cell">
                            <span>Customer</span>
                            <strong>{{ $order->user?->name ?? 'Guest / Walk-in' }}</strong>
                        </div>
                        <div class="order-info-cell">
                            <span>Phone</span>
                            <a href="tel:{{ $order->phone }}">{{ $order->phone }}</a>
                        </div>
                        <div class="order-info-cell">
                            <span>Delivery method</span>
                            <strong><i class="ph {{ $order->delivery_type->value === 'pickup' ? 'ph-storefront' : 'ph-moped' }}"></i> {{ $order->delivery_type->label() }}</strong>
                        </div>
                        <div class="order-info-cell">
                            <span>{{ $order->delivery_type->value === 'pickup' ? 'Collection' : 'Delivery address' }}</span>
                            <strong>{{ $order->delivery_address ?: 'Pickup directly at bakery' }}</strong>
                        </div>
                        @if($order->notes)
                            <div class="order-info-cell order-info-cell--wide">
                                <span>Special instructions</span>
                                <p>{{ $order->notes }}</p>
                            </div>
                        @endif
                        @if($order->payment_reference)
                            <div class="order-info-cell order-info-cell--wide">
                                <span>Payment reference</span>
                                <strong class="order-reference">{{ $order->payment_reference }}</strong>
                            </div>
                        @endif
                        @if($order->paymentReceiptUrl)
                            <div class="order-info-cell order-info-cell--wide">
                                <span>Transfer receipt</span>
                                @if(str_ends_with(strtolower((string) $order->payment_receipt), '.pdf'))
                                    <a href="{{ $order->paymentReceiptUrl }}" target="_blank" rel="noopener noreferrer" class="order-receipt-link">
                                        <i class="ph ph-file-pdf"></i> View receipt PDF
                                    </a>
                                @else
                                    <a href="{{ $order->paymentReceiptUrl }}" target="_blank" rel="noopener noreferrer" class="order-receipt-image-link">
                                        <img src="{{ $order->paymentReceiptUrl }}" alt="Payment receipt for {{ $order->order_number }}">
                                        <span>Open receipt image <i class="ph ph-arrow-up-right"></i></span>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </section>
            </main>

            <aside class="order-detail-aside">
                <section class="stat-card order-action-card">
                    <div class="order-action-card__heading">
                        <span class="order-action-card__icon"><i class="ph ph-arrows-clockwise"></i></span>
                        <div><h2>Update order status</h2><p>Let the customer know how it’s progressing.</p></div>
                    </div>
                    <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                        @csrf
                        @method('PATCH')
                        <label class="form-label" for="order-status">Order status</label>
                        <select id="order-status" name="status" class="form-select">
                            @foreach($statuses as $status)
                                <option value="{{ $status->value }}" @selected($order->status === $status)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-success order-action-card__button" type="submit"><i class="ph ph-floppy-disk"></i> Save status</button>
                    </form>
                </section>

                <section class="stat-card order-action-card">
                    <div class="order-action-card__heading">
                        <span class="order-action-card__icon"><i class="ph ph-credit-card"></i></span>
                        <div><h2>Update payment</h2><p>Record the latest payment state.</p></div>
                    </div>
                    <form method="POST" action="{{ route('admin.orders.payment', $order) }}">
                        @csrf
                        @method('PATCH')
                        <label class="form-label" for="payment-status">Payment status</label>
                        <select id="payment-status" name="payment_status" class="form-select">
                            @foreach($paymentStatuses as $paymentStatus)
                                <option value="{{ $paymentStatus->value }}" @selected($order->payment_status === $paymentStatus)>{{ $paymentStatus->label() }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-success order-action-card__button" type="submit"><i class="ph ph-floppy-disk"></i> Save payment</button>
                    </form>
                </section>
            </aside>
        </div>
    </div>

    @push('styles')
        <style>
            .order-detail-page { width: 100%; }
            .order-detail-page, .order-detail-page > *, .order-detail-grid > * { min-width: 0; }
            .orders-eyebrow { display: inline-flex; align-items: center; gap: 7px; margin-top: 9px; color: var(--brand-choco); font-size: .68rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
            .order-detail-header { align-items: flex-end; margin-bottom: 20px; }
            .order-detail-header h1 { margin: 6px 0 6px; font-family: var(--font-serif); font-size: clamp(1.8rem, 3vw, 2.5rem); letter-spacing: -.04em; overflow-wrap: anywhere; }
            .order-detail-header p { margin: 0; color: var(--brand-muted); font-size: .82rem; }
            .order-detail-back { display: flex; align-items: center; gap: 6px; width: fit-content; color: var(--brand-muted); font-size: .76rem; font-weight: 700; text-decoration: none; }
            .order-detail-back:hover { color: var(--brand-choco); }
            .order-detail-header__status { padding: 7px 12px; font-size: .75rem; }
            .order-detail-summary { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; margin-bottom: 20px; }
            .order-summary-card { display: flex; align-items: center; gap: 13px; min-width: 0; padding: 16px; border: 1px solid var(--card-border); border-radius: 16px; background: #fff; box-shadow: var(--card-shadow); }
            .order-summary-card__icon { display: grid; width: 42px; height: 42px; flex: 0 0 42px; place-items: center; border-radius: 13px; background: #f5ede6; color: var(--brand-choco); font-size: 1.2rem; }
            .order-summary-card__copy { display: grid; min-width: 0; gap: 4px; }
            .order-summary-card__copy small { color: var(--brand-muted); font-size: .66rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
            .order-summary-card__copy strong { color: var(--brand-ink); font-size: .95rem; }
            .order-summary-card__copy > span { color: var(--brand-muted); font-size: .75rem; }
            .order-summary-card--total .order-summary-card__copy strong { font-size: 1.12rem; }
            .order-detail-grid { display: grid; grid-template-columns: minmax(0, 1.55fr) minmax(280px, .85fr); gap: 18px; align-items: start; }
            .order-detail-main, .order-detail-aside { display: grid; min-width: 0; gap: 18px; }
            .order-detail-panel { overflow: hidden; }
            .order-detail-panel__header { min-height: 68px; }
            .order-detail-panel__header h2 { margin: 0; }
            .order-detail-panel__header p { margin: 5px 0 0 25px; color: var(--brand-muted); font-size: .74rem; }
            .order-items-wrap { overflow-x: auto; }
            .order-items-table { min-width: 540px; }
            .order-items-table th, .order-items-table td { padding-right: 17px; padding-left: 17px; }
            .order-items-table__total { text-align: right; white-space: nowrap; }
            .order-item-name { color: var(--brand-ink); }
            .order-item-quantity { display: inline-flex; padding: 5px 9px; border: 1px solid var(--card-border); border-radius: 8px; background: #f8f3ee; color: var(--brand-choco); font-size: .75rem; font-weight: 800; }
            .order-item-unit { color: var(--brand-muted) !important; white-space: nowrap; }
            .order-items-table tfoot td { padding: 15px 17px; border-top: 1px solid var(--card-border); background: #f8f3ee; color: var(--brand-ink); font-size: .9rem; font-weight: 800; }
            .order-items-table tfoot td:last-child { color: var(--brand-choco); font-size: 1rem; }
            .order-info-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .order-info-cell { display: grid; align-content: start; gap: 6px; min-width: 0; padding: 15px 18px; border-bottom: 1px solid #eee5dc; }
            .order-info-cell:nth-child(odd):not(.order-info-cell--wide) { border-right: 1px solid #eee5dc; }
            .order-info-cell > span { color: var(--brand-muted); font-size: .65rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
            .order-info-cell > strong, .order-info-cell > a:not(.order-receipt-link):not(.order-receipt-image-link) { color: var(--brand-ink); font-size: .82rem; overflow-wrap: anywhere; }
            .order-info-cell > strong i { color: var(--brand-choco); }
            .order-info-cell > a { color: var(--brand-choco); text-decoration: none; }
            .order-info-cell--wide { grid-column: 1 / -1; border-bottom: 0; }
            .order-info-cell--wide p { margin: 0; padding: 10px 12px; border: 1px solid var(--card-border); border-radius: 10px; background: #f8f3ee; color: var(--brand-ink-soft); font-size: .8rem; white-space: pre-line; }
            .order-reference { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: .75rem !important; }
            .order-receipt-link { display: inline-flex; align-items: center; gap: 7px; color: var(--brand-choco); font-size: .8rem; font-weight: 700; text-decoration: none; }
            .order-receipt-image-link { display: grid; justify-items: start; gap: 7px; width: fit-content; color: var(--brand-choco); font-size: .72rem; font-weight: 700; text-decoration: none; }
            .order-receipt-image-link img { width: min(100%, 260px); max-height: 180px; object-fit: contain; border: 1px solid var(--card-border); border-radius: 10px; background: #fff; }
            .order-action-card { padding: 18px; }
            .order-action-card__heading { display: flex; align-items: flex-start; gap: 11px; margin-bottom: 17px; }
            .order-action-card__icon { display: grid; width: 38px; height: 38px; flex: 0 0 38px; place-items: center; border-radius: 11px; background: #f5ede6; color: var(--brand-choco); font-size: 1.1rem; }
            .order-action-card__heading h2 { margin: 1px 0 5px; font-size: .9rem; }
            .order-action-card__heading p { margin: 0; color: var(--brand-muted); font-size: .73rem; line-height: 1.5; }
            .order-action-card .form-select { min-height: 44px; border-color: var(--card-border); }
            .order-action-card__button { display: flex; align-items: center; justify-content: center; gap: 7px; width: 100%; min-height: 42px; margin-top: 13px; }
            @media (max-width: 1050px) {
                .order-detail-grid { grid-template-columns: minmax(0, 1fr); }
                .order-detail-aside { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            }
            @media (max-width: 650px) {
                .order-detail-header { align-items: flex-start; }
                .order-detail-header__status { align-self: flex-end; }
                .order-detail-summary { grid-template-columns: minmax(0, 1fr); gap: 10px; }
                .order-summary-card { padding: 12px 14px; }
                .order-detail-aside { grid-template-columns: minmax(0, 1fr); }
                .order-info-cell { padding: 13px; }
                .order-detail-panel__header { align-items: flex-start; }
            }
            @media (max-width: 420px) {
                .order-detail-header { flex-direction: column; align-items: flex-start; }
                .order-detail-header__status { align-self: flex-start; }
                .order-info-grid { grid-template-columns: minmax(0, 1fr); }
                .order-info-cell:nth-child(odd):not(.order-info-cell--wide) { border-right: 0; }
            }
        </style>
    @endpush
@endsection

@extends('layouts.customer')

@section('title', 'Order '.$order->order_number.' - Father Care Bakery')

@section('content')
    <div class="order-detail-page">
        <div class="container py-5">
            <a href="{{ route('customer.orders.index') }}" class="back-link mb-3">
                <i class="ph ph-arrow-left"></i>
                Back to orders
            </a>

            <div class="order-detail-header mb-4">
                <div>
                    <div class="section-eyebrow">
                        <i class="ph ph-receipt"></i> Order details
                    </div>
                    <h1 class="order-detail-title">{{ $order->order_number }}</h1>
                    <p class="order-detail-date">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                </div>
                <div class="status-group">
                    <span class="order-status status-{{ strtolower(str_replace('_', '-', $order->status->value ?? $order->status->name ?? 'pending')) }}">
                        {{ $order->status->label() }}
                    </span>
                    @if($order->payment_status)
                        <span class="payment-status-pill {{ $order->payment_status->isPaid() ? 'is-paid' : 'is-pending' }}">
                            {{ $order->payment_status->label() }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="detail-panel">
                        <div class="panel-head">
                            <h2>Items ordered</h2>
                            <span>{{ $order->items->count() }} item(s)</span>
                        </div>

                        <div class="item-list">
                            @foreach($order->items as $item)
                                <div class="item-row">
                                    <div class="item-info">
                                        <div class="item-name">{{ $item->product_name }}</div>
                                        <div class="item-meta">Qty: {{ $item->quantity }}</div>
                                    </div>
                                    <div class="item-price">₦{{ number_format($item->total, 2) }}</div>
                                </div>
                            @endforeach
                        </div>

                        <div class="totals-box">
                            <div class="total-row">
                                <span>Subtotal</span>
                                <strong>₦{{ number_format($order->total_amount, 2) }}</strong>
                            </div>
                        </div>
                    </div>

                    @if($order->payment_method === \App\Enums\PaymentMethod::BANK_TRANSFER)
                        <div class="detail-panel mt-4">
                            <div class="panel-head">
                                <h2>Bank transfer</h2>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <div class="info-label">Bank</div>
                                    <div class="info-value">{{ config('bakery.bank.name') }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-label">Account name</div>
                                    <div class="info-value">{{ config('bakery.bank.account_name') }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-label">Account number</div>
                                    <div class="info-value">{{ config('bakery.bank.account_number') }}</div>
                                </div>
                            </div>

                            @if($order->paymentReceiptUrl)
                                <div class="info-label mb-2">Uploaded receipt</div>
                                @if(str_ends_with(strtolower((string) $order->payment_receipt), '.pdf'))
                                    <a href="{{ $order->paymentReceiptUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm-sage-outline">
                                        <i class="ph ph-file-pdf"></i> View receipt PDF
                                    </a>
                                @else
                                    <a href="{{ $order->paymentReceiptUrl }}" target="_blank" rel="noopener noreferrer">
                                        <img src="{{ $order->paymentReceiptUrl }}"
                                             alt="Payment receipt for {{ $order->order_number }}"
                                             class="receipt-image">
                                    </a>
                                @endif
                            @elseif($order->isAwaitingManualPayment())
                                <p class="muted-copy mb-0">No receipt on file yet. Please contact the bakery if you already transferred.</p>
                            @endif
                        </div>
                    @endif

                    @if($order->payment_method === \App\Enums\PaymentMethod::CASH_ON_DELIVERY && $order->isAwaitingManualPayment())
                        <div class="detail-panel mt-4">
                            <div class="panel-head">
                                <h2>Cash on delivery</h2>
                            </div>
                            <p class="muted-copy mb-0">Please have <strong>₦{{ number_format($order->total_amount, 2) }}</strong> ready when your order arrives.</p>
                        </div>
                    @endif
                </div>

                <div class="col-lg-4">
                    <div class="detail-panel">
                        <div class="panel-head">
                            <h2>Fulfillment</h2>
                        </div>

                        <div class="info-block">
                            <div class="info-label">Delivery method</div>
                            <div class="info-value">{{ $order->delivery_type->label() }}</div>
                        </div>

                        <div class="info-block">
                            <div class="info-label">Address</div>
                            <div class="info-value">
                                @if($order->delivery_address)
                                    {{ $order->delivery_address }}
                                @else
                                    Pickup at {{ config('bakery.address') }}
                                @endif
                            </div>
                        </div>

                        <div class="info-block">
                            <div class="info-label">Phone</div>
                            <div class="info-value">{{ $order->phone }}</div>
                        </div>

                        @if($order->payment_method)
                            <div class="info-block">
                                <div class="info-label">Payment</div>
                                <div class="info-value">{{ $order->payment_method->label() }}</div>
                            </div>
                        @endif

                        @if($order->notes)
                            <div class="info-block">
                                <div class="info-label">Notes</div>
                                <div class="info-value">{{ $order->notes }}</div>
                            </div>
                        @endif

                        @if($order->needsOnlinePayment())
                            <a href="{{ route('customer.payments.retry', $order) }}" class="btn-sage w-100 mt-3">
                                {{ $order->payment_status === \App\Enums\PaymentStatus::FAILED ? 'Try payment again' : 'Complete payment' }}
                            </a>
                        @endif

                        @if($order->canBeCancelled())
                            <form method="POST" action="{{ route('customer.orders.cancel', $order) }}" class="mt-3">
                                @csrf
                                <button class="btn btn-outline-danger w-100" type="submit">Cancel order</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .order-detail-page {
            background: linear-gradient(180deg, #fff 0%, #f9f4ef 100%);
            min-height: calc(100vh - 120px);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            color: var(--sage);
            font-weight: 600;
        }

        .order-detail-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .order-detail-title {
            margin: 0.45rem 0 0.15rem;
            font-size: clamp(2.3rem, 5vw, 3.4rem);
            letter-spacing: -0.05em;
        }

        .order-detail-date {
            margin: 0;
            color: var(--muted);
        }

        .status-group {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.7rem;
        }

        .payment-status-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 0.8rem;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .payment-status-pill.is-paid {
            background: rgba(38, 125, 82, 0.1);
            color: #186c47;
        }

        .payment-status-pill.is-pending {
            background: rgba(224, 146, 22, 0.12);
            color: #8d5c10;
        }

        .detail-panel {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 1.4rem;
            box-shadow: 0 8px 18px rgba(61,43,31,0.04);
        }

        .panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.1rem;
        }

        .panel-head h2 {
            margin: 0;
            font-size: 1.65rem;
        }

        .panel-head span {
            color: var(--muted);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .item-list {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
        }

        .item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.9rem 0;
            border-bottom: 1px solid rgba(216,199,176,0.9);
        }

        .item-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .item-name {
            font-weight: 600;
            color: var(--ink);
        }

        .item-meta {
            font-size: 0.8rem;
            color: var(--muted);
            margin-top: 0.15rem;
        }

        .item-price {
            font-weight: 700;
            white-space: nowrap;
            color: var(--ink);
        }

        .totals-box {
            margin-top: 1.2rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(216,199,176,0.9);
        }

        .total-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            font-size: 1.02rem;
        }

        .info-label {
            font-size: 0.72rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 0.25rem;
        }

        .info-block {
            padding-bottom: 1rem;
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(216,199,176,0.9);
        }

        .info-block:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .info-value {
            color: var(--ink);
            font-weight: 500;
            line-height: 1.6;
        }

        .muted-copy {
            color: var(--muted);
            line-height: 1.7;
        }

        .receipt-image {
            width: 100%;
            max-height: 300px;
            object-fit: contain;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: #fff;
            margin-top: 0.5rem;
        }

        @media (max-width: 767.98px) {
            .order-detail-header {
                align-items: flex-start;
            }

            .item-row {
                align-items: flex-start;
                flex-direction: column;
            }

            .item-price {
                width: 100%;
                text-align: left;
            }
        }
    </style>
    @endpush
@endsection

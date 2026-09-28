@extends('layouts.admin')

@section('title', 'Order #' . $order->order_number . ' — Father Care Bakery')

@section('content')
    <div class="page-header fade-in">
        <div>
            <h1>Order #{{ $order->order_number }}</h1>
            <p>Placed on {{ $order->created_at->format('d M Y, H:i') }}</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-ghost">
            <i class="ph ph-arrow-left"></i> Back to Orders
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="panel fade-in fade-in-1 mb-4">
                <div class="panel-header">
                    <h2><i class="ph ph-bag"></i> Order Items</h2>
                    <span class="chip chip-{{ strtolower($order->status->value ?? 'pending') }}">
                        <span class="chip-dot"></span>
                        {{ $order->status->label() }}
                    </span>
                </div>
                <div style="overflow-x:auto;">
                    <table class="saas-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th style="text-align:right;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div style="font-weight:600; color:var(--brand-ink);">{{ $item->product_name }}</div>
                                    </td>
                                    <td>
                                        <span class="chip" style="background:#F5EDE6; color:var(--brand-choco); border:1px solid #D8C7B0;">
                                            {{ $item->quantity }}x
                                        </span>
                                    </td>
                                    <td style="color:var(--brand-ink-soft);">₦{{ number_format($item->price, 2) }}</td>
                                    <td class="amount-cell" style="text-align:right;">₦{{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background:#F5EDE6; border-top:2px solid var(--card-border);">
                                <td colspan="3" style="text-align:right; font-weight:700; color:var(--brand-ink); font-size:.95rem; padding:16px 18px;">
                                    Grand Total:
                                </td>
                                <td class="amount-cell" style="text-align:right; font-size:1.15rem; color:var(--brand-choco); font-weight:800; padding:16px 18px;">
                                    ₦{{ number_format($order->total_amount, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Customer & Delivery Details --}}
            <div class="stat-card mb-4 fade-in fade-in-2">
                <h2 style="font-size:1rem; font-weight:700; margin-bottom:16px; color:var(--brand-ink); display:flex; align-items:center; gap:8px;">
                    <i class="ph ph-user" style="color:var(--brand-choco);"></i> Customer Info
                </h2>
                <div style="display:flex; flex-direction:column; gap:12px; font-size:.875rem;">
                    <div>
                        <span class="form-label" style="margin-bottom:2px;">Name</span>
                        <strong style="color:var(--brand-ink);">{{ $order->user?->name ?? 'Guest / Walk-in' }}</strong>
                    </div>
                    <div>
                        <span class="form-label" style="margin-bottom:2px;">Phone</span>
                        <a href="tel:{{ $order->phone }}" style="color:var(--brand-choco); font-weight:600; text-decoration:none;">{{ $order->phone }}</a>
                    </div>
                    <div>
                        <span class="form-label" style="margin-bottom:2px;">Delivery Method</span>
                        <span class="chip" style="background:#F5EDE6; border:1px solid #D8C7B0; color:var(--brand-choco);">
                            <i class="ph {{ $order->delivery_type->value === 'pickup' ? 'ph-storefront' : 'ph-moped' }}"></i>
                            {{ $order->delivery_type->label() }}
                        </span>
                    </div>
                    <div>
                        <span class="form-label" style="margin-bottom:2px;">Delivery Address</span>
                        <span style="color:var(--brand-ink-soft);">{{ $order->delivery_address ?: 'Pickup directly at bakery' }}</span>
                    </div>
                    @if($order->notes)
                        <div>
                            <span class="form-label" style="margin-bottom:2px;">Special Notes</span>
                            <div style="background:#F5EDE6; padding:10px 12px; border-radius:6px; border:1px solid #D8C7B0; color:var(--brand-ink-soft); font-size:.82rem;">
                                {{ $order->notes }}
                            </div>
                        </div>
                    @endif
                    <div>
                        <span class="form-label" style="margin-bottom:2px;">Payment method</span>
                        <strong style="color:var(--brand-ink);">{{ $order->payment_method?->label() ?? 'Not set' }}</strong>
                    </div>
                    <div>
                        <span class="form-label" style="margin-bottom:2px;">Payment status</span>
                        <span class="chip" style="background:#F5EDE6; border:1px solid #D8C7B0; color:var(--brand-choco);">
                            {{ $order->payment_status?->label() ?? 'Pending' }}
                        </span>
                    </div>
                    @if($order->payment_reference)
                        <div>
                            <span class="form-label" style="margin-bottom:2px;">Payment reference</span>
                            <span style="color:var(--brand-ink-soft);">{{ $order->payment_reference }}</span>
                        </div>
                    @endif
                    @if($order->paymentReceiptUrl)
                        <div>
                            <span class="form-label" style="margin-bottom:2px;">Transfer receipt</span>
                            @if(str_ends_with(strtolower((string) $order->payment_receipt), '.pdf'))
                                <a href="{{ $order->paymentReceiptUrl }}" target="_blank" rel="noopener noreferrer" style="color:var(--brand-choco); font-weight:600;">
                                    View receipt PDF
                                </a>
                            @else
                                <a href="{{ $order->paymentReceiptUrl }}" target="_blank" rel="noopener noreferrer">
                                    <img src="{{ $order->paymentReceiptUrl }}"
                                         alt="Payment receipt"
                                         style="width:100%; max-height:220px; object-fit:contain; border-radius:10px; border:1px solid #D8C7B0; background:#fff; margin-top:6px;">
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Payment Update Form --}}
            <div class="stat-card mb-4 fade-in fade-in-3">
                <h2 style="font-size:1rem; font-weight:700; margin-bottom:16px; color:var(--brand-ink); display:flex; align-items:center; gap:8px;">
                    <i class="ph ph-money" style="color:var(--brand-choco);"></i> Update Payment
                </h2>
                <form method="POST" action="{{ route('admin.orders.payment', $order) }}">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label">Payment Status</label>
                        <select name="payment_status" class="form-select">
                            @foreach($paymentStatuses as $paymentStatus)
                                <option value="{{ $paymentStatus->value }}" @selected($order->payment_status === $paymentStatus)>{{ $paymentStatus->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-success w-100" type="submit">
                        <i class="ph ph-floppy-disk"></i> Update Payment
                    </button>
                </form>
            </div>

            {{-- Status Update Form --}}
            <div class="stat-card fade-in fade-in-3">
                <h2 style="font-size:1rem; font-weight:700; margin-bottom:16px; color:var(--brand-ink); display:flex; align-items:center; gap:8px;">
                    <i class="ph ph-arrows-clockwise" style="color:var(--brand-choco);"></i> Update Status
                </h2>
                <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label">Order Status</label>
                        <select name="status" class="form-select">
                            @foreach($statuses as $status)
                                <option value="{{ $status->value }}" @selected($order->status === $status)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-success w-100" type="submit">
                        <i class="ph ph-floppy-disk"></i> Update Status
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

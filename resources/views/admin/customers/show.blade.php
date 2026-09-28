@extends('layouts.admin')

@section('title', $customer->name . ' — Customer Profile')

@section('content')
    <div class="page-header fade-in">
        <div>
            <h1>{{ $customer->name }}</h1>
            <p>Customer Profile & Order History</p>
        </div>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-ghost">
            <i class="ph ph-arrow-left"></i> Back to Customers
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="stat-card fade-in fade-in-1">
                <div style="display:flex; align-items:center; gap:14px; margin-bottom:18px;">
                    <div class="cust-avatar" style="width:48px; height:48px; font-size:1.1rem;">
                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                    </div>
                    <div>
                        <strong style="color:var(--brand-ink); font-size:1.05rem; display:block;">{{ $customer->name }}</strong>
                        <span style="color:var(--brand-muted); font-size:.78rem;">Customer ID #{{ $customer->id }}</span>
                    </div>
                </div>

                <div style="display:flex; flex-direction:column; gap:12px; font-size:.875rem; border-top:1px solid var(--card-border); padding-top:16px;">
                    <div>
                        <span class="form-label" style="margin-bottom:2px;">Email Address</span>
                        <a href="mailto:{{ $customer->email }}" style="color:var(--brand-choco); font-weight:600; text-decoration:none;">{{ $customer->email }}</a>
                    </div>
                    <div>
                        <span class="form-label" style="margin-bottom:2px;">Phone Number</span>
                        <span style="color:var(--brand-ink-soft);">{{ $customer->phone ?: 'No phone provided' }}</span>
                    </div>
                    <div>
                        <span class="form-label" style="margin-bottom:2px;">Delivery Address</span>
                        <span style="color:var(--brand-ink-soft);">{{ $customer->address ?: 'No saved address' }}</span>
                    </div>
                    <div>
                        <span class="form-label" style="margin-bottom:2px;">Member Since</span>
                        <span style="color:var(--brand-ink-soft);">{{ $customer->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="panel fade-in fade-in-2">
                <div class="panel-header">
                    <h2><i class="ph ph-receipt"></i> Orders History</h2>
                    <span class="chip" style="background:#F5EDE6; color:var(--brand-choco); border:1px solid #D8C7B0;">
                        {{ $customer->orders->count() }} Orders
                    </span>
                </div>
                <div style="overflow-x:auto;">
                    <table class="saas-table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th style="text-align:right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customer->orders as $order)
                                @php $sk = strtolower($order->status->value ?? 'pending'); @endphp
                                <tr>
                                    <td>
                                        <a class="order-num" href="{{ route('admin.orders.show', $order) }}">
                                            #{{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td style="color:var(--brand-muted); font-size:.82rem;">
                                        {{ $order->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td>
                                        <span class="chip chip-{{ $sk }}">
                                            <span class="chip-dot"></span>
                                            {{ $order->status->label() }}
                                        </span>
                                    </td>
                                    <td class="amount-cell">₦{{ number_format($order->total_amount, 2) }}</td>
                                    <td style="text-align:right;">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-ghost btn-sm">
                                            <i class="ph ph-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state">
                                            <i class="ph ph-receipt"></i>
                                            This customer has not placed any orders yet.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.admin')

@section('title', 'Orders — Father Care Bakery')

@section('content')
    <div class="page-header fade-in">
        <div>
            <h1>Orders Management</h1>
            <p>Review customer orders, update order statuses, and track fulfillment</p>
        </div>
        <form method="GET" class="d-flex gap-2">
            <select name="status" class="form-select" style="min-width:180px;" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="panel fade-in fade-in-1">
        <div style="overflow-x:auto;">
            <table class="saas-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Delivery Type</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th style="text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        @php $sk = strtolower($order->status->value ?? 'pending'); @endphp
                        <tr>
                            <td>
                                <a class="order-num" href="{{ route('admin.orders.show', $order) }}">
                                    #{{ $order->order_number }}
                                </a>
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:9px;">
                                    <div class="sidebar-user-avatar" style="width:28px; height:28px; font-size:.65rem;">
                                        {{ strtoupper(substr($order->user?->name ?? '?', 0, 1)) }}
                                    </div>
                                    <span style="font-weight:600; color:var(--brand-ink);">{{ $order->user?->name ?? '—' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="chip" style="background:#F5EDE6; border:1px solid #D8C7B0; color:var(--brand-choco);">
                                    <i class="ph {{ $order->delivery_type->value === 'pickup' ? 'ph-storefront' : 'ph-moped' }}"></i>
                                    {{ $order->delivery_type->label() }}
                                </span>
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
                                    <i class="ph ph-eye"></i> View Order
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="ph ph-receipt"></i>
                                    No orders found matching your criteria.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div style="padding:16px 22px; border-top:1px solid var(--card-border);">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection

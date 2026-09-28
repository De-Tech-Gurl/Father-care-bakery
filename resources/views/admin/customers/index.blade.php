@extends('layouts.admin')

@section('title', 'Customers — Father Care Bakery')

@section('content')
    <div class="page-header fade-in">
        <div>
            <h1>Customers</h1>
            <p>View registered bakery patrons and their purchase histories</p>
        </div>
    </div>

    <div class="panel fade-in fade-in-1">
        <div style="overflow-x:auto;">
            <table class="saas-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Total Orders</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div class="cust-avatar" style="width:34px; height:34px; font-size:.75rem;">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                    </div>
                                    <strong style="color:var(--brand-ink); font-size:.88rem;">{{ $customer->name }}</strong>
                                </div>
                            </td>
                            <td style="color:var(--brand-ink-soft);">{{ $customer->email }}</td>
                            <td style="color:var(--brand-ink-soft);">{{ $customer->phone ?? '—' }}</td>
                            <td>
                                <span class="chip" style="background:#F5EDE6; color:var(--brand-choco); border:1px solid #D8C7B0;">
                                    {{ $customer->orders_count }} {{ Str::plural('order', $customer->orders_count) }}
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-ghost btn-sm">
                                    <i class="ph ph-eye"></i> View Profile
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="ph ph-users"></i>
                                    No registered customers found.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
            <div style="padding:16px 22px; border-top:1px solid var(--card-border);">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
@endsection

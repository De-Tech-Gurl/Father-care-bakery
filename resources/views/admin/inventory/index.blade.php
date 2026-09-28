@extends('layouts.admin')

@section('title', 'Inventory — Father Care Bakery')

@section('content')
    <div class="page-header fade-in">
        <div>
            <h1>Inventory Management</h1>
            <p>Track stock levels and record inventory adjustments</p>
        </div>
    </div>

    {{-- Stock adjustment form --}}
    <div class="stat-card mb-4 fade-in fade-in-1">
        <h2 style="font-size:1rem; font-weight:700; margin-bottom:18px; color:var(--brand-ink); display:flex; align-items:center; gap:8px;">
            <i class="ph ph-sliders" style="color:var(--brand-choco);"></i> Adjust Stock
        </h2>
        <form method="POST" action="{{ route('admin.inventory.store') }}" class="row g-3 align-items-end">
            @csrf
            <div class="col-md-4">
                <label class="form-label">Product</label>
                <select name="product_id" class="form-select" required>
                    <option value="">Select product...</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} (Current: {{ $product->stock_quantity }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Change (+ / -)</label>
                <input type="number" name="quantity_change" class="form-control" placeholder="+10 or -2" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Reason</label>
                <select name="reason" class="form-select" required>
                    @foreach($reasons as $reason)
                        <option value="{{ $reason->value }}">{{ $reason->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Reference</label>
                <input type="text" name="reference" class="form-control" placeholder="PO#, batch, etc.">
            </div>
            <div class="col-md-1">
                <label class="form-label" style="opacity:0; user-select:none;">&nbsp;</label>
                <button class="btn btn-success w-100" style="padding:9px 0;">Save</button>
            </div>
        </form>
    </div>

    {{-- Movement logs --}}
    <div class="panel fade-in fade-in-2">
        <div class="panel-header">
            <h2><i class="ph ph-clock-counter-clockwise"></i> Recent Stock Movements</h2>
        </div>
        <div style="overflow-x:auto;">
            <table class="saas-table">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Product</th>
                        <th>Change</th>
                        <th>Reason</th>
                        <th>Recorded By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                        @php
                            $isPos = $movement->quantity_change > 0;
                        @endphp
                        <tr>
                            <td style="color:var(--brand-muted); font-size:.82rem;">
                                {{ $movement->created_at->format('d M Y, H:i') }}
                            </td>
                            <td>
                                <strong style="color:var(--brand-ink);">{{ $movement->product?->name ?? '—' }}</strong>
                            </td>
                            <td>
                                <span class="chip {{ $isPos ? 'chip-ready' : 'chip-cancelled' }}">
                                    {{ $isPos ? '+' : '' }}{{ $movement->quantity_change }}
                                </span>
                            </td>
                            <td>
                                <span class="chip chip-pending">
                                    {{ $movement->reason->label() }}
                                </span>
                            </td>
                            <td style="color:var(--brand-ink-soft);">
                                {{ $movement->creator?->name ?? 'System' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="ph ph-package"></i>
                                    No inventory movements recorded yet.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($movements->hasPages())
            <div style="padding:16px 22px; border-top:1px solid var(--card-border);">
                {{ $movements->links() }}
            </div>
        @endif
    </div>
@endsection

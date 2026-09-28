@extends('layouts.admin')

@section('title', 'Products — Father Care Bakery')

@section('content')

<div class="page-header fade-in">
    <div>
        <h1>Products Catalogue</h1>
        <p>Manage your bakery products, pricing, stock levels and visibility</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-success">
        <i class="ph ph-plus"></i> Add Product
    </a>
</div>

<div class="panel fade-in fade-in-1">
    <div style="overflow-x:auto;">
        <table class="saas-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:12px;">
                                @if($product->imageUrl)
                                    <img src="{{ $product->imageUrl }}" alt="{{ $product->name }}"
                                         style="width:42px;height:42px;border-radius:10px;object-fit:cover;border:1px solid var(--card-border);">
                                @else
                                    <div style="width:42px;height:42px;border-radius:10px;background:#F5EDE6;
                                                display:flex;align-items:center;justify-content:center;border:1px solid var(--card-border);">
                                        <i class="ph ph-cookie" style="color:var(--brand-choco);font-size:1.3rem;"></i>
                                    </div>
                                @endif
                                <div>
                                    <div style="font-weight:700;color:var(--brand-ink);font-size:.88rem;">{{ $product->name }}</div>
                                    @if($product->is_featured)
                                        <span class="chip" style="background:#F5EDE6; border:1px solid #D8C7B0; color:#6B3E1F; font-size:.62rem; padding:1px 7px; margin-top:3px;">
                                            ⭐ Featured
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="color:var(--brand-ink-soft);">{{ $product->category?->name ?? '—' }}</td>
                        <td class="amount-cell">₦{{ number_format($product->price, 2) }}</td>
                        <td>
                            @if($product->isLowStock())
                                <span class="chip chip-cancelled">
                                    <span class="chip-dot"></span>
                                    {{ $product->stock_quantity }} low stock
                                </span>
                            @else
                                <span style="font-weight:600; color:var(--brand-ink);">{{ $product->stock_quantity }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="chip {{ $product->is_active ? 'chip-active' : 'chip-hidden' }}">
                                <span class="chip-dot"></span>
                                {{ $product->is_active ? 'Active' : 'Hidden' }}
                            </span>
                        </td>
                        <td style="text-align:right;">
                            <div style="display:flex;gap:6px;justify-content:flex-end;">
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-ghost btn-sm">
                                    <i class="ph ph-pencil-simple"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                      style="display:inline;" onsubmit="return confirm('Delete {{ addslashes($product->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm" type="submit" title="Delete product">
                                        <i class="ph ph-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">
                        <div class="empty-state">
                            <i class="ph ph-cookie"></i>
                            No products in catalogue yet. <a href="{{ route('admin.products.create') }}" style="color:var(--brand-choco); font-weight:600;">Add your first product</a>.
                        </div>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
        <div style="padding:16px 22px;border-top:1px solid var(--card-border);">
            {{ $products->links() }}
        </div>
    @endif
</div>

@endsection

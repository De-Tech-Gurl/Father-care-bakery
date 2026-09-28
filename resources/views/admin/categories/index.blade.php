@extends('layouts.admin')

@section('title', 'Categories — Father Care Bakery')

@section('content')
    <div class="page-header fade-in">
        <div>
            <h1>Categories</h1>
            <p>Organize bakery products into customer-facing departments</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-success">
            <i class="ph ph-plus"></i> Add Category
        </a>
    </div>

    <div class="panel fade-in fade-in-1">
        <div style="overflow-x:auto;">
            <table class="saas-table">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Products Count</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div style="width:34px; height:34px; border-radius:8px; background:#F5EDE6; border:1px solid #D8C7B0; display:flex; align-items:center; justify-content:center; color:var(--brand-choco);">
                                        <i class="ph ph-tag" style="font-size:1.1rem;"></i>
                                    </div>
                                    <strong style="color:var(--brand-ink); font-size:.9rem;">{{ $category->name }}</strong>
                                </div>
                            </td>
                            <td>
                                <span class="chip" style="background:#F5EDE6; color:var(--brand-choco); border:1px solid #D8C7B0;">
                                    {{ $category->products_count }} {{ Str::plural('product', $category->products_count) }}
                                </span>
                            </td>
                            <td>
                                <span class="chip {{ $category->is_active ? 'chip-active' : 'chip-hidden' }}">
                                    <span class="chip-dot"></span>
                                    {{ $category->is_active ? 'Active' : 'Hidden' }}
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <div style="display:flex; gap:6px; justify-content:flex-end;">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-ghost btn-sm">
                                        <i class="ph ph-pencil-simple"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" style="display:inline;" onsubmit="return confirm('Delete category {{ addslashes($category->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="submit">
                                            <i class="ph ph-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <i class="ph ph-tag"></i>
                                    No categories added yet. <a href="{{ route('admin.categories.create') }}" style="color:var(--brand-choco);">Create one now</a>.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
            <div style="padding:16px 22px; border-top:1px solid var(--card-border);">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
@endsection

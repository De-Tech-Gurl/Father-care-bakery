@extends('layouts.customer')

@section('title', 'All Products - Father Care Bakery')

@section('content')
    <div class="container py-5">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div>
                <h1 class="fw-700 mb-1">Our Products</h1>
                <p class="text-muted mb-0">Browse bread, cakes, pastries, and more baked fresh daily.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('customer.products.index') }}" class="row g-2 mb-4">
            <div class="col-md-4">
                <select name="category" class="form-select">
                    <option value="">All categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id || (string) request('category') === $category->slug)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search products...">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary-custom w-100">Search</button>
            </div>
        </form>

        <div class="row g-4">
            @forelse($products as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    <article class="product-card">
                        <div class="product-card-topbar">
                            <span class="product-badge">Fresh today</span>
                            @if($product->stock_quantity > 0)
                                <span class="product-stock-dot">In stock</span>
                            @else
                                <span class="product-stock-dot sold-out">Sold out</span>
                            @endif
                        </div>

                        <a href="{{ route('customer.products.show', $product) }}" class="text-decoration-none">
                            <div class="product-img-wrap">
                                @if($product->imageUrl)
                                    <img src="{{ $product->imageUrl }}" alt="{{ $product->name }}">
                                @else
                                    <div class="product-img-placeholder">
                                        <i class="ph ph-cake"></i>
                                    </div>
                                @endif
                            </div>
                        </a>

                        <div class="product-body">
                            <div class="product-category-label">
                                {{ $product->category?->name ?? 'Bakery pick' }}
                            </div>
                            <div class="product-name">
                                <a href="{{ route('customer.products.show', $product) }}" class="text-decoration-none" style="color:inherit;">{{ $product->name }}</a>
                            </div>
                            <p class="product-desc">{{ \Illuminate\Support\Str::limit($product->description, 50) }}</p>

                            <div class="product-footer">
                                <div class="product-price-wrap">
                                    <span class="product-price">₦{{ number_format($product->price, 2) }}</span>
                                    @if($product->compare_at_price && $product->compare_at_price > $product->price)
                                        <span class="product-old-price">₦{{ number_format($product->compare_at_price, 2) }}</span>
                                    @endif
                                </div>
                                <button class="product-add-btn add-to-cart"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-price="{{ $product->price }}"
                                        @disabled($product->stock_quantity < 1)>
                                    <i class="ph ph-shopping-cart"></i>
                                    Add
                                </button>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No products match your search.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>

    @push('styles')
    <style>
        .product-card {
            background: #fff;
            border: 1px solid rgba(107,62,31,0.12);
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 12px 28px rgba(61,43,31,0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 36px rgba(61,43,31,0.06);
            border-color: rgba(107,62,31,0.2);
        }

        .product-card-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem 0.9rem 0;
        }

        .product-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.65rem;
            border-radius: 999px;
            background: rgba(107,62,31,0.08);
            color: var(--sage);
            font-size: 0.66rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .product-stock-dot {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            color: #2d5d38;
            font-size: 0.68rem;
            font-weight: 600;
        }

        .product-stock-dot::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #2d5d38;
            display: inline-block;
        }

        .product-stock-dot.sold-out {
            color: #8b2d2d;
        }

        .product-stock-dot.sold-out::before {
            background: #8b2d2d;
        }

        .product-img-wrap {
            height: 220px;
            margin: 0.8rem 0.9rem 0;
            border-radius: 18px;
            overflow: hidden;
            background: linear-gradient(135deg, #f4ede7 0%, #efe4d7 100%);
            position: relative;
        }

        .product-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-img-placeholder {
            width: 100%;
            height: 100%;
            display: grid;
            place-items: center;
            color: var(--sage);
            font-size: 3rem;
        }

        .product-body {
            padding: 1rem 0.95rem 0.95rem;
        }

        .product-category-label {
            color: var(--muted);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.35rem;
        }

        .product-name {
            font-size: 1.1rem;
            font-weight: 700;
            line-height: 1.35;
            min-height: 2.8rem;
        }

        .product-desc {
            color: var(--muted);
            font-size: 0.84rem;
            line-height: 1.6;
            min-height: 2.2rem;
            margin: 0.45rem 0 0.9rem;
        }

        .product-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.8rem;
        }

        .product-price-wrap {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .product-price {
            font-size: 1.12rem;
            font-weight: 800;
            color: var(--ink);
        }

        .product-old-price {
            color: var(--muted);
            font-size: 0.74rem;
            text-decoration: line-through;
        }

        .product-add-btn {
            border: none;
            border-radius: 12px;
            background: var(--sage);
            color: #fff;
            padding: 0.65rem 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 700;
            font-size: 0.82rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
            box-shadow: 0 10px 18px rgba(107,62,31,0.16);
        }

        .product-add-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 20px rgba(107,62,31,0.2);
        }

        .product-add-btn:disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }

        @media (max-width: 575.98px) {
            .product-img-wrap {
                height: 180px;
            }

            .product-name {
                min-height: unset;
            }
        }
    </style>
    @endpush
@endsection

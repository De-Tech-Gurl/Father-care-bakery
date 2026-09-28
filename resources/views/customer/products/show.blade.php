@extends('layouts.customer')

@section('title', $product->name.' - Father Care Bakery')

@section('content')
    <div class="container py-5">
        <div class="row g-5 align-items-start">
            <div class="col-lg-6">
                <div class="product-img-wrap" style="height: min(380px, 70vw); border-radius: var(--radius-lg);">
                    @if($product->imageUrl)
                        <img src="{{ $product->imageUrl }}" alt="{{ $product->name }}">
                    @else
                        <div class="product-img-placeholder" style="height:100%;">
                            <i class="ph ph-cake"></i>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-lg-6">
                @if($product->category)
                    <div class="section-eyebrow mb-2">{{ $product->category->name }}</div>
                @endif
                <h1 class="fw-700 mb-3">{{ $product->name }}</h1>
                <p class="text-muted mb-4" style="white-space: pre-line;">{{ $product->description }}</p>
                <div class="d-flex align-items-center flex-wrap gap-2 mb-3">
                    <div class="product-price" style="font-size:1.6rem;">₦{{ number_format($product->price, 2) }}</div>
                    @if($product->compare_at_price && $product->compare_at_price > $product->price)
                        <span class="combo-was">₦{{ number_format($product->compare_at_price, 0) }}</span>
                    @endif
                    @if($product->savingsPercent)
                        <span class="combo-save">SAVE {{ $product->savingsPercent }}%</span>
                    @endif
                </div>
                <p class="text-muted mb-4">
                    {{ $product->stock_quantity > 0 ? $product->stock_quantity.' in stock' : 'Currently out of stock' }}
                </p>
                <button class="btn-sage add-to-cart"
                        data-id="{{ $product->id }}"
                        data-name="{{ $product->name }}"
                        data-price="{{ $product->price }}"
                        @disabled($product->stock_quantity < 1)>
                    <i class="ph ph-shopping-cart"></i>
                    Add to cart
                </button>
            </div>
        </div>

        @if($related->isNotEmpty())
            <div class="mt-5 pt-4">
                <h2 class="section-title mb-4">You may also like</h2>
                <div class="row g-4">
                    @foreach($related as $item)
                        <div class="col-6 col-md-3">
                            <div class="product-card">
                                <a href="{{ route('customer.products.show', $item) }}" class="text-decoration-none">
                                    <div class="product-img-wrap">
                                        @if($item->imageUrl)
                                            <img src="{{ $item->imageUrl }}" alt="{{ $item->name }}">
                                        @else
                                            <div class="product-img-placeholder">
                                                <i class="ph ph-cake"></i>
                                            </div>
                                        @endif
                                    </div>
                                </a>
                                <div class="product-body">
                                    <div class="product-name">{{ $item->name }}</div>
                                    <div class="product-footer">
                                        <span class="product-price">₦{{ number_format($item->price, 2) }}</span>
                                        <button class="btn-sm-sage add-to-cart" data-id="{{ $item->id }}">
                                            <i class="ph ph-shopping-cart"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection

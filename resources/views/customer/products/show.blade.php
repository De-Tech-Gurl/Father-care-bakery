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
                <a class="product-review-summary" href="#reviews">
                    @if($product->reviews_count > 0)
                        <span class="product-stars" aria-label="{{ number_format((float) $product->reviews_avg_rating, 1) }} out of 5 stars">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="{{ $i <= round((float) $product->reviews_avg_rating) ? 'ph-fill ph-star star-filled' : 'ph ph-star star-empty' }}"></i>
                            @endfor
                        </span>
                        <strong>{{ number_format((float) $product->reviews_avg_rating, 1) }} / 5</strong>
                        <span>({{ $product->reviews_count }} {{ \Illuminate\Support\Str::plural('review', $product->reviews_count) }})</span>
                    @else
                        <span>No reviews yet</span>
                    @endif
                </a>
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

        <section class="product-reviews mt-5 pt-4" id="reviews">
            <h2 class="section-title mb-4">Customer reviews</h2>

            @if(session('status'))
                <div class="review-status" role="status">{{ session('status') }}</div>
            @endif

            @auth
                @if(auth()->user()->isCustomer())
                    <form method="POST" action="{{ route('customer.products.reviews.store', $product) }}" class="review-form mb-5">
                        @csrf
                        <h3>{{ $myReview ? 'Update your review' : 'Leave a review' }}</h3>

                        <label for="rating" class="form-label">Your rating</label>
                        <select id="rating" name="rating" class="form-select" required>
                            <option value="">Choose a rating</option>
                            @foreach(range(5, 1) as $rating)
                                <option value="{{ $rating }}" @selected((string) old('rating', $myReview?->rating) === (string) $rating)>
                                    {{ $rating }} {{ \Illuminate\Support\Str::plural('star', $rating) }}
                                </option>
                            @endforeach
                        </select>
                        @error('rating')
                            <p class="review-error">{{ $message }}</p>
                        @enderror

                        <label for="comment" class="form-label mt-3">Your comment</label>
                        <textarea id="comment" name="comment" class="form-control" rows="4" maxlength="2000" required>{{ old('comment', $myReview?->comment) }}</textarea>
                        @error('comment')
                            <p class="review-error">{{ $message }}</p>
                        @enderror

                        <button type="submit" class="btn-sage mt-3">
                            {{ $myReview ? 'Update review' : 'Submit review' }}
                        </button>
                    </form>
                @else
                    <p class="review-login-prompt">Only customer accounts can leave product reviews.</p>
                @endif
            @else
                <p class="review-login-prompt">
                    <a href="{{ route('login') }}">Sign in</a> to leave a rating and comment.
                </p>
            @endauth

            <div class="review-list">
                @forelse($reviews as $review)
                    <article class="review-item">
                        <div class="review-item__header">
                            <strong>{{ $review->user->name }}</strong>
                            <span class="review-item__rating" aria-label="{{ $review->rating }} out of 5 stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="{{ $i <= $review->rating ? 'ph-fill ph-star star-filled' : 'ph ph-star star-empty' }}"></i>
                                @endfor
                            </span>
                            <time datetime="{{ $review->created_at->toDateString() }}">{{ $review->created_at->format('M j, Y') }}</time>
                        </div>
                        <p>{{ $review->comment }}</p>
                    </article>
                @empty
                    <p class="text-muted">Be the first to review this product.</p>
                @endforelse
            </div>

            <div class="mt-4">{{ $reviews->links() }}</div>
        </section>

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

    @push('styles')
        <style>
            .product-review-summary {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 16px;
                color: var(--muted);
                text-decoration: none;
            }

            .product-review-summary .product-stars {
                display: inline-flex;
                gap: 2px;
                margin: 0;
            }

            .product-review-summary strong {
                color: var(--ink);
            }

            .product-reviews {
                border-top: 1px solid var(--border);
            }

            .review-form {
                max-width: 680px;
                padding: 22px;
                border: 1px solid var(--border);
                border-radius: var(--radius-lg);
                background: var(--white);
            }

            .review-form h3 {
                margin-bottom: 18px;
                font-size: 1.15rem;
            }

            .review-error {
                margin-top: 6px;
                color: #9b2c2c;
                font-size: .875rem;
            }

            .review-status {
                margin-bottom: 18px;
                padding: 12px 16px;
                border: 1px solid var(--border);
                border-radius: var(--radius-md);
                color: var(--sage-dark);
                background: var(--sage-xlight);
            }

            .review-login-prompt {
                margin-bottom: 28px;
            }

            .review-login-prompt a {
                color: var(--sage-dark);
                font-weight: 700;
            }

            .review-item {
                max-width: 800px;
                padding: 18px 0;
                border-bottom: 1px solid var(--border);
            }

            .review-item__header {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 10px;
                margin-bottom: 8px;
            }

            .review-item__header time {
                color: var(--muted);
                font-size: .8rem;
            }

            .review-item__rating {
                display: inline-flex;
                gap: 2px;
            }

            .review-item p {
                margin: 0;
                white-space: pre-line;
            }
        </style>
    @endpush
@endsection

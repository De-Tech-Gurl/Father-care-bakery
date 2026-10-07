@extends('layouts.customer')

@section('title', 'Father Care Bakery – Fresh Bakes Everyday')
@section('meta_description', 'Delicious cakes, bread, pastries and buns baked fresh daily with love. Order online for pickup or delivery. Father Care Bakery, Ikot Abasi, Akwa Ibom.')

@php
    $customCakeWhatsapp = 'https://wa.me/234'.ltrim((string) config('bakery.phone'), '0').'?text='.rawurlencode("Hi Father Care Bakery, I'd like to order a custom cake.");
    $heroShowcase = [
        [
            'image' => 'images/hero-cake.png',
            'label' => 'Cakes',
            'caption' => 'Celebration cakes',
            'alt' => 'Celebration cake with chocolate drip from Father Care Bakery',
        ],
        [
            'image' => 'images/hero-bread.png',
            'label' => 'Bread',
            'caption' => 'Fresh daily loaves',
            'alt' => 'Freshly baked bread from Father Care Bakery',
        ],
        [
            'image' => 'images/hero-pastries.png',
            'label' => 'Pastries',
            'caption' => 'Flaky & golden',
            'alt' => 'Flaky pastries from Father Care Bakery',
        ],
        [
            'image' => 'images/hero-buns.png',
            'label' => 'Buns',
            'caption' => 'Soft & sweet',
            'alt' => 'Soft bakery buns from Father Care Bakery',
        ],
        [
            'image' => 'images/hero-specials.png',
            'label' => 'Specials',
            'caption' => 'Pies, rolls & more',
            'alt' => 'Bakery specials from Father Care Bakery',
        ],
    ];
@endphp

@push('styles')
<style>
    .hero-copy { animation: heroCopyIn 0.75s ease both; }
    .hero-section .hero-eyebrow { margin-bottom: 10px; }
    @keyframes heroCopyIn {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: none; }
    }

    .hero-hours {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin: 0 0 18px;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--sage-dark);
    }
    .hero-hours .live-dot { margin-right: 0; }

    .hero-image-frame {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        max-width: 530px;
        min-height: 510px;
        margin: 0 auto;
        padding-bottom: 12px;
    }

    .hero-glow-aura {
        position: absolute;
        width: 470px;
        height: 470px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(107,62,31,0.22) 0%, rgba(61,43,31,0.10) 50%, transparent 72%);
        filter: blur(30px);
        animation: auraPulse 6s ease-in-out infinite alternate;
        z-index: 1;
        pointer-events: none;
    }
    @keyframes auraPulse {
        0%   { transform: scale(0.92); opacity: 0.7; }
        100% { transform: scale(1.08); opacity: 1; }
    }

    .hero-rotating-ring {
        position: absolute;
        width: 490px;
        height: 490px;
        z-index: 2;
        pointer-events: none;
        animation: ringSpin 36s linear infinite;
        opacity: 0.8;
    }
    @keyframes ringSpin {
        from { transform: rotate(0deg); }
        to   { transform: rotate(360deg); }
    }

    .hero-img-circle {
        width: 400px;
        height: 400px;
        border-radius: 50%;
        position: relative;
        z-index: 3;
        border: 6px solid #FFFFFF;
        outline: 3px solid #6B3E1F;
        box-shadow:
            0 24px 60px rgba(61,43,31,0.22),
            0 0 35px rgba(107,62,31,0.22);
        overflow: hidden;
        animation: heroCakeFloat 5.5s ease-in-out infinite;
        transition: transform 0.4s ease, box-shadow 0.4s ease;
        background: #3D2B1F;
    }
    .hero-img-circle:hover {
        box-shadow:
            0 32px 75px rgba(61,43,31,0.28),
            0 0 50px rgba(107,62,31,0.28);
    }
    .hero-slide {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        opacity: 0;
        transform: scale(1.04);
        transition: opacity 0.9s ease, transform 1.1s ease;
        pointer-events: none;
    }
    .hero-slide.is-active {
        opacity: 1;
        transform: scale(1);
        z-index: 1;
    }
    .hero-img-circle .placeholder-icon {
        position: absolute;
        inset: 0;
        z-index: 0;
    }

    @keyframes heroCakeFloat {
        0%, 100% { transform: translateY(0); }
        50%      { transform: translateY(-12px); }
    }

    .hero-showcase-label {
        position: absolute;
        left: 50%;
        bottom: 18px;
        transform: translateX(-50%);
        z-index: 4;
        background: rgba(255,255,255,0.94);
        color: #3D2B1F;
        border: 1.5px solid rgba(107,62,31,0.22);
        border-radius: 999px;
        padding: 6px 14px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        white-space: nowrap;
        box-shadow: 0 8px 20px rgba(61,43,31,0.14);
        pointer-events: none;
    }

    .hero-showcase-dots {
        position: absolute;
        left: 50%;
        bottom: -28px;
        transform: translateX(-50%);
        z-index: 6;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .hero-showcase-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        border: none;
        padding: 0;
        background: rgba(107,62,31,0.28);
        cursor: pointer;
        transition: transform 0.2s ease, background 0.2s ease;
    }
    .hero-showcase-dot.is-active {
        background: #6B3E1F;
        transform: scale(1.25);
    }
    .hero-showcase-dot:focus-visible {
        outline: 2px solid #6B3E1F;
        outline-offset: 2px;
    }

    .hero-sparkle {
        position: absolute;
        z-index: 4;
        pointer-events: none;
        color: #6B3E1F;
        font-size: 1.35rem;
        animation: sparkleTwinkle 3.2s ease-in-out infinite alternate;
    }
    .hero-sparkle-1 { top: 28px; left: 36px; animation-delay: 0s; }
    .hero-sparkle-2 { bottom: 56px; right: 18px; animation-delay: 1.4s; font-size: 1.15rem; }

    @keyframes sparkleTwinkle {
        0%   { transform: scale(0.85); opacity: 0.4; }
        100% { transform: scale(1.15); opacity: 1; }
    }

    .hero-fresh-seal {
        position: absolute;
        bottom: 52px;
        right: 15px;
        z-index: 6;
        background: linear-gradient(135deg, #6B3E1F, #3D2B1F);
        color: #FFFFFF;
        border: 2px solid #FFFFFF;
        border-radius: 999px;
        padding: 7px 15px;
        font-size: 0.73rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        display: flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 8px 24px rgba(61,43,31,0.25);
        animation: sealFloat 4.8s ease-in-out infinite;
    }
    .hero-fresh-seal i { color: #FFFFFF; font-size: 1rem; }
    @keyframes sealFloat {
        0%, 100% { transform: translateY(0); }
        50%      { transform: translateY(-7px); }
    }

    .hero-float-badge {
        position: absolute;
        z-index: 5;
        background: #FFFFFF;
        border-radius: 18px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 16px 36px rgba(61,43,31,0.14);
        border: 1.5px solid rgba(107,62,31,0.22);
    }
    .hero-float-badge .fb-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: #F5EDE6;
        border: 1px solid rgba(107,62,31,0.22);
        color: #6B3E1F;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .hero-float-badge.badge-top {
        top: 22px;
        right: -18px;
        animation: floatBadgeTop 4.6s ease-in-out infinite;
    }
    .hero-float-badge.badge-bottom {
        bottom: 38px;
        left: -18px;
        animation: floatBadgeBottom 5.2s ease-in-out infinite;
    }
    @keyframes floatBadgeTop {
        0%, 100% { transform: translateY(0); }
        50%      { transform: translateY(-8px); }
    }
    @keyframes floatBadgeBottom {
        0%, 100% { transform: translateY(0); }
        50%      { transform: translateY(8px); }
    }

    .live-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #6B3E1F;
        display: inline-block;
        margin-right: 4px;
        box-shadow: 0 0 6px #6B3E1F;
        animation: livePulse 1.8s infinite;
    }
    @keyframes livePulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%      { opacity: 0.4; transform: scale(0.7); }
    }

    @media (max-width: 991.98px) {
        .hero-image-frame { margin-top: 12px; max-width: 420px; min-height: 0; overflow: visible; }
        .hero-img-circle { width: min(320px, 72vw); height: min(320px, 72vw); }
        .hero-glow-aura { width: min(350px, 80vw); height: min(350px, 80vw); }
        .hero-rotating-ring { width: min(380px, 88vw); height: min(380px, 88vw); }
        .hero-float-badge.badge-top { right: 0; top: 8px; }
        .hero-float-badge.badge-bottom { left: 0; bottom: 8px; }
        .hero-fresh-seal { right: 8px; bottom: 12px; }
    }
    @media (max-width: 767.98px) {
        .hero-rotating-ring,
        .hero-sparkle { display: none; }
        .hero-image-frame { flex-direction: column; min-height: 0; padding-bottom: 18px; }
        .hero-float-badge,
        .hero-float-badge.badge-top,
        .hero-float-badge.badge-bottom {
            position: static;
            width: 100%;
            max-width: 360px;
            margin: 10px auto 0;
            animation: none;
        }
        .hero-fresh-seal { position: static; margin-top: 14px; align-self: center; animation: none; }
        .hero-img-circle { width: min(270px, 78vw); height: min(270px, 78vw); animation: none; }
        .hero-showcase-dots { bottom: -22px; }
        .hero-copy { animation: none; }
    }
    @media (max-width: 575.98px) {
        .hero-image-frame { min-height: auto; max-width: 100%; }
        .hero-subtitle br { display: none; }
    }
    @media (prefers-reduced-motion: reduce) {
        .hero-copy,
        .hero-glow-aura,
        .hero-rotating-ring,
        .hero-img-circle,
        .hero-sparkle,
        .hero-fresh-seal,
        .hero-float-badge,
        .live-dot {
            animation: none !important;
        }
        .hero-slide {
            transition: none !important;
            transform: none !important;
        }
    }

</style>
@endpush

@section('content')

{{-- ================================================================
     HERO SECTION
================================================================ --}}
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">

            <div class="col-lg-6 hero-copy">
                <div class="hero-eyebrow">
                    <i class="ph ph-sun"></i>
                    Baked this morning in Ikot Abasi
                </div>

                <p class="hero-hours">
                    <span class="live-dot" aria-hidden="true"></span>
                    Open today · until 8:00 PM
                </p>

                <h1 class="hero-title">
                    Warm from the oven.<br>
                    <span class="accent">Made for you.</span>
                </h1>

                <p class="hero-subtitle">
                    Cakes for celebrations, bread for the table, and combos worth sharing.
                    Order for pickup or we'll bring it warm to your door.
                </p>

                <div class="hero-cta-group">
                    <a href="{{ route('customer.products.index') }}" class="btn-sage">
                        <i class="ph ph-shopping-bag"></i>
                        Shop the bake
                        <i class="ph ph-arrow-right" style="font-size:0.75rem;"></i>
                    </a>
                    <a href="{{ $customCakeWhatsapp }}"
                       class="btn-sage-outline"
                       target="_blank"
                       rel="noopener noreferrer">
                        <i class="ph ph-whatsapp-logo"></i>
                        Order a custom cake
                    </a>
                </div>

                <div class="hero-trust">
                    <div class="trust-item">
                        <div class="trust-icon"><i class="ph ph-storefront"></i></div>
                        <div class="trust-text">
                            <strong>Pickup in minutes</strong>
                            <span>Ready when you arrive.</span>
                        </div>
                    </div>
                    <div class="trust-item">
                        <div class="trust-icon"><i class="ph ph-moped"></i></div>
                        <div class="trust-text">
                            <strong>Same-day delivery</strong>
                            <span>Order before 2 PM.</span>
                        </div>
                    </div>
                    <div class="trust-item">
                        <div class="trust-icon"><i class="ph ph-heart"></i></div>
                        <div class="trust-text">
                            <strong>Baked with care</strong>
                            <span>Fresh for every table.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 d-flex justify-content-center">
                <div class="hero-image-frame">
                    <div class="hero-glow-aura"></div>

                    <svg class="hero-rotating-ring" viewBox="0 0 500 500" aria-hidden="true">
                        <defs>
                            <path id="textRingPath" d="M 250, 250 m -218, 0 a 218,218 0 1,1 436,0 a 218,218 0 1,1 -436,0" />
                        </defs>
                        <text font-family="'Playfair Display', Georgia, serif" font-size="13" font-weight="700" letter-spacing="4px" fill="#6B3E1F">
                            <textPath href="#textRingPath">
                                FATHER CARE BAKERY · FRESH BAKES DAILY · CAKES BREAD PASTRIES · IKOT ABASI ·
                            </textPath>
                        </text>
                    </svg>

                    <i class="ph ph-sparkle hero-sparkle hero-sparkle-1" aria-hidden="true"></i>
                    <i class="ph ph-sparkle hero-sparkle hero-sparkle-2" aria-hidden="true"></i>

                    <div class="hero-img-circle" id="heroShowcase" aria-live="polite">
                        @foreach($heroShowcase as $index => $slide)
                            <img class="hero-slide {{ $index === 0 ? 'is-active' : '' }}"
                                 src="{{ asset($slide['image']) }}"
                                 alt="{{ $slide['alt'] }}"
                                 data-label="{{ $slide['label'] }}"
                                 data-caption="{{ $slide['caption'] }}"
                                 width="400"
                                 height="400"
                                 @if($index === 0) loading="eager" @else loading="lazy" @endif>
                        @endforeach
                        <div class="placeholder-icon" style="display:none; flex-direction:column; align-items:center; justify-content:center; gap:10px;">
                            <i class="ph ph-cake"></i>
                            <span style="font-size:0.9rem; font-family:var(--font-serif); color:var(--sage); opacity:0.7;">Father Care Bakery</span>
                        </div>
                        <div class="hero-showcase-label" id="heroShowcaseLabel">
                            {{ $heroShowcase[0]['label'] }} · {{ $heroShowcase[0]['caption'] }}
                        </div>
                    </div>

                    <div class="hero-showcase-dots" id="heroShowcaseDots" role="tablist" aria-label="Bakery products showcase">
                        @foreach($heroShowcase as $index => $slide)
                            <button type="button"
                                    class="hero-showcase-dot {{ $index === 0 ? 'is-active' : '' }}"
                                    data-index="{{ $index }}"
                                    role="tab"
                                    aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                                    aria-label="Show {{ $slide['label'] }}"></button>
                        @endforeach
                    </div>

                    <div class="hero-fresh-seal">
                        <i class="ph ph-flame"></i>
                        <span id="heroFreshSealText">Still warm today</span>
                    </div>

                    <div class="hero-float-badge badge-top">
                        <div class="fb-icon">
                            <i class="ph ph-house"></i>
                        </div>
                        <div>
                            <strong style="font-size:0.9rem; color:var(--ink); line-height:1.2; display:block;">Loved in Ikot Abasi</strong>
                            <span style="font-size:0.72rem; color:var(--muted); font-weight:500;">Families keep coming back</span>
                        </div>
                    </div>

                    <div class="hero-float-badge badge-bottom">
                        <div class="fb-icon">
                            <i class="ph ph-moped"></i>
                        </div>
                        <div>
                            <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                                <strong style="font-size:0.86rem; color:var(--ink); line-height:1;">Same-day delivery</strong>
                                <span style="font-size:0.62rem; font-weight:700; color:#FFFFFF; background:#6B3E1F; padding:2px 7px; border-radius:99px; display:inline-flex; align-items:center;">
                                    <span class="live-dot"></span> Open
                                </span>
                            </div>
                            <div style="font-size:0.72rem; color:var(--muted); font-weight:500; margin-top:3px;">
                                Order before 2 PM · warm to your door
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


{{-- ================================================================
     MAIN CONTENT BLOCK: Best Sellers + Combo Deals (Side by Side)
================================================================ --}}
<section class="py-5 bg-cream" id="about">
    <div class="container">
        <div class="row g-5">

            {{-- ---- LEFT COL: Best Sellers ---- --}}
            <div class="col-lg-7">
                <div class="section-header-row">
                    <div>
                        <div class="section-eyebrow">
                            <i class="ph ph-crown"></i> Best Sellers
                        </div>
                        <h2 class="section-title">Our Best Sellers <span style="color:var(--blush-dark); font-size:0.7em;">♡</span></h2>
                        <p class="section-subtitle">Customer favorites you'll love.</p>
                    </div>
                    <a href="{{ route('customer.products.index') }}" class="section-link">
                        VIEW ALL <i class="ph ph-arrow-right"></i>
                    </a>
                </div>

                <div class="row g-3">
                    @forelse($featured as $product)
                        <div class="col-6 col-sm-4">
                            <div class="product-card">
                                <div class="product-img-wrap">
                                    @if($product->imageUrl)
                                        <img src="{{ $product->imageUrl }}" alt="{{ $product->name }}">
                                    @else
                                        <div class="product-img-placeholder">
                                            <i class="ph ph-cake"></i>
                                        </div>
                                    @endif
                                    <div class="product-badge-wrap">
                                        <span class="badge-bestseller">Best Seller</span>
                                    </div>
                                </div>
                                <div class="product-body">
                                    <div class="product-name">
                                        <a href="{{ route('customer.products.show', $product) }}" class="text-decoration-none" style="color:inherit;">{{ $product->name }}</a>
                                    </div>
                                    <p class="product-desc">{{ \Illuminate\Support\Str::limit($product->description, 48) }}</p>

                                    <div class="product-stars">
                                        @if($product->reviews_count > 0)
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="{{ $i <= round((float) $product->reviews_avg_rating) ? 'ph-fill ph-star star-filled' : 'ph ph-star star-empty' }}"></i>
                                            @endfor
                                            <span class="star-count" aria-label="{{ number_format((float) $product->reviews_avg_rating, 1) }} out of 5 stars">
                                                {{ number_format((float) $product->reviews_avg_rating, 1) }} ({{ $product->reviews_count }})
                                            </span>
                                        @else
                                            <span class="star-count">No reviews yet</span>
                                        @endif
                                    </div>

                                    <div class="product-footer">
                                        <span class="product-price">₦{{ number_format($product->price, 2) }}</span>
                                        <button class="btn-sm-sage add-to-cart"
                                                data-id="{{ $product->id }}"
                                                data-name="{{ $product->name }}"
                                                data-price="{{ $product->price }}">
                                            <i class="ph ph-shopping-cart"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="empty-state">
                                <i class="ph ph-cake d-block"></i>
                                <p>No featured products yet. Please seed the database.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ---- RIGHT COL: Combo Deals ---- --}}
            <div class="col-lg-5" id="combos">
                <div class="section-header-row">
                    <div>
                        <div class="section-eyebrow">
                            <i class="ph ph-tag"></i> Save More
                        </div>
                        <h2 class="section-title" style="font-size:1.6rem;">Combo Deals</h2>
                        <p class="section-subtitle">Save more with our sweet combos.</p>
                    </div>
                    @if($combosCategory)
                        <a href="{{ route('customer.products.index', ['category' => $combosCategory->slug]) }}" class="section-link">
                            VIEW ALL <i class="ph ph-arrow-right"></i>
                        </a>
                    @endif
                </div>

                @forelse($combos as $combo)
                    <div class="combo-card">
                        <a href="{{ route('customer.products.show', $combo) }}" class="combo-img-link">
                            @if($combo->imageUrl)
                                <img class="combo-img" src="{{ $combo->imageUrl }}" alt="{{ $combo->name }}">
                            @else
                                <div class="combo-img-placeholder">
                                    <i class="ph ph-package"></i>
                                </div>
                            @endif
                        </a>
                        <div class="combo-body">
                            <div class="combo-name">
                                <a href="{{ route('customer.products.show', $combo) }}" class="text-decoration-none" style="color:inherit;">{{ $combo->name }}</a>
                            </div>
                            @if($combo->descriptionLines)
                                <ul class="combo-items-list">
                                    @foreach($combo->descriptionLines as $line)
                                        <li>• {{ $line }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            <div class="combo-price-row">
                                <span class="combo-price">₦{{ number_format($combo->price, 0) }}</span>
                                @if($combo->compare_at_price && $combo->compare_at_price > $combo->price)
                                    <span class="combo-was">₦{{ number_format($combo->compare_at_price, 0) }}</span>
                                @endif
                                @if($combo->savingsPercent)
                                    <span class="combo-save">SAVE {{ $combo->savingsPercent }}%</span>
                                @endif
                                <button type="button"
                                        class="btn-sm-sage combo-add add-to-cart"
                                        data-id="{{ $combo->id }}"
                                        data-name="{{ $combo->name }}"
                                        data-price="{{ $combo->price }}"
                                        @disabled($combo->stock_quantity < 1)
                                        aria-label="Add {{ $combo->name }} to cart">
                                    <i class="ph ph-shopping-cart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="ph ph-package d-block"></i>
                        <p>No combo deals yet. Check back soon.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</section>


{{-- ================================================================
     MORE SWEET CHOICES (LATEST PRODUCTS)
================================================================ --}}
<section class="py-5 bg-sage-xlight">
    <div class="container">
        <div class="section-header-row">
            <div>
                <div class="section-eyebrow">
                    <i class="ph ph-clock"></i> Fresh Arrivals
                </div>
                <h2 class="section-title">More Sweet Choices <span style="color:var(--blush-dark); font-size:0.7em;">♡</span></h2>
                <p class="section-subtitle">Explore our freshly baked collection.</p>
            </div>
            <a href="{{ route('customer.products.index') }}" class="section-link">
                VIEW ALL <i class="ph ph-arrow-right"></i>
            </a>
        </div>

        <div class="row g-3">
            @forelse($latest as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="product-card">
                        <div class="product-img-wrap">
                            @if($product->imageUrl)
                                <img src="{{ $product->imageUrl }}" alt="{{ $product->name }}">
                            @else
                                <div class="product-img-placeholder">
                                    <i class="ph ph-cookie"></i>
                                </div>
                            @endif
                            <div class="product-badge-wrap">
                                <span class="badge-new">New</span>
                            </div>
                        </div>
                        <div class="product-body">
                            <div class="product-name">
                                <a href="{{ route('customer.products.show', $product) }}" class="text-decoration-none" style="color:inherit;">{{ $product->name }}</a>
                            </div>
                            <p class="product-desc">{{ \Illuminate\Support\Str::limit($product->description, 48) }}</p>

                            <div class="product-stars">
                                @if($product->reviews_count > 0)
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="{{ $i <= round((float) $product->reviews_avg_rating) ? 'ph-fill ph-star star-filled' : 'ph ph-star star-empty' }}"></i>
                                    @endfor
                                    <span class="star-count" aria-label="{{ number_format((float) $product->reviews_avg_rating, 1) }} out of 5 stars">
                                        {{ number_format((float) $product->reviews_avg_rating, 1) }} ({{ $product->reviews_count }})
                                    </span>
                                @else
                                    <span class="star-count">No reviews yet</span>
                                @endif
                            </div>

                            <div class="product-footer">
                                <span class="product-price">₦{{ number_format($product->price, 2) }}</span>
                                <button class="btn-sm-sage add-to-cart"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-price="{{ $product->price }}">
                                    <i class="ph ph-shopping-cart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-state">
                        <i class="ph ph-cookie d-block"></i>
                        <p>No products available yet. Please run the seeder.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>


{{-- ================================================================
     ORDER STEPS + CTA BLOCK
================================================================ --}}
<section class="how-works-section" id="story">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="mb-4 how-works-header">
                    <div class="section-eyebrow">
                        <i class="ph ph-check-circle"></i> How It Works
                    </div>
                    <h2 class="how-works-title">Order &amp; Enjoy<br>in Easy Steps</h2>
                </div>

                <div class="how-works-grid">
                    <div class="how-step-card">
                        <span class="step-chip">01</span>
                        <div class="how-step-icon step-one">
                            <i class="ph ph-cake"></i>
                        </div>
                        <div class="how-step-body">
                            <h3>Choose Your Cake</h3>
                            <p>Select your favorite cake and customize it.</p>
                        </div>
                    </div>

                    <div class="how-step-card">
                        <span class="step-chip">02</span>
                        <div class="how-step-icon step-two">
                            <i class="ph ph-calendar-check"></i>
                        </div>
                        <div class="how-step-body">
                            <h3>Pick Date &amp; Time</h3>
                            <p>Choose your preferred delivery date and time.</p>
                        </div>
                    </div>

                    <div class="how-step-card">
                        <span class="step-chip">03</span>
                        <div class="how-step-icon step-three">
                            <i class="ph ph-truck"></i>
                        </div>
                        <div class="how-step-body">
                            <h3>Freshly Delivered</h3>
                            <p>Freshly made and delivered to your doorstep.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="how-order-cta">
                    <div class="cta-tag">Freshly baked</div>
                    <div class="cta-decor one"></div>
                    <div class="cta-decor two"></div>

                    <h3>Ready to Order?</h3>
                    <p>Browse our full collection of freshly baked delights and place your order in minutes.</p>
                    <a href="{{ route('customer.products.index') }}" class="cta-order-btn">
                        Order now <i class="ph ph-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    .how-works-section {
        padding: 4.5rem 0 4rem;
        background: rgba(248, 242, 236, 0.45);
    }

    .how-works-header {
        margin-bottom: 2rem;
    }

    .how-works-title {
        font-family: var(--font-serif);
        font-size: clamp(2.7rem, 4vw, 4rem);
        line-height: 0.95;
        margin: 0.8rem 0 0;
        color: var(--ink);
        letter-spacing: -0.04em;
    }

    .how-works-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1.5rem;
    }

    .how-step-card {
        position: relative;
        background: rgba(255,255,255,0.45);
        border: 1px solid rgba(107,62,31,0.12);
        border-radius: 24px;
        padding: 1.1rem 1.1rem 1.3rem;
        min-height: 300px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        text-align: center;
        box-shadow: 0 12px 24px rgba(61,43,31,0.03);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .how-step-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 36px rgba(61,43,31,0.06);
    }

    .step-chip {
        align-self: flex-start;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.5rem;
        height: 2.1rem;
        padding: 0 0.6rem;
        border-radius: 999px;
        background: rgba(107,62,31,0.08);
        color: var(--ink);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        margin-bottom: 0.8rem;
    }

    .how-step-icon {
        width: 108px;
        height: 108px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.1rem;
        color: var(--ink);
        margin: 0 auto 1rem;
        border: 1px solid rgba(107,62,31,0.12);
    }

    .step-one { background: rgba(232, 226, 220, 0.88); }
    .step-two { background: rgba(241, 232, 224, 0.86); }
    .step-three { background: rgba(234, 227, 220, 0.9); }

    .how-step-body {
        width: 100%;
    }

    .how-step-body h3 {
        margin: 0 0 0.7rem;
        font-size: clamp(1.3rem, 1.8vw, 1.8rem);
        line-height: 1.2;
        color: var(--ink);
        font-weight: 700;
    }

    .how-step-body p {
        margin: 0;
        color: var(--muted);
        font-size: 0.96rem;
        line-height: 1.7;
    }

    .how-order-cta {
        position: relative;
        background: linear-gradient(145deg, #3d2b1f, #6b3e1f 58%, #8b5a3c 140%);
        border-radius: 30px;
        min-height: 330px;
        padding: 2rem 2rem 2.2rem;
        box-shadow: 0 22px 54px rgba(61,43,31,0.14);
        color: #fff;
        overflow: hidden;
    }

    .cta-tag {
        position: relative;
        z-index: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.45rem 0.8rem;
        border-radius: 999px;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.14);
        color: rgba(255,255,255,0.9);
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        margin-bottom: 1rem;
    }

    .cta-decor {
        position: absolute;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
        pointer-events: none;
    }

    .cta-decor.one {
        width: 220px;
        height: 220px;
        top: -72px;
        right: -68px;
    }

    .cta-decor.two {
        width: 170px;
        height: 170px;
        left: -40px;
        bottom: -58px;
        background: rgba(255,255,255,0.04);
    }

    .how-order-cta h3 {
        position: relative;
        z-index: 1;
        margin: 0 0 0.9rem;
        color: #fff;
        font-family: var(--font-serif);
        font-size: clamp(2rem, 3vw, 2.8rem);
        line-height: 1.1;
    }

    .how-order-cta p {
        position: relative;
        z-index: 1;
        margin: 0 0 2rem;
        color: rgba(255,255,255,0.84);
        font-size: 1rem;
        line-height: 1.7;
        max-width: 340px;
    }

    .cta-order-btn {
        position: relative;
        z-index: 1;
        display: inline-flex;
        align-items: center;
        gap: 0.7rem;
        background: #fff;
        color: var(--sage-dark);
        border: none;
        border-radius: 14px;
        padding: 1rem 1.5rem;
        font-weight: 700;
        font-size: 0.96rem;
        text-decoration: none;
        box-shadow: 0 14px 26px rgba(0,0,0,0.12);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .cta-order-btn:hover {
        text-decoration: none;
        color: var(--sage-dark);
        transform: translateY(-2px);
        box-shadow: 0 18px 30px rgba(0,0,0,0.18);
    }

    @media (max-width: 991.98px) {
        .how-works-grid {
            grid-template-columns: 1fr;
        }

        .how-step-card {
            min-height: auto;
        }
    }

    @media (max-width: 767.98px) {
        .how-works-section {
            padding-top: 3.5rem;
            padding-bottom: 3rem;
        }

        .how-order-cta {
            min-height: 260px;
            padding: 1.5rem 1.3rem;
        }

        .how-step-icon {
            width: 104px;
            height: 104px;
        }
    }
</style>
@endpush

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ---- Hero product showcase ---- */
    const showcase = document.getElementById('heroShowcase');
    const slides = showcase ? Array.from(showcase.querySelectorAll('.hero-slide')) : [];
    const dots = Array.from(document.querySelectorAll('#heroShowcaseDots .hero-showcase-dot'));
    const labelEl = document.getElementById('heroShowcaseLabel');
    const sealEl = document.getElementById('heroFreshSealText');
    let currentSlide = 0;
    let showcaseTimer = null;
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function showSlide(index) {
        if (!slides.length) return;
        currentSlide = (index + slides.length) % slides.length;

        slides.forEach((slide, i) => {
            slide.classList.toggle('is-active', i === currentSlide);
        });

        dots.forEach((dot, i) => {
            const active = i === currentSlide;
            dot.classList.toggle('is-active', active);
            dot.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        const active = slides[currentSlide];
        if (labelEl && active) {
            labelEl.textContent = active.dataset.label + ' · ' + active.dataset.caption;
        }
        if (sealEl && active) {
            sealEl.textContent = active.dataset.label + ' · fresh today';
        }
    }

    function startShowcase() {
        if (reduceMotion || slides.length < 2) return;
        stopShowcase();
        showcaseTimer = window.setInterval(() => showSlide(currentSlide + 1), 3800);
    }

    function stopShowcase() {
        if (showcaseTimer) {
            window.clearInterval(showcaseTimer);
            showcaseTimer = null;
        }
    }

    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            showSlide(Number(dot.dataset.index));
            startShowcase();
        });
    });

    if (showcase) {
        showcase.addEventListener('mouseenter', stopShowcase);
        showcase.addEventListener('mouseleave', startShowcase);
        showcase.addEventListener('focusin', stopShowcase);
        showcase.addEventListener('focusout', startShowcase);
    }

    showSlide(0);
    startShowcase();

    /* ---- Scroll-in animations ---- */
    const observer = new window.IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.product-card, .combo-card, .step-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(el);
    });

});
</script>
@endpush
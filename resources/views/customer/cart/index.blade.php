@extends('layouts.customer')

@section('title', 'My Cart - Father Care Bakery')

@section('content')
    <div class="cart-page-shell">
        <div class="container py-5">
            <div class="cart-page-heading">
                <div class="cart-hero-copy">
                    <div class="section-eyebrow"><i class="ph ph-shopping-cart"></i> Your basket</div>
                    <h1 class="cart-page-title">A little joy,<br><span>almost ready.</span></h1>
                    <p class="cart-page-intro">Your fresh bakery picks are waiting. Check your basket, then let us prepare something special for you.</p>
                    <div class="cart-hero-meta"><span><i class="ph ph-check-circle"></i> Freshly prepared</span><span><i class="ph ph-shield-check"></i> Secure checkout</span></div>
                </div>
                <div class="cart-hero-art" aria-hidden="true">
                    <div class="cart-hero-art-glow"></div>
                    <img src="{{ asset('images/hero-bread.png') }}" alt="">
                    <span class="cart-hero-count">{{ $lines->sum('quantity') }} <small>{{ \Illuminate\Support\Str::plural('item', $lines->sum('quantity')) }}</small></span>
                </div>
                <a href="{{ route('customer.products.index') }}" class="cart-continue-link"><i class="ph ph-arrow-left"></i> Continue shopping</a>
            </div>

            @if($lines->isEmpty())
                <div class="empty-cart-state">
                    <div class="empty-cart-icon">
                        <i class="ph ph-bag"></i>
                    </div>
                    <h2>Your cart is empty</h2>
                    <p>Pick a few fresh favourites and come back when you’re ready to checkout.</p>
                    <a href="{{ route('customer.products.index') }}" class="btn-sage">
                        <i class="ph ph-storefront"></i>
                        Explore bakery picks
                    </a>
                </div>
            @else
                <div class="cart-reference-layout">
                    <div class="cart-items-panel">
                        <div class="panel-header">
                            <div><span class="panel-kicker">Order review</span><h2>{{ $lines->sum('quantity') }} {{ \Illuminate\Support\Str::plural('item', $lines->sum('quantity')) }}</h2></div>
                            <span class="panel-note">Freshly prepared with care</span>
                        </div>

                        @foreach($lines as $line)
                            <article class="cart-item-row" data-stock-quantity="{{ $line['product']->stock_quantity }}">
                                <div class="cart-item-product">
                                    <div class="cart-item-thumb">
                                        @if($line['product']->imageUrl)
                                            <img src="{{ $line['product']->imageUrl }}" alt="{{ $line['product']->name }}">
                                        @else
                                            <div class="cart-item-placeholder">
                                                <i class="ph ph-cake"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="cart-item-copy">
                                        <div class="cart-item-name">{{ $line['product']->name }}</div>
                                        <div class="cart-item-category">Fresh bakery pick</div>
                                        <div class="cart-item-meta">{{ $line['product']->stock_quantity }} available today</div>
                                        <div class="cart-item-price-wrap">
                                            <span class="cart-item-price">₦{{ number_format($line['product']->price, 2) }}</span>
                                            <span class="cart-item-unit">per item</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="cart-item-qty">
                                    <form method="POST" action="{{ route('customer.cart.update', $line['product']) }}" class="cart-qty-form" data-cart-update>
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" name="quantity" value="{{ $line['quantity'] - 1 }}" aria-label="Decrease quantity" data-cart-decrease>
                                            <i class="ph ph-minus"></i>
                                        </button>
                                        <span data-cart-quantity>{{ $line['quantity'] }}</span>
                                        <button type="submit" name="quantity" value="{{ $line['quantity'] + 1 }}" aria-label="Increase quantity" data-cart-increase @disabled($line['quantity'] >= $line['product']->stock_quantity)>
                                            <i class="ph ph-plus"></i>
                                        </button>
                                    </form>
                                </div>

                                <div class="cart-item-actions">
                                    <form method="POST" action="{{ route('customer.cart.destroy', $line['product']) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="cart-remove-link">Remove</button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <aside class="cart-summary-panel">
                        <!-- <div class="coupon-box">
                            <input type="text" placeholder="Coupon code" aria-label="Coupon code">
                            <button type="button">Apply now</button>
                        </div> -->

                        <div class="summary-card">
                            <h3>Your Order</h3>

                            <div class="summary-row">
                                <span>Subtotal <small>({{ $lines->sum('quantity') }} items)</small></span>
                                <strong data-cart-subtotal>₦{{ number_format($subtotal, 2) }}</strong>
                            </div>

                            <div class="summary-row summary-info-row"><span>Delivery</span><strong>Selected at checkout</strong></div>
                            <div class="summary-row summary-info-row"><span>Payment</span><strong>Secure checkout</strong></div>

                            <div class="summary-divider"></div>

                            <div class="summary-total-row">
                                <span>Order subtotal</span>
                                <strong data-cart-total>₦{{ number_format($subtotal, 2) }}</strong>
                            </div>

                            <a href="{{ route('customer.checkout.create') }}" class="checkout-button">Continue to checkout <i class="ph ph-arrow-right"></i></a>
                            <p class="summary-reassurance"><i class="ph ph-lock-key"></i> Delivery and payment details are confirmed next.</p>
                        </div>
                    </aside>
                </div>
            @endif
        </div>
    </div>

    @push('styles')
    <style>
        .cart-reference-shell {
            background: #f3e9e3;
            min-height: calc(100vh - 120px);
            padding-bottom: 2rem;
        }

        .cart-stepper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2.5rem;
            color: #999;
            font-size: 0.88rem;
            letter-spacing: 0.02em;
            margin-bottom: 2rem;
            position: relative;
        }

        .cart-stepper span {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .cart-stepper span:not(:last-child)::after {
            content: "";
            width: 34px;
            height: 1px;
            background: rgba(16, 16, 16, 0.15);
            display: inline-block;
            margin-left: 1.1rem;
        }

        .cart-stepper .is-active {
            color: #111;
            font-weight: 600;
        }

        .cart-reference-title {
            font-size: clamp(2.2rem, 4vw, 3rem);
            margin-bottom: 2rem;
            letter-spacing: -0.04em;
        }

        .cart-reference-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.7fr) minmax(280px, 0.82fr);
            gap: 2rem;
            align-items: start;
        }

        .cart-items-panel {
            background: rgba(255,255,255,0.35);
            border: 1px solid rgba(17,17,17,0.08);
            border-radius: 0;
            padding: 1.5rem 1.5rem 0.5rem;
        }

        .panel-header {
            border-bottom: 1px solid rgba(17,17,17,0.1);
            padding-bottom: 1rem;
            margin-bottom: 0.8rem;
        }

        .panel-header h2 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .cart-item-row {
            display: grid;
            grid-template-columns: minmax(0, 1.8fr) 0.7fr 0.45fr;
            align-items: center;
            gap: 1rem;
            padding: 1.2rem 0;
            border-bottom: 1px solid rgba(17,17,17,0.1);
        }

        .cart-item-product {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .cart-item-thumb {
            width: 82px;
            height: 82px;
            flex-shrink: 0;
            border-radius: 10px;
            overflow: hidden;
            background: #f3efe9;
            border: 1px solid rgba(17,17,17,0.08);
        }

        .cart-item-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cart-item-placeholder {
            width: 100%;
            height: 100%;
            display: grid;
            place-items: center;
            color: var(--sage);
            font-size: 2rem;
        }

        .cart-item-copy {
            min-width: 0;
        }

        .cart-item-name {
            font-size: 1.08rem;
            font-weight: 600;
            color: #111;
            margin-bottom: 0.18rem;
        }

        .cart-item-category {
            font-size: 0.88rem;
            color: #666;
        }

        .cart-item-meta {
            margin-top: 0.2rem;
            font-size: 0.78rem;
            color: #666;
        }

        .cart-item-price-wrap {
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.55rem;
            flex-wrap: wrap;
        }

        .cart-item-price-old {
            font-size: 0.85rem;
            color: #8a8a8a;
            text-decoration: line-through;
        }

        .cart-item-price {
            font-size: 1rem;
            font-weight: 700;
            color: #111;
        }

        .cart-item-discount {
            color: #b3261e;
            font-weight: 700;
            font-size: 0.78rem;
        }

        .cart-item-qty {
            display: flex;
            justify-content: center;
        }

        .cart-qty-form {
            display: inline-flex;
            align-items: center;
            border: 1px solid rgba(17,17,17,0.15);
            background: #fff;
            border-radius: 0;
            overflow: hidden;
        }

        .cart-qty-form button {
            width: 34px;
            height: 34px;
            border: none;
            background: #1a1a1a;
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 1rem;
        }

        .cart-qty-form button:disabled {
            opacity: 0.55;
            cursor: not-allowed;
        }

        .cart-qty-form span {
            min-width: 26px;
            text-align: center;
            font-weight: 700;
            color: #111;
            font-size: 0.95rem;
        }

        .cart-item-actions {
            display: flex;
            justify-content: flex-end;
        }

        .cart-remove-link {
            border: none;
            background: transparent;
            color: #2d2d2d;
            font-size: 0.8rem;
            text-decoration: none;
            padding: 0.2rem 0;
        }

        .cart-summary-panel {
            background: rgba(255,255,255,0.3);
            border: 1px solid rgba(17,17,17,0.08);
            padding: 1.15rem 1.1rem 1.2rem;
            position: sticky;
            top: 1rem;
        }

        .coupon-box {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid rgba(17,17,17,0.15);
            background: #fff;
            padding: 0.2rem 0.2rem 0.2rem 0.8rem;
            margin-bottom: 1rem;
        }

        .coupon-box input {
            flex: 1;
            border: none;
            background: transparent;
            font-size: 0.9rem;
            padding: 0.7rem 0;
            outline: none;
        }

        .coupon-box button {
            border: none;
            background: #121212;
            color: #fff;
            padding: 0.72rem 0.9rem;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            cursor: pointer;
        }

        .summary-card {
            background: rgba(255,255,255,0.6);
            border: 1px solid rgba(17,17,17,0.08);
            padding: 1rem 1rem 0.8rem;
        }

        .summary-card h3 {
            margin: 0 0 0.8rem;
            font-size: 1.15rem;
            font-weight: 700;
        }

        .summary-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            color: #3a3a3a;
            font-size: 0.9rem;
            padding: 0.25rem 0;
        }

        .summary-row strong {
            color: #111;
            font-weight: 700;
        }

        .compact-row {
            align-items: flex-start;
            padding-top: 0.4rem;
            padding-bottom: 0.2rem;
        }

        .delivery-options {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            width: 160px;
        }

        .delivery-options label {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.8rem;
            color: #333;
            justify-content: space-between;
        }

        .delivery-options input {
            margin: 0;
        }

        .tip-row {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.45rem;
            margin: 0.8rem 0 0.5rem;
        }

        .tip-pill {
            border: 1px solid rgba(17,17,17,0.12);
            background: #fff;
            padding: 0.45rem 0.25rem;
            font-size: 0.78rem;
            color: #333;
        }

        .tip-pill.active {
            border-color: #111;
            background: #111;
            color: #fff;
        }

        .line-row {
            padding-top: 0.5rem;
            padding-bottom: 0.3rem;
        }

        .summary-divider {
            border-top: 1px solid rgba(17,17,17,0.12);
            margin: 0.8rem 0 0.6rem;
        }

        .summary-total-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.35rem 0 0.85rem;
            font-size: 1rem;
            font-weight: 700;
        }

        .summary-total-row strong {
            font-size: 1.2rem;
            color: #111;
        }

        .checkout-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            border: none;
            background: #ef7d32;
            color: #fff;
            font-weight: 700;
            padding: 0.9rem 1rem;
            text-decoration: none;
            transition: opacity 0.2s ease;
            border-radius: 0;
        }

        .checkout-button:hover {
            color: #fff;
            opacity: 0.95;
            text-decoration: none;
        }

        .empty-cart-state {
            background: rgba(255,255,255,0.4);
            border: 1px solid rgba(17,17,17,0.08);
            padding: 3rem 1.5rem;
            text-align: center;
        }

        .empty-cart-icon {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: grid;
            place-items: center;
            background: rgba(107,62,31,0.08);
            color: var(--sage);
            font-size: 2.3rem;
        }

        .empty-cart-state h2 {
            margin: 0 0 0.65rem;
            font-size: clamp(2rem, 4vw, 2.8rem);
        }

        .empty-cart-state p {
            max-width: 580px;
            margin: 0 auto 1.5rem;
            color: var(--muted);
        }

        .cart-page-shell { background: linear-gradient(180deg, #fff 0%, #f9f4ef 100%); min-height: calc(100vh - 120px); }
        .cart-page-heading { display: grid; grid-template-columns: minmax(0, 1fr) minmax(230px, .42fr) auto; align-items: center; gap: 1.5rem; min-height: 260px; margin-bottom: 2rem; padding: 2.2rem 2.5rem; overflow: hidden; position: relative; border-radius: 26px; background: linear-gradient(115deg, #3d2b1f 0%, #6b3e1f 62%, #8b5a3c 100%); box-shadow: 0 20px 45px rgba(61,43,31,.15); }
        .cart-page-heading::after { content: ''; position: absolute; width: 360px; height: 360px; right: 18%; top: -220px; border: 1px solid rgba(255,255,255,.15); border-radius: 50%; pointer-events: none; }
        .cart-hero-copy { position: relative; z-index: 1; }
        .cart-hero-copy .section-eyebrow { background: rgba(255,255,255,.1); color: #f3d9c1; border-color: rgba(255,255,255,.18); }
        .cart-page-title { color: #fff; font-size: clamp(2.2rem, 4vw, 3.65rem); line-height: 1.03; letter-spacing: -.055em; margin: .5rem 0 0; }
        .cart-page-title span { color: #f0c391; font-style: italic; }
        .cart-page-intro { color: rgba(255,255,255,.72); margin: .8rem 0 1rem; max-width: 34rem; line-height: 1.6; }
        .cart-hero-meta { display: flex; flex-wrap: wrap; gap: 1rem; color: rgba(255,255,255,.8); font-size: .75rem; font-weight: 700; }
        .cart-hero-meta i { color: #f0c391; margin-right: .25rem; }
        .cart-hero-art { position: relative; z-index: 1; height: 220px; display: grid; place-items: center; }
        .cart-hero-art-glow { position: absolute; width: 190px; height: 190px; border-radius: 50%; background: rgba(255,211,161,.22); filter: blur(24px); }
        .cart-hero-art img { width: 220px; height: 220px; object-fit: contain; filter: drop-shadow(0 18px 14px rgba(35,18,8,.28)); animation: cartHeroFloat 5s ease-in-out infinite; }
        .cart-hero-count { position: absolute; right: -2px; bottom: 12px; min-width: 78px; padding: .65rem .75rem; border: 1px solid rgba(255,255,255,.2); border-radius: 14px; background: rgba(255,255,255,.12); color: #fff; text-align: center; font-size: 1.25rem; font-weight: 800; backdrop-filter: blur(8px); }
        .cart-hero-count small { display: block; color: rgba(255,255,255,.72); font-size: .65rem; font-weight: 600; }
        @keyframes cartHeroFloat { 0%, 100% { transform: translateY(0) rotate(-2deg); } 50% { transform: translateY(-8px) rotate(2deg); } }
        .cart-continue-link { position: relative; z-index: 1; align-self: start; margin-top: .25rem; color: #fff; }
        .cart-continue-link:hover { color: #f0c391; }
        .cart-continue-link { color: var(--sage); text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: .45rem; white-space: nowrap; }
        .cart-continue-link:hover { color: var(--sage-dark); }
        .cart-reference-layout { grid-template-columns: minmax(0, 1.75fr) minmax(320px, .85fr); gap: 1.5rem; }
        .cart-items-panel, .cart-summary-panel { background: #fff; border: 1px solid var(--border); border-radius: 22px; box-shadow: var(--shadow-xs); }
        .cart-items-panel { padding: 1.35rem 1.35rem .4rem; }
        .panel-header { display: flex; justify-content: space-between; align-items: end; padding-bottom: 1.05rem; margin-bottom: .1rem; border-color: var(--border); }
        .panel-header h2 { color: var(--ink); font-size: 1.2rem; margin: .3rem 0 0; }
        .panel-kicker { color: var(--sage); font-size: .7rem; font-weight: 800; text-transform: uppercase; letter-spacing: .1em; }
        .panel-note { color: var(--muted); font-size: .78rem; }
        .cart-item-row { grid-template-columns: minmax(0, 1.8fr) .7fr .45fr; padding: 1.1rem 0; border-color: var(--border); }
        .cart-item-thumb { width: 88px; height: 88px; border-radius: 15px; background: var(--sage-xlight); border-color: var(--border); }
        .cart-item-name, .cart-item-price { color: var(--ink); }
        .cart-item-category { color: var(--sage); font-weight: 700; font-size: .78rem; }
        .cart-item-meta, .cart-item-unit { color: var(--muted); }
        .cart-item-price-wrap { margin-top: .65rem; }
        .cart-item-price { font-size: 1.02rem; }
        .cart-qty-form { border: 1px solid var(--border); border-radius: 10px; }
        .cart-qty-form button { width: 30px; height: 30px; background: var(--sage-xlight); color: var(--sage); }
        .cart-qty-form span { color: var(--ink); min-width: 30px; }
        .cart-remove-link { color: var(--muted); font-weight: 700; }
        .cart-remove-link:hover { color: #9a3e35; }
        .cart-summary-panel { padding: 1rem; position: sticky; top: 1.25rem; }
        .coupon-box { border: 1px solid var(--border); border-radius: 12px; margin-bottom: 1rem; }
        .coupon-box button { background: var(--sage); border-radius: 9px; margin-right: .15rem; }
        .summary-card { background: var(--sage-xlight); border: 1px solid rgba(107,62,31,.1); border-radius: 16px; padding: 1.25rem; }
        .summary-card h3 { color: var(--ink); font-size: 1.3rem; margin-bottom: 1.1rem; }
        .summary-row { color: var(--ink-soft); padding: .5rem 0; }
        .summary-row small { color: var(--muted); }
        .summary-row strong { color: var(--ink); }
        .summary-info-row { border-top: 1px solid rgba(216,199,176,.65); font-size: .83rem; }
        .summary-info-row strong { color: var(--muted); font-size: .78rem; font-weight: 600; }
        .summary-divider { border-color: rgba(216,199,176,.9); margin-top: .9rem; }
        .summary-total-row { color: var(--ink); padding-top: .7rem; }
        .summary-total-row strong { color: var(--sage); font-size: 1.35rem; }
        .checkout-button { background: var(--sage); border-radius: 11px; gap: .5rem; padding: .95rem 1rem; }
        .checkout-button:hover { background: var(--sage-dark); opacity: 1; }
        .summary-reassurance { color: var(--muted); font-size: .72rem; text-align: center; margin: .8rem 0 0; }
        .summary-reassurance i { color: var(--sage); margin-right: .2rem; }
        .empty-cart-state { background: #fff; border: 1px solid var(--border); border-radius: 22px; box-shadow: var(--shadow-xs); }
        .empty-cart-icon { background: var(--sage-xlight); color: var(--sage); }

        @media (max-width: 991.98px) {
            .cart-reference-layout {
                grid-template-columns: 1fr;
            }
            .cart-summary-panel {
                position: static;
            }
        }

        @media (max-width: 767.98px) {
            .cart-page-heading { display: block; min-height: 0; padding: 1.6rem; }
            .cart-hero-art { height: 150px; margin: .5rem 0 0; }
            .cart-hero-art img { width: 155px; height: 155px; }
            .cart-hero-count { right: 12%; bottom: 0; }
            .cart-continue-link { display: inline-flex; margin-top: 1rem; }
            .cart-item-row {
                grid-template-columns: 1fr;
                gap: 0.85rem;
            }

            .cart-item-actions {
                justify-content: flex-start;
            }

            .cart-stepper {
                gap: 1rem;
                font-size: 0.8rem;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.querySelectorAll('[data-cart-update]').forEach((form) => {
            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                event.stopPropagation();

                const scrollPosition = window.scrollY;
                const button = event.submitter;
                const row = form.closest('.cart-item-row');
                const quantity = row.querySelector('[data-cart-quantity]');
                const increaseButton = form.querySelector('[data-cart-increase]');
                const decreaseButton = form.querySelector('[data-cart-decrease]');
                const formData = new window.FormData(form);
                formData.set('quantity', button?.value || quantity.textContent.trim());
                let availableStock = Number(row.dataset.stockQuantity);
                form.querySelectorAll('button').forEach((item) => item.disabled = true);

                try {
                    const response = await fetch(form.action, {
                        method: 'PATCH',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                        },
                        body: new window.URLSearchParams(formData)
                    });

                    if (!response.ok) throw new Error('Cart update failed');

                    const data = await response.json();
                    availableStock = Number(data.stock_quantity ?? availableStock);
                    if (data.quantity === 0) {
                        row.remove();
                    } else {
                        quantity.textContent = data.quantity;
                    }
                    document.querySelectorAll('[data-cart-subtotal], [data-cart-total]').forEach((element) => {
                        element.textContent = '₦' + Number(data.subtotal).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    });
                    if (typeof data.cart_count === 'number') syncCartCount(data.cart_count);
                    showCartToast(data.message || 'Cart updated');
                } catch (error) {
                    showCartToast('Could not update cart. Please try again.');
                } finally {
                    if (window.scrollY !== scrollPosition) window.scrollTo(0, scrollPosition);
                    if (row.isConnected) {
                        form.querySelectorAll('button').forEach((item) => item.disabled = false);
                        if (increaseButton) increaseButton.disabled = Number(quantity.textContent) >= availableStock;
                        if (decreaseButton) decreaseButton.disabled = Number(quantity.textContent) <= 0;
                    }
                }
            });
        });
    </script>
    @endpush
@endsection

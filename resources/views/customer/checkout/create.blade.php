@extends('layouts.customer')

@section('title', 'Checkout - Father Care Bakery')

@section('content')
    <div class="checkout-page-shell">
        <div class="container py-5">
            <div class="checkout-heading">
                <div>
                    <div class="section-eyebrow"><i class="ph ph-lock-key"></i> Secure checkout</div>
                    <h1>Let’s get your order ready.</h1>
                    <p>Confirm your details, choose how you’ll receive your bake, and select a payment method.</p>
                </div>
                <a href="{{ route('customer.cart.index') }}" class="checkout-back-link"><i class="ph ph-arrow-left"></i> Back to cart</a>
            </div>

            <form method="POST" action="{{ route('customer.checkout.store') }}" id="checkout-form" enctype="multipart/form-data">
                @csrf
                <div class="checkout-layout">
                    <div class="checkout-main">
                        <section class="checkout-card">
                            <div class="checkout-card-heading"><span class="checkout-step">01</span><div><h2>Your details</h2><p>We’ll use these details to contact you about this order.</p></div></div>
                            <div class="customer-profile"><span class="profile-avatar"><i class="ph ph-user"></i></span><div><strong>{{ auth()->user()->name }}</strong><span>{{ auth()->user()->email }}</span></div><span class="profile-confirmed"><i class="ph ph-check-circle"></i> Signed in</span></div>
                            <div class="checkout-fields two-columns">
                                <div><label for="phone">Phone number</label><input id="phone" type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}" class="checkout-input @error('phone') is-invalid @enderror" placeholder="e.g. 0813 950 2961" required>@error('phone')<div class="checkout-error">{{ $message }}</div>@enderror</div>
                                <div><label>Email address</label><div class="checkout-input is-readonly"><i class="ph ph-envelope"></i>{{ auth()->user()->email }}</div></div>
                            </div>
                        </section>

                        <section class="checkout-card">
                            <div class="checkout-card-heading"><span class="checkout-step">02</span><div><h2>How would you like to receive it?</h2><p>Choose pickup for the fastest collection or delivery to your door.</p></div></div>
                            <div class="delivery-choice-grid">
                                <label class="delivery-choice"><input type="radio" name="delivery_type" value="pickup" @checked(old('delivery_type', 'pickup') === 'pickup')><span class="choice-icon"><i class="ph ph-storefront"></i></span><span><strong>Pickup at the bakery</strong><small>{{ config('bakery.address') }}</small></span><i class="ph ph-check-circle choice-check"></i></label>
                                <label class="delivery-choice"><input type="radio" name="delivery_type" value="delivery" @checked(old('delivery_type') === 'delivery')><span class="choice-icon"><i class="ph ph-moped"></i></span><span><strong>Home delivery</strong><small>We’ll bring it to your address.</small></span><i class="ph ph-check-circle choice-check"></i></label>
                            </div>
                            @error('delivery_type')<div class="checkout-error">{{ $message }}</div>@enderror
                            <div id="address-wrap" class="address-panel">
                                <label for="delivery_address">Delivery address</label>
                                <textarea id="delivery_address" name="delivery_address" rows="3" class="checkout-input @error('delivery_address') is-invalid @enderror" placeholder="House number, street, area, and any helpful landmark">{{ old('delivery_address', auth()->user()->address) }}</textarea>
                                <small>Use a complete address so our rider can find you without delay.</small>
                                @error('delivery_address')<div class="checkout-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="checkout-fields notes-field"><label for="notes">Order notes <span>Optional</span></label><textarea id="notes" name="notes" rows="2" class="checkout-input" placeholder="Add a message for the bakery, such as a preferred pickup time.">{{ old('notes') }}</textarea></div>
                        </section>

                        <section class="checkout-card">
                            <div class="checkout-card-heading"><span class="checkout-step">03</span><div><h2>Payment method</h2><p>Choose how you’d like to complete this order.</p></div></div>
                            <div class="payment-grid">
                                @foreach($paymentMethods as $method)
                                    @php($disabled = $method->usesPaystack() && ! $paystackReady)
                                    <label class="payment-option {{ $disabled ? 'is-disabled' : '' }}">
                                        <input type="radio" name="payment_method" value="{{ $method->value }}" class="payment-method-input" @checked(old('payment_method', 'bank_transfer') === $method->value) @disabled($disabled) required>
                                        <span class="payment-icon"><i class="ph {{ $method->value === 'bank_transfer' ? 'ph-bank' : ($method->value === 'cash_on_delivery' ? 'ph-money' : ($method->value === 'ussd' ? 'ph-device-mobile' : 'ph-credit-card')) }}"></i></span>
                                        <span class="payment-copy"><strong>{{ $method->label() }}</strong><small>{{ $method->description() }}</small>@if($disabled)<em>Unavailable until Paystack is configured.</em>@endif</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('payment_method')<div class="checkout-error">{{ $message }}</div>@enderror
                            <div id="receipt-wrap" class="receipt-panel"><label for="payment_receipt">Transfer receipt <span>Required for bank transfer</span></label><div class="bank-details"><i class="ph ph-bank"></i><span><strong>{{ $bank['name'] }}</strong><small>{{ $bank['account_name'] }} · {{ $bank['account_number'] }}</small></span></div><input id="payment_receipt" type="file" name="payment_receipt" accept=".jpg,.jpeg,.png,.webp,.pdf,image/*,application/pdf" class="checkout-input @error('payment_receipt') is-invalid @enderror"> <small>Upload a clear JPG, PNG, WEBP, or PDF under 5MB.</small>@error('payment_receipt')<div class="checkout-error">{{ $message }}</div>@enderror</div>
                        </section>
                    </div>

                    <aside class="checkout-sidebar"><div class="checkout-summary-card"><div class="summary-top"><span class="checkout-step">04</span><div><h2>Order summary</h2><p>{{ $lines->sum('quantity') }} {{ \Illuminate\Support\Str::plural('item', $lines->sum('quantity')) }} in your basket</p></div></div><div class="checkout-items">@foreach($lines as $line)<div class="checkout-item"><div class="checkout-item-image">@if($line['product']->imageUrl)<img src="{{ $line['product']->imageUrl }}" alt="{{ $line['product']->name }}">@else<i class="ph ph-cake"></i>@endif</div><div><strong>{{ $line['product']->name }}</strong><span>Qty {{ $line['quantity'] }}</span></div><b>₦{{ number_format($line['line_total'], 2) }}</b></div>@endforeach</div><div class="checkout-total"><span>Order total</span><strong>₦{{ number_format($subtotal, 2) }}</strong></div><button type="submit" class="checkout-submit">Place my order <i class="ph ph-arrow-right"></i></button><p class="checkout-secure"><i class="ph ph-shield-check"></i> Your information is protected and only used to fulfil this order.</p></div></aside>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .checkout-page-shell { background: linear-gradient(180deg, #fff 0%, #f9f4ef 100%); min-height: calc(100vh - 120px); }
    .checkout-heading { display: flex; align-items: end; justify-content: space-between; gap: 1rem; margin-bottom: 2rem; }
    .checkout-heading h1 { color: var(--ink); font-size: clamp(2.25rem, 4vw, 3.45rem); letter-spacing: -.05em; margin: .5rem 0 .55rem; }
    .checkout-heading p { color: var(--muted); margin: 0; max-width: 39rem; }
    .checkout-back-link { color: var(--sage); font-weight: 700; text-decoration: none; white-space: nowrap; }
    .checkout-layout { display: grid; grid-template-columns: minmax(0, 1.55fr) minmax(320px, .8fr); gap: 1.5rem; align-items: start; }
    .checkout-main { display: grid; gap: 1.25rem; }
    .checkout-card, .checkout-summary-card { background: #fff; border: 1px solid var(--border); border-radius: 22px; box-shadow: var(--shadow-xs); }
    .checkout-card { padding: clamp(1.2rem, 3vw, 1.8rem); }
    .checkout-card-heading, .summary-top { display: flex; gap: .85rem; align-items: flex-start; margin-bottom: 1.35rem; }
    .checkout-step { width: 2.2rem; height: 2.2rem; display: grid; place-items: center; border-radius: 11px; flex: 0 0 auto; background: var(--sage-xlight); color: var(--sage); font-size: .72rem; font-weight: 800; letter-spacing: .05em; }
    .checkout-card h2, .summary-top h2 { color: var(--ink); font-size: 1.2rem; margin: .1rem 0 .25rem; }
    .checkout-card-heading p, .summary-top p { color: var(--muted); font-size: .82rem; margin: 0; }
    .customer-profile { display: flex; align-items: center; gap: .75rem; padding: .8rem; border-radius: 14px; background: var(--sage-xlight); margin-bottom: 1rem; }
    .profile-avatar { width: 2.35rem; height: 2.35rem; display: grid; place-items: center; border-radius: 50%; background: var(--sage); color: #fff; }
    .customer-profile strong, .customer-profile span { display: block; }
    .customer-profile strong { color: var(--ink); font-size: .9rem; }
    .customer-profile div span { color: var(--muted); font-size: .78rem; }
    .profile-confirmed { margin-left: auto; color: #39704b; font-size: .72rem; font-weight: 700; }
    .checkout-fields { display: grid; gap: 1rem; }
    .two-columns { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .checkout-fields label, .address-panel label, .receipt-panel label { display: flex; justify-content: space-between; color: var(--ink); font-size: .78rem; font-weight: 800; margin-bottom: .4rem; }
    .checkout-fields label span, .receipt-panel label span { color: var(--muted); font-weight: 500; }
    .checkout-input { display: block; width: 100%; border: 1px solid var(--border); border-radius: 11px; background: #fff; color: var(--ink); padding: .78rem .85rem; outline: none; font-size: .88rem; }
    .checkout-input:focus { border-color: var(--sage); box-shadow: 0 0 0 3px rgba(107,62,31,.08); }
    textarea.checkout-input { resize: vertical; }
    .checkout-input.is-readonly { color: var(--muted); background: #faf8f6; display: flex; align-items: center; gap: .5rem; min-height: 42px; }
    .checkout-error { color: #a13e35; font-size: .75rem; margin-top: .35rem; }
    .delivery-choice-grid, .payment-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .8rem; }
    .delivery-choice, .payment-option { position: relative; display: flex; gap: .7rem; align-items: flex-start; border: 1px solid var(--border); border-radius: 14px; padding: 1rem; cursor: pointer; background: #fff; transition: border-color .2s ease, box-shadow .2s ease, background .2s ease; }
    .delivery-choice input, .payment-option input { position: absolute; opacity: 0; }
    .delivery-choice:has(input:checked), .payment-option:has(input:checked) { border-color: var(--sage); background: var(--sage-xlight); box-shadow: 0 0 0 3px rgba(107,62,31,.07); }
    .choice-icon, .payment-icon { width: 2.35rem; height: 2.35rem; display: grid; place-items: center; border-radius: 10px; background: #f8f1eb; color: var(--sage); font-size: 1.15rem; flex: 0 0 auto; }
    .delivery-choice strong, .delivery-choice small, .payment-copy strong, .payment-copy small, .payment-copy em { display: block; }
    .delivery-choice strong, .payment-copy strong { color: var(--ink); font-size: .82rem; }
    .delivery-choice small, .payment-copy small { color: var(--muted); font-size: .72rem; line-height: 1.45; margin-top: .25rem; }
    .choice-check { display: none; margin-left: auto; color: var(--sage); }
    .delivery-choice:has(input:checked) .choice-check { display: block; }
    .address-panel, .receipt-panel { margin-top: 1rem; padding: 1rem; border-radius: 14px; background: #faf8f6; border: 1px solid var(--border); }
    .bank-details { display: flex; align-items: center; gap: .65rem; padding: .7rem; border-radius: 10px; background: var(--sage-xlight); color: var(--sage); margin-bottom: .75rem; }
    .bank-details strong, .bank-details small { display: block; }
    .bank-details strong { color: var(--ink); font-size: .76rem; }
    .bank-details small { color: var(--muted); font-size: .7rem; margin-top: .2rem; }
    .address-panel small, .receipt-panel small { display: block; color: var(--muted); font-size: .72rem; margin-top: .45rem; }
    .notes-field { margin-top: 1rem; }
    .payment-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .payment-option.is-disabled { opacity: .5; cursor: not-allowed; }
    .payment-copy em { color: #a13e35; font-size: .68rem; font-style: normal; margin-top: .35rem; }
    .checkout-sidebar { position: sticky; top: 1rem; }
    .checkout-summary-card { padding: 1.25rem; }
    .checkout-items { display: grid; gap: .85rem; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); padding: 1rem 0; }
    .checkout-item { display: grid; grid-template-columns: 2.8rem minmax(0, 1fr) auto; gap: .65rem; align-items: center; }
    .checkout-item-image { width: 2.8rem; height: 2.8rem; display: grid; place-items: center; border-radius: 10px; overflow: hidden; background: var(--sage-xlight); color: var(--sage); }
    .checkout-item-image img { width: 100%; height: 100%; object-fit: cover; }
    .checkout-item strong, .checkout-item span { display: block; }
    .checkout-item strong { color: var(--ink); font-size: .78rem; }
    .checkout-item span { color: var(--muted); font-size: .7rem; margin-top: .2rem; }
    .checkout-item b { color: var(--ink); font-size: .78rem; white-space: nowrap; }
    .checkout-total { display: flex; justify-content: space-between; align-items: center; padding: 1.1rem 0; color: var(--ink); font-weight: 700; }
    .checkout-total strong { color: var(--sage); font-size: 1.35rem; }
    .checkout-submit { display: flex; justify-content: center; align-items: center; gap: .5rem; width: 100%; border: 0; border-radius: 11px; padding: .95rem 1rem; background: var(--sage); color: #fff; font-weight: 800; }
    .checkout-submit:hover { background: var(--sage-dark); }
    .checkout-secure { color: var(--muted); text-align: center; font-size: .7rem; margin: .8rem 0 0; }
    .checkout-secure i { color: var(--sage); }
    @media (max-width: 991.98px) { .checkout-layout { grid-template-columns: 1fr; } .checkout-sidebar { position: static; } }
    @media (max-width: 767.98px) { .checkout-heading { align-items: flex-start; flex-direction: column; } .two-columns, .delivery-choice-grid, .payment-grid { grid-template-columns: 1fr; } .profile-confirmed { display: none !important; } }
</style>
@endpush

@push('scripts')
<script>
    const deliveryInputs = document.querySelectorAll('input[name="delivery_type"]');
    const addressWrap = document.getElementById('address-wrap');
    const receiptWrap = document.getElementById('receipt-wrap');
    const receiptInput = document.getElementById('payment_receipt');
    const paymentInputs = document.querySelectorAll('.payment-method-input');
    const codOption = document.querySelector('input[name="payment_method"][value="cash_on_delivery"]');

    function toggleAddress() {
        const delivery = selectedDeliveryType() === 'delivery';
        addressWrap.style.display = delivery ? 'block' : 'none';
        document.getElementById('delivery_address').required = delivery;
    }

    function selectedPaymentMethod() {
        const checked = document.querySelector('.payment-method-input:checked');
        return checked ? checked.value : null;
    }

    function toggleReceipt() {
        const bankTransfer = selectedPaymentMethod() === 'bank_transfer';
        receiptWrap.style.display = bankTransfer ? 'block' : 'none';
        receiptInput.required = bankTransfer;
        if (!bankTransfer) {
            receiptInput.value = '';
        }
    }

    deliveryInputs.forEach((input) => input.addEventListener('change', toggleAddress));
    paymentInputs.forEach((input) => input.addEventListener('change', toggleReceipt));
    toggleAddress();
    toggleReceipt();
</script>
@endpush

@extends('layouts.customer')

@section('title', 'Contact Father Care Bakery')
@section('meta_description', 'Contact Father Care Bakery for custom cakes, orders, delivery, or general inquiries in Ikot Abasi, Akwa Ibom.')

@section('content')
<section class="contact-hero">
    <div class="container">
        <div class="contact-hero-inner">
            <div class="row align-items-center g-4 g-lg-5">
                <div class="col-lg-6">
                    <div class="contact-hero-copy">
                        <div class="section-eyebrow">
                            <i class="ph ph-phone-call"></i> Contact Us
                        </div>
                        <h1 class="contact-hero-title">Let’s make your next order sweet.</h1>
                        <p class="contact-hero-subtitle">
                            Reach out for custom cakes, pickup details, delivery questions, and everyday bakery orders made with care.
                        </p>

                        <div class="contact-hero-actions">
                            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary-custom">
                                <i class="ph ph-whatsapp-logo me-1"></i>
                                WhatsApp us
                            </a>
                            <a href="tel:+234{{ ltrim(config('bakery.phone'), '0') }}" class="btn btn-outline-secondary">
                                <i class="ph ph-phone me-1"></i>
                                Call bakery
                            </a>
                        </div>

                        <div class="contact-trust-row">
                            <div class="trust-pill">
                                <i class="ph ph-clock"></i>
                                Open daily
                            </div>
                            <div class="trust-pill">
                                <i class="ph ph-moped"></i>
                                Same-day delivery
                            </div>
                            <div class="trust-pill">
                                <i class="ph ph-heart"></i>
                                Freshly baked
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="contact-hero-visual">
                        <div class="visual-glow"></div>
                        <div class="visual-card primary-card">
                            <span class="mini-badge">Fresh today</span>
                            <h3>Custom cakes</h3>
                            <p>Made for birthdays, celebrations, and everyday joy.</p>
                        </div>

                        <div class="visual-card secondary-card">
                            <div class="card-row">
                                <i class="ph ph-map-pin"></i>
                                <span>{{ config('bakery.address') }}</span>
                            </div>
                            <div class="card-row">
                                <i class="ph ph-phone"></i>
                                <span>{{ config('bakery.phone') }}</span>
                            </div>
                        </div>

                        <div class="visual-photo-wrap">
                            <img src="{{ asset('images/hero-cake.png') }}" alt="Father Care Bakery cake showcase" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="pb-5 pb-lg-6 bg-cream">
    <div class="container">

        <div class="row g-4 g-lg-5 align-items-start">
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-4 h-100" style="background: rgba(255,255,255,.86); border:1px solid rgba(107,62,31,.08);">
                    <h2 class="h4 fw-700 mb-4">Get in touch</h2>

                    <div class="d-flex flex-column gap-3">
                        <a href="tel:+234{{ ltrim(config('bakery.phone'), '0') }}" class="contact-card-link">
                            <span class="contact-icon"><i class="ph ph-phone"></i></span>
                            <span>
                                <strong>Call us</strong>
                                <small>{{ config('bakery.phone') }}</small>
                            </span>
                        </a>

                        <a href="mailto:{{ config('bakery.email') }}" class="contact-card-link">
                            <span class="contact-icon"><i class="ph ph-envelope"></i></span>
                            <span>
                                <strong>Email</strong>
                                <small>{{ config('bakery.email') }}</small>
                            </span>
                        </a>

                        <a href="https://maps.google.com/?q={{ urlencode(config('bakery.address')) }}" target="_blank" rel="noopener noreferrer" class="contact-card-link">
                            <span class="contact-icon"><i class="ph ph-map-pin"></i></span>
                            <span>
                                <strong>Visit the bakery</strong>
                                <small>{{ config('bakery.address') }}</small>
                            </span>
                        </a>

                        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="contact-card-link whatsapp-card">
                            <span class="contact-icon"><i class="ph ph-whatsapp-logo"></i></span>
                            <span>
                                <strong>WhatsApp</strong>
                                <small>Chat with us for quick orders</small>
                            </span>
                        </a>
                    </div>

                    <div class="mt-4 pt-4 border-top" style="border-color:rgba(107,62,31,.12);">
                        <h3 class="h6 fw-700 mb-3">Opening hours</h3>
                        <ul class="list-unstyled mb-0 small" style="color: var(--muted); line-height:2;">
                            <li><i class="ph ph-clock me-2"></i>{{ config('bakery.hours.weekdays') }}</li>
                            <li><i class="ph ph-clock me-2"></i>{{ config('bakery.hours.saturday') }}</li>
                            <li><i class="ph ph-clock me-2"></i>{{ config('bakery.hours.sunday') }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-5" style="background: linear-gradient(180deg, rgba(255,255,255,.92), rgba(248,242,236,.96)); border:1px solid rgba(107,62,31,.08);">
                    <h2 class="h4 fw-700 mb-4">Send a message</h2>

                    <form action="{{ $whatsappUrl }}" method="GET" target="_blank" rel="noopener noreferrer">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-600">Full name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Your name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-600">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" required>
                            </div>
                            <div class="col-12">
                                <label for="subject" class="form-label fw-600">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" placeholder="Order enquiry, custom cake, delivery question..." required>
                            </div>
                            <div class="col-12">
                                <label for="message" class="form-label fw-600">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="6" placeholder="Tell us what you need..." required></textarea>
                            </div>
                            <div class="col-12 d-flex gap-2 flex-wrap align-items-center">
                                <button type="submit" class="btn btn-primary-custom">
                                    <i class="ph ph-paper-plane-tilt me-1"></i>
                                    Send message
                                </button>
                                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary">
                                    <i class="ph ph-whatsapp-logo me-1"></i>
                                    WhatsApp chat
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: rgba(255,255,255,.88); border:1px solid rgba(107,62,31,.08);">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 px-4 pt-4 pb-3">
                        <div>
                            <div class="section-eyebrow" style="font-size: 0.72rem; margin-bottom: 4px;">
                                <i class="ph ph-map-pin"></i> Find us
                            </div>
                            <h2 class="h5 fw-700 mb-0">Bakery location</h2>
                        </div>
                        <a href="https://maps.google.com/?q={{ urlencode(config('bakery.address')) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm-sage-outline">
                            Open in Google Maps
                        </a>
                    </div>
                    <div class="map-frame-wrap">
                        <iframe
                            src="{{ $mapUrl }}"
                            width="100%"
                            height="420"
                            style="border:0; display:block;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Father Care Bakery location map">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
    .contact-hero {
        position: relative;
        padding: 3.25rem 0 2rem;
        background: radial-gradient(circle at top left, rgba(255,255,255,0.95), rgba(243,234,227,0.9) 38%, rgba(233,220,210,0.8) 100%);
    }

    .contact-hero-inner {
        background: linear-gradient(145deg, rgba(255,255,255,0.72), rgba(245,236,228,0.9));
        border: 1px solid rgba(107,62,31,.08);
        border-radius: 30px;
        padding: 2rem;
        box-shadow: 0 28px 60px rgba(61,43,31,.08);
    }

    .contact-hero-copy {
        padding: 0.5rem 0;
    }

    .contact-hero-title {
        font-family: var(--font-serif);
        font-size: clamp(2.5rem, 4vw, 4.2rem);
        line-height: 1.05;
        color: var(--ink);
        margin: 0.8rem 0 1rem;
    }

    .contact-hero-subtitle {
        font-size: 1.04rem;
        color: var(--muted);
        line-height: 1.8;
        margin: 0;
        max-width: 560px;
    }

    .contact-hero-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.9rem;
        margin-top: 1.5rem;
    }

    .contact-trust-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: 1.6rem;
    }

    .trust-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        background: rgba(107,62,31,.06);
        color: var(--sage-dark);
        border: 1px solid rgba(107,62,31,.1);
        border-radius: 999px;
        padding: 0.55rem 0.85rem;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .contact-hero-visual {
        position: relative;
        min-height: 420px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem 0;
    }

    .visual-glow {
        position: absolute;
        width: 88%;
        height: 88%;
        border-radius: 32px;
        background: radial-gradient(circle, rgba(175,136,105,.24), rgba(107,62,31,.08) 45%, transparent 72%);
        filter: blur(22px);
    }

    .visual-photo-wrap {
        position: relative;
        z-index: 2;
        width: min(88%, 430px);
        border-radius: 30px;
        overflow: hidden;
        box-shadow: 0 24px 48px rgba(61,43,31,.14);
        border: 6px solid rgba(255,255,255,.9);
        background: #f5ede6;
    }

    .visual-photo-wrap img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .visual-card {
        position: absolute;
        z-index: 3;
        border-radius: 22px;
        background: rgba(255,255,255,.94);
        border: 1px solid rgba(107,62,31,.09);
        box-shadow: 0 18px 32px rgba(61,43,31,.08);
        backdrop-filter: blur(6px);
    }

    .primary-card {
        left: 10px;
        top: 52px;
        padding: 1rem 1.2rem;
        max-width: 210px;
    }

    .mini-badge {
        display: inline-block;
        padding: 0.38rem 0.7rem;
        border-radius: 999px;
        background: rgba(107,62,31,.08);
        color: var(--sage-dark);
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .primary-card h3 {
        font-family: var(--font-serif);
        font-size: 1.55rem;
        margin: 0.8rem 0 0.45rem;
        color: var(--ink);
    }

    .primary-card p {
        color: var(--muted);
        margin: 0;
        font-size: 0.76rem;
        line-height: 1.6;
    }

    .secondary-card {
        right: 8px;
        bottom: 28px;
        padding: 1rem 1.05rem;
        width: min(100%, 260px);
    }

    .card-row {
        display: flex;
        align-items: flex-start;
        gap: 0.7rem;
        font-size: 0.78rem;
        color: var(--ink);
        line-height: 1.5;
    }

    .card-row + .card-row {
        margin-top: 0.8rem;
    }

    .card-row i {
        color: var(--sage-dark);
        font-size: 1rem;
        margin-top: 0.15rem;
    }

    .contact-card-link {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 18px 18px;
        border-radius: 18px;
        background: #fff;
        border: 1px solid rgba(107,62,31,.09);
        box-shadow: 0 10px 24px rgba(61,43,31,.04);
        color: var(--ink);
        text-decoration: none;
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }

    .contact-card-link:hover {
        text-decoration: none;
        color: var(--ink);
        transform: translateY(-2px);
        border-color: rgba(107,62,31,.18);
        box-shadow: 0 12px 26px rgba(61,43,31,.08);
    }

    .contact-card-link strong,
    .contact-card-link small {
        display: block;
    }

    .contact-card-link small {
        color: var(--muted);
        margin-top: 4px;
        line-height: 1.5;
    }

    .contact-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(107,62,31,.08);
        color: var(--sage-dark);
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .whatsapp-card .contact-icon {
        background: rgba(37, 211, 102, .12);
        color: #1f9d4a;
    }

    .map-frame-wrap {
        position: relative;
        width: 100%;
        border-top: 1px solid rgba(107,62,31,.08);
        background: #f8f1eb;
    }

    @media (max-width: 991.98px) {
        .contact-hero-inner {
            padding: 1.5rem;
        }

        .contact-hero-visual {
            min-height: 360px;
        }
    }

    @media (max-width: 767.98px) {
        .contact-hero {
            padding-top: 2.5rem;
        }

        .contact-hero-inner {
            padding: 1.15rem;
            border-radius: 22px;
        }

        .contact-card-link {
            padding: 15px 14px;
        }

        .contact-hero-visual {
            min-height: 300px;
        }

        .primary-card {
            left: 4px;
            top: 12px;
            max-width: 170px;
        }

        .secondary-card {
            right: 4px;
            bottom: 8px;
            width: 210px;
        }
    }
</style>
@endpush
@endsection

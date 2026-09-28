<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Father Care Bakery – Fresh Bakes Everyday')</title>
    <meta name="description" content="@yield('meta_description', 'Father Care Bakery — Delicious bread, cakes, pastries and buns baked fresh daily with love. Order online for pickup or delivery in Ikot Abasi, Akwa Ibom.')">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Phosphor Icons (outlined) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2/src/regular/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2/src/fill/style.css">

    <!-- Google Fonts: Playfair Display + Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,800;1,400;1,600&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* ================================================================
           DESIGN TOKENS
        ================================================================ */
        :root {
            /* Palette — white and chocolate brown only */
            --sage:          #6B3E1F;
            --sage-dark:     #3D2B1F;
            --sage-mid:      #8B5A3C;
            --sage-light:    #E8D5C4;
            --sage-xlight:   #F5EDE6;
            --cream:         #FFFFFF;
            --cream-dark:    #F5EDE6;
            --blush:         #6B3E1F;
            --blush-dark:    #3D2B1F;
            --gold:          #6B3E1F;
            --ink:           #2A1C12;
            --ink-soft:      #5C4030;
            --muted:         #8A7060;
            --muted-light:   #B8A090;
            --border:        #D8C7B0;
            --white:         #FFFFFF;

            /* Typography */
            --font-serif:    'Playfair Display', Georgia, serif;
            --font-sans:     'Inter', -apple-system, BlinkMacSystemFont, sans-serif;

            /* Shadows */
            --shadow-xs:     0 1px 3px rgba(61,43,31,.08);
            --shadow-sm:     0 4px 16px rgba(61,43,31,.10);
            --shadow-md:     0 8px 32px rgba(61,43,31,.14);
            --shadow-lg:     0 16px 56px rgba(61,43,31,.16);

            /* Radius */
            --radius-sm:     8px;
            --radius-md:     14px;
            --radius-lg:     20px;
            --radius-xl:     28px;
            --radius-pill:   999px;
        }

        /* ================================================================
           GLOBAL RESET & BASE
        ================================================================ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; -webkit-text-size-adjust: 100%; }

        body {
            font-family: var(--font-sans);
            background: var(--cream);
            color: var(--ink);
            line-height: 1.65;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        h1, h2, h3, h4 {
            font-family: var(--font-serif);
            line-height: 1.2;
            color: var(--ink);
        }

        a { text-decoration: none; color: inherit; }
        img, svg, video { max-width: 100%; height: auto; display: block; }
        input, select, textarea { max-width: 100%; }
        button, .btn-sage, .btn-sage-outline, .btn-sm-sage, .btn-sm-sage-outline, .btn-primary-custom {
            touch-action: manipulation;
        }

        .ph, .ph-fill {
            font-style: normal;
            line-height: 1;
            vertical-align: -0.125em;
        }

        /* ================================================================
           UTILITY CLASSES
        ================================================================ */
        .text-sage       { color: var(--sage) !important; }
        .text-muted-soft { color: var(--muted) !important; }
        .bg-cream        { background: var(--cream) !important; }
        .bg-sage-xlight  { background: var(--sage-xlight) !important; }
        .bg-cream-dark   { background: var(--cream-dark) !important; }

        /* ================================================================
           BUTTONS
        ================================================================ */
        .btn-sage {
            background: var(--sage);
            color: var(--white);
            border: 2px solid var(--sage);
            padding: 12px 28px;
            font-family: var(--font-sans);
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: var(--radius-sm);
            letter-spacing: 0.3px;
            transition: all 0.28s cubic-bezier(.4,0,.2,1);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .btn-sage:hover {
            background: var(--sage-dark);
            border-color: var(--sage-dark);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-sage-outline {
            background: transparent;
            color: var(--sage);
            border: 2px solid var(--sage);
            padding: 12px 28px;
            font-family: var(--font-sans);
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: var(--radius-sm);
            letter-spacing: 0.3px;
            transition: all 0.28s cubic-bezier(.4,0,.2,1);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .btn-sage-outline:hover {
            background: var(--sage);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }

        .btn-sm-sage {
            background: var(--sage);
            color: var(--white);
            border: 1.5px solid var(--sage);
            padding: 7px 16px;
            font-size: 0.8rem;
            font-weight: 600;
            border-radius: var(--radius-sm);
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            white-space: nowrap;
        }
        .btn-sm-sage:hover {
            background: var(--sage-dark);
            border-color: var(--sage-dark);
            color: var(--white);
            transform: translateY(-1px);
        }

        .btn-sm-sage-outline {
            background: transparent;
            color: var(--sage);
            border: 1.5px solid var(--sage);
            padding: 7px 16px;
            font-size: 0.8rem;
            font-weight: 600;
            border-radius: var(--radius-sm);
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }
        .btn-sm-sage-outline:hover {
            background: var(--sage);
            color: var(--white);
        }

        .btn-primary-custom {
            background: var(--sage);
            border: 2px solid var(--sage);
            color: var(--white);
            font-weight: 600;
            padding: 11px 24px;
            border-radius: var(--radius-sm);
        }
        .btn-primary-custom:hover,
        .btn-primary-custom:focus {
            background: var(--sage-dark);
            border-color: var(--sage-dark);
            color: var(--white);
        }
        .text-primary-custom { color: var(--gold) !important; }

        /* ================================================================
           TOPBAR (Announcement Strip)
        ================================================================ */
        /* .topbar {
            background: var(--sage-dark);
            color: var(--white);
            font-size: 0.78rem;
            font-weight: 500;
            padding: 8px 0;
            text-align: center;
            letter-spacing: 0.3px;
        }
        .topbar a { color: var(--white); font-weight: 700; margin-left: 4px; }
        .topbar a:hover { text-decoration: underline; color: var(--white); } */

        /* ================================================================
           NAVBAR
        ================================================================ */
        .navbar-custom {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 10px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 16px rgba(42,28,18,.08);
        }
        .navbar-actions {
            display: none;
            align-items: center;
            gap: 2px;
            margin-left: auto;
        }
        .navbar-toggler {
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
        .navbar-toggler:focus { box-shadow: none; }

        .navbar-brand-custom {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .brand-logo {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            background: #3D2B1F;
            border: 2px solid var(--gold);
        }
        .brand-text-wrap { line-height: 1.1; }
        .brand-name {
            font-family: var(--font-serif);
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--ink);
            display: block;
        }
        .brand-name span { color: var(--gold); }
        .brand-tagline {
            font-size: 0.67rem;
            color: var(--muted);
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
            display: block;
        }

        .nav-link-custom {
            color: var(--ink-soft);
            font-weight: 500;
            font-size: 0.9rem;
            padding: 8px 14px !important;
            border-radius: var(--radius-pill);
            transition: all 0.2s ease;
            position: relative;
        }
        .nav-link-custom:hover,
        .nav-link-custom.active {
            color: var(--sage);
            background: var(--sage-xlight);
        }

        /* Cart icon */
        .nav-cart-btn {
            position: relative;
            color: var(--ink-soft);
            font-size: 1.2rem;
            width: 44px;
            height: 44px;
            padding: 0;
            border-radius: var(--radius-sm);
            transition: all 0.2s ease;
            border: none;
            background: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .nav-cart-btn:hover { color: var(--sage); background: var(--sage-xlight); }
        .cart-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            background: var(--sage);
            color: var(--white);
            font-size: 0.55rem;
            font-weight: 700;
            width: 17px;
            height: 17px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--white);
        }

        /* Search button */
        .nav-icon-btn {
            color: var(--ink-soft);
            font-size: 1.15rem;
            width: 44px;
            height: 44px;
            padding: 0;
            border-radius: var(--radius-sm);
            border: none;
            background: none;
            transition: all 0.2s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .nav-icon-btn:hover { color: var(--sage); background: var(--sage-xlight); }

        /* ================================================================
           HERO SECTION
        ================================================================ */
        .hero-section {
            background: linear-gradient(160deg, #FFFFFF 0%, #F5EDE6 42%, #FFFFFF 100%);
            padding: 72px 0 56px;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(107,62,31,.07) 0%, transparent 70%);
            top: -120px;
            right: -80px;
            pointer-events: none;
        }
        .hero-section::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(107,62,31,.12) 0%, transparent 70%);
            bottom: -60px;
            left: 5%;
            pointer-events: none;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(107,62,31,.1);
            color: var(--sage);
            padding: 6px 18px;
            border-radius: var(--radius-pill);
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.4px;
            margin-bottom: 20px;
            border: 1px solid rgba(107,62,31,.18);
        }
        .hero-title {
            font-family: var(--font-serif);
            font-size: clamp(2.15rem, 5vw, 3.55rem);
            font-weight: 800;
            line-height: 1.12;
            letter-spacing: -0.02em;
            color: var(--ink);
            margin-bottom: 18px;
        }
        .hero-title .accent {
            color: var(--sage);
            font-style: italic;
            font-weight: 700;
            position: relative;
            display: inline-block;
        }
        .hero-subtitle {
            font-size: 1.08rem;
            color: var(--muted);
            max-width: 34rem;
            line-height: 1.75;
            margin-bottom: 32px;
        }
        .hero-cta-group {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 44px;
        }

        /* Trust badges below hero CTA */
        .hero-trust {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .trust-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .trust-icon {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--sage);
            font-size: 0.95rem;
            box-shadow: var(--shadow-xs);
            flex-shrink: 0;
        }
        .trust-text strong {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--ink);
            line-height: 1.2;
        }
        .trust-text span {
            font-size: 0.72rem;
            color: var(--muted);
        }

        /* Hero image side */
        .hero-image-frame {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hero-img-circle {
            width: 380px;
            height: 380px;
            border-radius: 50%;
            background: var(--sage-dark);
            border: 6px solid var(--white);
            outline: 3px solid var(--sage);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .hero-img-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        .hero-img-circle .placeholder-icon {
            font-size: 7rem;
            color: var(--sage);
            opacity: 0.35;
        }
        /* Floating badges */
        .hero-float-badge {
            position: absolute;
            background: var(--white);
            border-radius: var(--radius-md);
            padding: 10px 16px;
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .hero-float-badge .fb-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--sage-xlight);
            color: var(--sage);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }
        .hero-float-badge.badge-top {
            top: 30px;
            right: -10px;
        }
        .hero-float-badge.badge-bottom {
            bottom: 50px;
            left: -10px;
        }

        /* ================================================================
           SECTION COMMON
        ================================================================ */
        .section-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--gold);
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .section-title {
            font-family: var(--font-serif);
            font-size: 2rem;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 6px;
        }
        .section-subtitle {
            font-size: 0.95rem;
            color: var(--muted);
        }
        .section-link {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--sage);
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: gap 0.2s ease;
        }
        .section-link:hover { gap: 8px; color: var(--sage-dark); }

        /* Section divider line */
        .section-header-row {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        /* ================================================================
           PRODUCT CARDS
        ================================================================ */
        .product-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            transition: all 0.35s cubic-bezier(.25,.46,.45,.94);
            height: 100%;
            position: relative;
        }
        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(107,62,31,.25);
        }
        .product-img-wrap {
            position: relative;
            height: 188px;
            overflow: hidden;
            background: var(--sage-xlight);
        }
        .product-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .product-card:hover .product-img-wrap img { transform: scale(1.06); }
        .product-badge-wrap {
            position: absolute;
            top: 10px;
            left: 10px;
            display: flex;
            gap: 6px;
        }
        .badge-bestseller {
            background: var(--sage);
            color: var(--white);
            font-size: 0.65rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: var(--radius-pill);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-new {
            background: var(--gold);
            color: var(--white);
            font-size: 0.65rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: var(--radius-pill);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .product-body {
            padding: 16px 18px 18px;
        }
        .product-name {
            font-weight: 600;
            font-size: 0.97rem;
            color: var(--ink);
            margin-bottom: 3px;
            word-break: break-word;
        }
        .product-desc {
            font-size: 0.8rem;
            color: var(--muted);
            margin-bottom: 10px;
            display: -webkit-box;
            line-clamp: 2;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.5;
        }
        .product-stars {
            display: flex;
            align-items: center;
            gap: 3px;
            margin-bottom: 10px;
        }
        .star-filled { color: var(--gold); font-size: 0.75rem; }
        .star-empty  { color: var(--border); font-size: 0.75rem; }
        .star-count  { font-size: 0.72rem; color: var(--muted); margin-left: 4px; }
        .product-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 12px;
            border-top: 1px solid var(--border);
        }
        .product-price {
            font-family: var(--font-serif);
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--sage-dark);
        }

        /* Placeholder image (no product image) */
        .product-img-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3.5rem;
            color: var(--sage);
            opacity: 0.35;
        }

        /* ================================================================
           COMBO CARDS (Horizontal, SweetCrumbs style)
        ================================================================ */
        .combo-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 0;
            transition: all 0.3s ease;
            margin-bottom: 14px;
        }
        .combo-card:last-child { margin-bottom: 0; }
        .combo-card:hover {
            box-shadow: var(--shadow-md);
            border-color: rgba(107,62,31,.25);
            transform: translateX(4px);
        }
        .combo-img-link {
            display: block;
            flex-shrink: 0;
            line-height: 0;
        }
        .combo-img {
            width: 88px;
            height: 88px;
            object-fit: cover;
            flex-shrink: 0;
            background: var(--sage-xlight);
        }
        .combo-img-placeholder {
            width: 88px;
            height: 88px;
            flex-shrink: 0;
            background: var(--sage-xlight);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--sage);
            font-size: 1.8rem;
            opacity: 0.5;
        }
        .combo-body {
            padding: 12px 14px;
            flex: 1;
        }
        .combo-name {
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--ink);
            margin-bottom: 4px;
        }
        .combo-items-list {
            list-style: none;
            padding: 0;
            margin: 0 0 6px;
        }
        .combo-items-list li {
            font-size: 0.72rem;
            color: var(--muted);
            line-height: 1.6;
        }
        .combo-price-row {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .combo-add {
            margin-left: auto;
            padding: 6px 10px;
        }
        .combo-price {
            font-weight: 800;
            font-size: 0.95rem;
            color: var(--sage-dark);
        }
        .combo-was {
            font-size: 0.75rem;
            color: var(--muted-light);
            text-decoration: line-through;
        }
        .combo-save {
            background: rgba(107,62,31,.12);
            color: #6B3E1F;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: var(--radius-pill);
            white-space: nowrap;
        }

        /* ================================================================
           STEPS SECTION
        ================================================================ */
        .step-card {
            text-align: center;
            padding: 32px 20px;
            background: var(--white);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            height: 100%;
            transition: all 0.3s ease;
        }
        .step-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }
        .step-icon-ring {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: var(--sage-xlight);
            border: 2px solid rgba(107,62,31,.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--sage);
            font-size: 1.6rem;
            margin: 0 auto 18px;
            position: relative;
        }
        .step-icon-ring .step-num {
            position: absolute;
            top: -6px;
            right: -6px;
            width: 22px;
            height: 22px;
            background: var(--sage);
            color: var(--white);
            border-radius: 50%;
            font-size: 0.65rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--white);
        }
        .step-title {
            font-family: var(--font-serif);
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .step-desc {
            font-size: 0.88rem;
            color: var(--muted);
            line-height: 1.65;
        }

        /* ================================================================
           TRUST BAR (bottom strip)
        ================================================================ */
        .trust-bar {
            background: var(--sage);
            padding: 22px 0;
        }
        .trust-bar-item {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--white);
        }
        .trust-bar-item i {
            font-size: 1.4rem;
            opacity: 0.85;
        }
        .trust-bar-item strong {
            display: block;
            font-size: 0.85rem;
            font-weight: 700;
        }
        .trust-bar-item span {
            font-size: 0.75rem;
            opacity: 0.8;
        }

        /* ================================================================
           NEWSLETTER
        ================================================================ */
        .newsletter-section {
            background: #FFFFFF;
            padding: 56px 0;
            border-top: 1px solid var(--border);
        }
        .newsletter-title {
            font-family: var(--font-serif);
            font-size: 2rem;
            font-weight: 700;
            color: var(--ink);
        }
        .newsletter-title .accent { color: var(--gold); }
        .newsletter-sub {
            font-size: 0.93rem;
            color: var(--muted);
            margin-top: 6px;
        }
        .newsletter-form {
            display: flex;
            gap: 0;
            max-width: 420px;
            border-radius: var(--radius-sm);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid rgba(107,62,31,.2);
        }
        .newsletter-form input {
            flex: 1;
            min-width: 0;
            border: none;
            padding: 13px 18px;
            font-family: var(--font-sans);
            font-size: 0.88rem;
            color: var(--ink);
            background: var(--white);
            outline: none;
            min-height: 48px;
        }
        .newsletter-form button {
            background: var(--sage);
            color: var(--white);
            border: none;
            padding: 13px 22px;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            transition: background 0.2s ease;
            white-space: nowrap;
            min-height: 48px;
        }
        .newsletter-form button:hover { background: var(--sage-dark); }
        .newsletter-privacy {
            font-size: 0.72rem;
            color: var(--muted-light);
            margin-top: 8px;
        }

        /* ================================================================
           FOOTER
        ================================================================ */
        .footer {
            background: #1A120C;
            color: rgba(255,255,255,.75);
            padding: 60px 0 28px;
        }
        .footer-logo {
            width: 92px;
            height: 92px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin-bottom: 14px;
            background: #3D2B1F;
            border: 2px solid var(--gold);
        }
        .footer-brand-name {
            font-family: var(--font-serif);
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--white);
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }
        .footer-brand-name span { color: var(--gold); }
        .footer-about {
            font-size: 0.88rem;
            line-height: 1.75;
            max-width: 280px;
            color: rgba(255,255,255,.65);
            margin-bottom: 20px;
        }
        .footer-social a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: rgba(255,255,255,.1);
            color: var(--white);
            font-size: 0.85rem;
            margin-right: 8px;
            transition: all 0.2s ease;
        }
        .footer-social a:hover { background: var(--gold); color: var(--sage-dark); transform: translateY(-2px); }
        .footer h6 {
            color: var(--white);
            font-weight: 700;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 18px;
        }
        .footer ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .footer ul li {
            padding: 5px 0;
        }
        .footer ul li a {
            color: rgba(255,255,255,.6);
            font-size: 0.88rem;
            transition: color 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .footer ul li a:hover { color: var(--white); }
        .footer ul li a i { width: 16px; color: var(--blush); font-size: 0.8rem; }
        .footer-divider {
            border-color: rgba(255,255,255,.1);
            margin: 32px 0 20px;
        }
        .footer-bottom {
            text-align: center;
            font-size: 0.8rem;
            color: rgba(255,255,255,.4);
        }
        .footer-pay-icons {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            font-size: 0.75rem;
            color: rgba(255,255,255,.5);
        }
        .pay-tag {
            background: rgba(255,255,255,.1);
            color: rgba(255,255,255,.7);
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        /* ================================================================
           AUTH PAGES
        ================================================================ */
        .auth-page {
            min-height: 100vh;
            background: linear-gradient(155deg, var(--sage-xlight) 0%, var(--cream-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .auth-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 44px 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: var(--shadow-lg);
        }
        .auth-logo {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: 0 auto 20px;
            background: #3D2B1F;
        }
        .auth-title {
            font-family: var(--font-serif);
            font-size: 1.7rem;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 4px;
        }
        .auth-subtitle {
            font-size: 0.88rem;
            color: var(--muted);
            margin-bottom: 28px;
        }
        .auth-form .form-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--ink-soft);
            margin-bottom: 6px;
        }
        .auth-form .form-control {
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 11px 14px;
            font-size: 0.9rem;
            font-family: var(--font-sans);
            background: var(--white);
            color: var(--ink);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .auth-form .form-control:focus {
            border-color: var(--sage);
            box-shadow: 0 0 0 3px rgba(107,62,31,.1);
            outline: none;
        }
        .auth-form .form-control::placeholder { color: var(--muted-light); }

        .form-check-input:checked {
            background-color: var(--sage);
            border-color: var(--sage);
        }
        .form-control:focus,
        .form-select:focus {
            border-color: var(--sage);
            box-shadow: 0 0 0 0.2rem rgba(107,62,31,.22);
        }

        /* ================================================================
           EMPTY STATE
        ================================================================ */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--muted);
        }
        .empty-state i {
            font-size: 3rem;
            opacity: 0.25;
            margin-bottom: 16px;
            color: var(--sage);
        }
        .empty-state p { font-size: 0.95rem; }

        /* ================================================================
           ALERTS / FLASH
        ================================================================ */
        .alert-sage {
            background: var(--sage-xlight);
            border: 1px solid rgba(107,62,31,.3);
            border-radius: var(--radius-sm);
            color: var(--sage-dark);
            padding: 12px 18px;
            font-size: 0.88rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* ================================================================
           PAGE HERO (inner pages)
        ================================================================ */
        .page-hero {
            background: linear-gradient(160deg, #FFFFFF 0%, #F5EDE6 100%);
            padding: 48px 0 36px;
            border-bottom: 1px solid var(--border);
        }
        .page-hero-title {
            font-family: var(--font-serif);
            font-size: 2.4rem;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 6px;
        }
        .page-hero-sub {
            color: var(--muted);
            font-size: 0.95rem;
        }
        .breadcrumb-custom {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            color: var(--muted);
            margin-bottom: 16px;
        }
        .breadcrumb-custom a { color: var(--sage); }
        .breadcrumb-custom i { font-size: 0.6rem; }

        /* ================================================================
           CART LINES
        ================================================================ */
        .cart-lines { display: flex; flex-direction: column; gap: 12px; }
        .cart-line {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 10px 16px;
            align-items: center;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 16px;
        }
        .cart-line-name {
            font-weight: 700;
            color: var(--ink);
            word-break: break-word;
        }
        .cart-line-meta { font-size: 0.82rem; color: var(--muted); margin-top: 2px; }
        .cart-line-total { font-weight: 800; color: var(--sage-dark); white-space: nowrap; }
        .cart-qty-form { display: flex; align-items: center; gap: 8px; }
        .cart-qty-btn {
            width: 40px;
            height: 40px;
            border: 1.5px solid var(--sage);
            background: var(--white);
            color: var(--sage);
            border-radius: var(--radius-sm);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .cart-qty-btn:disabled {
            opacity: .4;
            cursor: not-allowed;
        }
        .cart-qty-value {
            min-width: 28px;
            text-align: center;
            font-weight: 700;
            color: var(--ink);
        }
        .cart-remove-form button {
            min-height: 44px;
            padding: 0 14px;
        }
        .cart-summary {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }
        .cart-summary-actions { text-align: right; }
        .cart-summary-actions .btn,
        .cart-summary-actions .btn-primary-custom { min-height: 48px; }

        /* ================================================================
           MOBILE DOCK
        ================================================================ */
        .mobile-dock {
            display: none;
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1100;
            background: var(--white);
            border-top: 1px solid var(--border);
            padding: 6px 8px calc(6px + env(safe-area-inset-bottom));
            box-shadow: 0 -8px 24px rgba(42,28,18,.08);
        }
        .mobile-dock a {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
            min-height: 52px;
            color: var(--ink-soft);
            font-size: 0.68rem;
            font-weight: 600;
            border-radius: 10px;
        }
        .mobile-dock a i { font-size: 1.25rem; }
        .mobile-dock a.active,
        .mobile-dock a:hover { color: var(--sage); background: var(--sage-xlight); }
        .dock-cart { position: relative; }
        .dock-cart .cart-badge {
            top: 4px;
            right: calc(50% - 18px);
        }

        /* ================================================================
           RESPONSIVE
        ================================================================ */
        @media (max-width: 991.98px) {
            .navbar-actions { display: flex; }
            .navbar-collapse {
                width: 100%;
                padding: 12px 0 8px;
                border-top: 1px solid var(--border);
                margin-top: 10px;
            }
            .navbar-nav {
                gap: 4px !important;
                margin-bottom: 8px;
            }
            .nav-link-custom {
                display: block;
                padding: 12px 14px !important;
                font-size: 1rem;
            }
            .hero-title      { font-size: 2.4rem; }
            .hero-img-circle { width: 280px; height: 280px; }
            .page-hero { padding: 32px 0 24px; }
            .page-hero-title { font-size: 1.9rem; }
            .section-title { font-size: 1.65rem; }
            .mobile-dock { display: flex; }
            body { padding-bottom: 72px; }
            .footer { padding-bottom: 36px; }
        }
        @media (max-width: 767.98px) {
            .topbar { font-size: 0.72rem; padding: 7px 12px; }
            .brand-logo { width: 44px; height: 44px; }
            .brand-name { font-size: 1.12rem; }
            .brand-tagline { display: none; }
            .hero-section    { padding: 36px 0 32px; }
            .hero-title      { font-size: 2rem; }
            .hero-subtitle   { font-size: 0.95rem; max-width: none; }
            .hero-img-circle { width: 220px; height: 220px; margin-top: 24px; }
            .hero-cta-group  { flex-direction: column; }
            .hero-cta-group a, .hero-cta-group button { width: 100%; justify-content: center; min-height: 48px; }
            .hero-trust      { gap: 12px; flex-direction: column; }
            .section-header-row { flex-direction: column; align-items: flex-start; gap: 8px; }
            .newsletter-form { max-width: 100%; flex-direction: column; overflow: visible; }
            .newsletter-form input,
            .newsletter-form button { width: 100%; border-radius: var(--radius-sm); }
            .newsletter-title { font-size: 1.55rem; }
            .auth-card       { padding: 28px 18px; }
            .combo-card { flex-direction: column; align-items: stretch; }
            .combo-img-link,
            .combo-img,
            .combo-img-placeholder { width: 100%; height: 120px; }
            .combo-img { height: 120px; }
            .combo-price-row { flex-wrap: wrap; }
            .step-card { padding: 24px 16px; }
            .cart-line { grid-template-columns: 1fr; }
            .cart-summary,
            .cart-summary-actions { width: 100%; text-align: left; }
            .cart-summary-actions .btn,
            .cart-summary-actions .btn-primary-custom,
            .cart-summary .btn { width: 100%; justify-content: center; }
            .form-control, .form-select { min-height: 44px; font-size: 16px !important; }
            .btn-sage { width: 100%; justify-content: center; min-height: 48px; }
            .btn-primary-custom { width: 100%; min-height: 48px; }
            .py-5 { padding-top: 1.75rem !important; padding-bottom: 1.75rem !important; }
            .footer-about { max-width: none; }
            .footer-social a { width: 44px; height: 44px; }
        }
        @media (max-width: 575.98px) {
            .hero-title      { font-size: 1.75rem; }
            .trust-bar .col-6 { flex: 0 0 100%; max-width: 100%; }
            .trust-bar-item { justify-content: flex-start; }
            .product-img-wrap { height: 148px; }
            .product-body { padding: 12px; }
            .product-desc,
            .product-stars { display: none; }
            .product-price { font-size: 1rem; }
            .btn-sm-sage {
                min-width: 40px;
                min-height: 40px;
                padding: 8px;
                justify-content: center;
            }
            .container { padding-left: 16px; padding-right: 16px; }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- ================================================================
         TOPBAR
    ================================================================ -->
    <!-- <div class="topbar">
        <i class="ph ph-truck me-1"></i>
        Free delivery on orders over <strong>₦5,000</strong>!
        <a href="{{ route('customer.products.index') }}">Order now →</a>
    </div> -->


    <!-- ================================================================
         NAVBAR
    ================================================================ -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">

            <!-- Brand -->
            <a class="navbar-brand-custom" href="{{ route('customer.home') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Father Care Bakery" class="brand-logo">
                <div class="brand-text-wrap">
                    <span class="brand-name">Father<span>Care</span></span>
                    <span class="brand-tagline">Our Daily Bread</span>
                </div>
            </a>

            <!-- Always-visible cart + menu on phones -->
            <div class="navbar-actions">
                <a href="{{ route('customer.cart.index') }}" class="nav-cart-btn" title="Cart" aria-label="Open cart">
                    <i class="ph ph-shopping-bag"></i>
                    <span class="cart-badge cartCount">{{ $cartCount }}</span>
                </a>
                <button class="navbar-toggler border-0 shadow-none" type="button"
                        data-bs-toggle="collapse" data-bs-target="#navbarMain"
                        aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="ph ph-list" style="font-size:1.35rem; color:var(--ink-soft);"></i>
                </button>
            </div>

            <!-- Nav items -->
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto align-items-lg-center gap-1">
                    <li class="nav-item">
                        <a class="nav-link-custom {{ request()->routeIs('customer.home') ? 'active' : '' }}"
                           href="{{ route('customer.home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom {{ request()->routeIs('customer.products.*') ? 'active' : '' }}"
                           href="{{ route('customer.products.index') }}">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom" href="{{ route('customer.home') }}#combos">Combos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom {{ request()->routeIs('customer.contact') ? 'active' : '' }}"
                           href="{{ route('customer.contact') }}">Contact</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link-custom {{ request()->routeIs('customer.orders.*') ? 'active' : '' }}"
                               href="{{ route('customer.orders.index') }}">My Orders</a>
                        </li>
                    @endauth
                </ul>

                <div class="d-flex align-items-center gap-2 mt-2 mt-lg-0 flex-wrap">
                    <a href="{{ route('customer.products.index') }}" class="nav-icon-btn" title="Search" aria-label="Search products">
                        <i class="ph ph-magnifying-glass"></i>
                    </a>

                    <a href="{{ route('customer.cart.index') }}" class="nav-cart-btn d-none d-lg-inline-flex" title="Cart" aria-label="Open cart">
                        <i class="ph ph-shopping-bag"></i>
                        <span class="cart-badge cartCount">{{ $cartCount }}</span>
                    </a>

                    @auth
                        <div class="dropdown">
                            <button class="nav-icon-btn" id="userMenuBtn"
                                    data-bs-toggle="dropdown" aria-expanded="false"
                                    type="button" title="Account">
                                <i class="ph ph-user-circle"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3 mt-2"
                                aria-labelledby="userMenuBtn"
                                style="min-width:200px; padding:8px;">
                                <li class="px-3 py-2 mb-1">
                                    <div style="font-weight:700; font-size:0.88rem; color:var(--ink);">{{ auth()->user()->name }}</div>
                                    <div style="font-size:0.75rem; color:var(--muted); word-break:break-all;">{{ auth()->user()->email }}</div>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item rounded-2 d-flex align-items-center gap-2 py-2" href="{{ route('customer.orders.index') }}">
                                        <i class="ph ph-receipt" style="color:var(--sage); width:16px;"></i>
                                        My Orders
                                    </a>
                                </li>
                                @if(auth()->user()?->isAdmin() && app('router')->has('admin.dashboard'))
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <a class="dropdown-item rounded-2 d-flex align-items-center gap-2 py-2"
                                           href="{{ route('admin.dashboard') }}">
                                            <i class="ph ph-gauge" style="color:var(--sage); width:16px;"></i>
                                            Admin Dashboard
                                        </a>
                                    </li>
                                @endif
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item rounded-2 d-flex align-items-center gap-2 py-2 text-danger"
                                       href="#"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="ph ph-sign-out" style="width:16px;"></i>
                                        Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn-sm-sage-outline">Login</a>
                        <a href="{{ route('register') }}" class="btn-sm-sage">Register</a>
                    @endauth
                </div>
            </div>

        </div>
    </nav>


    <!-- ================================================================
         FLASH MESSAGES
    ================================================================ -->
    @if(session('success') || session('error'))
        <div class="container mt-3">
            @if(session('success'))
                <div class="alert-sage">
                    <i class="ph ph-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger rounded-3 border-0 py-2 px-3">
                    <i class="ph ph-x-circle me-2"></i>{{ session('error') }}
                </div>
            @endif
        </div>
    @endif


    <!-- ================================================================
         MAIN CONTENT
    ================================================================ -->
    <main>
        @yield('content')
    </main>


    <!-- ================================================================
         TRUST BAR
    ================================================================ -->
    <!-- <div class="trust-bar">
        <div class="container">
            <div class="row g-3 justify-content-center text-center text-lg-start">
                <div class="col-6 col-lg-3">
                    <div class="trust-bar-item justify-content-center justify-content-lg-start">
                        <i class="ph ph-leaf"></i>
                        <div>
                            <strong>100% Natural</strong>
                            <span>No artificial additives</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="trust-bar-item justify-content-center justify-content-lg-start">
                        <i class="ph ph-truck"></i>
                        <div>
                            <strong>Same Day Delivery</strong>
                            <span>Order before 2 PM</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="trust-bar-item justify-content-center justify-content-lg-start">
                        <i class="ph ph-package"></i>
                        <div>
                            <strong>Secure Packaging</strong>
                            <span>Arrives fresh &amp; intact</span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="trust-bar-item justify-content-center justify-content-lg-start">
                        <i class="ph ph-headset"></i>
                        <div>
                            <strong>Customer Support</strong>
                            <span>We're here for you</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->


    <!-- ================================================================
         NEWSLETTER
    ================================================================ -->
    <section class="newsletter-section">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-5">
                    <div class="newsletter-title">
                        Get Something <span class="accent">Sweet</span><br>in Your Inbox
                        <span style="color:var(--blush-dark); font-size:0.7em; margin-left:6px;">♡</span>
                    </div>
                    <p class="newsletter-sub">
                        Be the first to know about new cakes, special offers &amp; sweet surprises.
                    </p>
                </div>
                <div class="col-lg-5 offset-lg-1">
                    <form class="newsletter-form" onsubmit="return false;">
                        <input type="email" placeholder="Enter your email" id="newsletterEmail">
                        <button type="submit">Subscribe</button>
                    </form>
                    <p class="newsletter-privacy">
                        <i class="ph ph-lock me-1"></i>
                        No spam, unsubscribe anytime.
                    </p>
                </div>
            </div>
        </div>
    </section>


    <!-- ================================================================
         FOOTER
    ================================================================ -->
    <footer class="footer" id="contact">
        <div class="container">
            <div class="row g-5">

                <!-- Brand column -->
                <div class="col-lg-4 col-md-6">
                    <img src="{{ asset('images/logo.png') }}" alt="Father Care Bakery" class="footer-logo">
                    <div class="footer-brand-name">
                        Father<span>Care</span> Bakery
                    </div>
                    <p class="footer-about">
                        Freshly baked with love. Serving the community with quality bread,
                        cakes, pastries, and buns since 2020. Made daily with the finest ingredients.
                    </p>
                    <div class="footer-social">
                        <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" title="Facebook"><i class="ph ph-facebook-logo"></i></a>
                        <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" title="Instagram"><i class="ph ph-instagram-logo"></i></a>
                        <a href="https://x.com" target="_blank" rel="noopener noreferrer" title="X"><i class="ph ph-x-logo"></i></a>
                        <a href="https://tiktok.com" target="_blank" rel="noopener noreferrer" title="TikTok"><i class="ph ph-tiktok-logo"></i></a>
                    </div>
                </div>

                <!-- Shop links -->
                <div class="col-lg-2 col-md-6 col-6">
                    <h6>Shop</h6>
                    <ul>
                        <li><a href="{{ route('customer.products.index', ['category' => 'cakes']) }}"><i class="ph ph-caret-right"></i>All Cakes</a></li>
                        <li><a href="{{ route('customer.products.index', ['category' => 'bread']) }}"><i class="ph ph-caret-right"></i>Bread</a></li>
                        <li><a href="{{ route('customer.products.index', ['category' => 'pastries']) }}"><i class="ph ph-caret-right"></i>Pastries</a></li>
                        <li><a href="{{ route('customer.products.index', ['category' => 'buns']) }}"><i class="ph ph-caret-right"></i>Buns</a></li>
                        <li><a href="{{ route('customer.home') }}#combos"><i class="ph ph-caret-right"></i>Combos</a></li>
                    </ul>
                </div>

                <!-- Company -->
                <div class="col-lg-2 col-md-6 col-6">
                    <h6>Company</h6>
                    <ul>
                        <li><a href="{{ route('customer.home') }}#about"><i class="ph ph-caret-right"></i>About Us</a></li>
                        <li><a href="{{ route('customer.products.index') }}"><i class="ph ph-caret-right"></i>Fresh Picks</a></li>
                        <li><a href="{{ route('customer.contact') }}"><i class="ph ph-caret-right"></i>Visit Us</a></li>
                    </ul>
                </div>

                <!-- Help & contact -->
                <div class="col-lg-4 col-md-6">
                    <h6>Contact Info</h6>
                    <ul>
                        <li>
                            <a href="#contact">
                                <i class="ph ph-map-pin"></i>
                                {{ config('bakery.address') }}
                            </a>
                        </li>
                        <li>
                            <a href="tel:+234{{ ltrim(config('bakery.phone'), '0') }}">
                                <i class="ph ph-phone"></i>
                                {{ config('bakery.phone') }}
                            </a>
                        </li>
                        <li>
                            <a href="mailto:{{ config('bakery.email') }}">
                                <i class="ph ph-envelope"></i>
                                {{ config('bakery.email') }}
                            </a>
                        </li>
                    </ul>

                    <div style="margin-top:20px;">
                        <h6 style="margin-bottom:10px; font-size:0.8rem;">Opening Hours</h6>
                        <ul>
                            <li><span style="color:inherit; opacity:0.9;"><i class="ph ph-clock"></i>{{ config('bakery.hours.weekdays') }}</span></li>
                            <li><span style="color:inherit; opacity:0.9;"><i class="ph ph-clock"></i>{{ config('bakery.hours.saturday') }}</span></li>
                            <li><span style="color:inherit; opacity:0.9;"><i class="ph ph-clock"></i>{{ config('bakery.hours.sunday') }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <hr class="footer-divider">

            <div class="footer-bottom">
                &copy; {{ date('Y') }} Father Care Bakery. All Rights Reserved.
                <div class="footer-pay-icons">
                    <span class="pay-tag">Bank Transfer</span>
                    <span class="pay-tag">USSD</span>
                    <span class="pay-tag">Cash on Delivery</span>
                    <span class="pay-tag">Card (Paystack)</span>
                </div>
            </div>
        </div>
    </footer>


    <!-- Mobile quick actions -->
    <nav class="mobile-dock" aria-label="Mobile shortcuts">
        <a href="{{ route('customer.home') }}" class="{{ request()->routeIs('customer.home') ? 'active' : '' }}">
            <i class="ph ph-house"></i>
            Home
        </a>
        <a href="{{ route('customer.products.index') }}" class="{{ request()->routeIs('customer.products.*') ? 'active' : '' }}">
            <i class="ph ph-storefront"></i>
            Shop
        </a>
        <a href="tel:+234{{ ltrim(config('bakery.phone'), '0') }}">
            <i class="ph ph-phone"></i>
            Call
        </a>
    </nav>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Cart count sync (shared) -->
    <script>
        function syncCartCount(count) {
            const value = typeof count === 'number' ? count : parseInt(String(count || '0'), 10);
            document.querySelectorAll('.cartCount').forEach((badge) => {
                badge.textContent = value;
            });
            localStorage.setItem('cartCount', value);
        }

        const initialCartCount = Number("{{ (int) $cartCount }}");
        syncCartCount(initialCartCount);

        const navCollapse = document.getElementById('navbarMain');
        document.querySelectorAll('#navbarMain .nav-link-custom').forEach((link) => {
            link.addEventListener('click', () => {
                if (window.bootstrap && navCollapse && navCollapse.classList.contains('show')) {
                    bootstrap.Collapse.getOrCreateInstance(navCollapse).hide();
                }
            });
        });

        function showCartToast(message) {
            const toast = document.getElementById('cartToast');
            if (!toast) return;
            toast.innerHTML = '<i class="ph ph-check-circle"></i><span></span>';
            toast.querySelector('span').textContent = message;
            toast.classList.add('show');
            clearTimeout(window.cartToastTimer);
            window.cartToastTimer = setTimeout(() => toast.classList.remove('show'), 1800);
        }

        document.querySelectorAll('.add-to-cart').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                const scrollPosition = window.scrollY;
                const productId = this.dataset.id;
                const original = this.innerHTML;
                this.disabled = true;

                fetch('{{ route("customer.cart.add") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ product_id: productId, quantity: 1 })
                }).then(response => response.ok ? response.json() : Promise.reject())
                  .then(data => {
                      if (typeof data.cart_count === 'number') {
                          syncCartCount(data.cart_count);
                      }
                      this.innerHTML = '<i class="ph ph-check"></i> Added';
                      showCartToast((this.dataset.name || 'Item') + ' added to cart');
                  }).catch(() => {})
                  .finally(() => {
                      if (window.scrollY !== scrollPosition) {
                          window.scrollTo(0, scrollPosition);
                      }
                      setTimeout(() => {
                          this.innerHTML = original;
                          this.disabled = false;
                      }, 1300);
                  });
            });
        });
    </script>

    <div id="cartToast" class="cart-toast" aria-live="polite" aria-atomic="true"></div>

    <style>
        .cart-toast {
            position: fixed;
            right: 1.2rem;
            bottom: 1.25rem;
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            background: rgba(61, 43, 31, 0.96);
            color: #fff;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            box-shadow: 0 14px 26px rgba(61,43,31,0.18);
            font-size: 0.8rem;
            font-weight: 600;
            opacity: 0;
            transform: translateY(12px);
            pointer-events: none;
            transition: all 0.25s ease;
            z-index: 2000;
        }

        .cart-toast i { color: #b9d8ae; font-size: 1.1rem; }

        .cart-toast.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>

    @stack('scripts')
</body>
</html>
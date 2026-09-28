<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin — Father Care Bakery')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2/src/regular/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.2/src/fill/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        /* ═══════════════════════════════════════════════════════
           FATHER CARE BAKERY — WHITE & BROWN BRAND DESIGN SYSTEM
           White: #FFFFFF | Chocolate: #6B3E1F | Espresso: #3D2B1F
           Ink: #2A1C12 | Warm Border: #D8C7B0 | Muted: #8A7060
        ═══════════════════════════════════════════════════════ */
        :root {
            /* Brand palette (white + brown only) */
            --brand-choco:     #6B3E1F;
            --brand-dark:      #3D2B1F;
            --brand-ink:       #2A1C12;
            --brand-ink-soft:  #5C4030;
            --brand-muted:     #8A7060;
            --brand-gold:      #6B3E1F;
            --brand-gold-dark: #3D2B1F;
            --brand-gold-dim:  rgba(107,62,31,.10);
            --brand-gold-glow: rgba(107,62,31,.18);
            --brand-cream:     #FFFFFF;
            --brand-cream-dark:#F5EDE6;

            /* Surface tokens */
            --bg-body:         #FFFFFF;
            --sidebar-bg:      #FFFFFF;
            --sidebar-border:  #D8C7B0;
            --header-bg:       rgba(255, 255, 255, 0.94);
            --header-border:   #D8C7B0;
            --card-bg:         #FFFFFF;
            --card-bg-alt:     #F5EDE6;
            --card-border:     #D8C7B0;
            --card-border-h:   #6B3E1F;
            --card-shadow:     0 2px 10px rgba(61,43,31,.05), 0 1px 3px rgba(61,43,31,.03);
            --card-shadow-h:   0 10px 30px rgba(61,43,31,.09), 0 2px 8px rgba(61,43,31,.04);

            /* Accents & Status — brown scale only */
            --accent:          #6B3E1F;
            --accent-gold:     #6B3E1F;
            --success:         #6B3E1F;
            --success-dim:     #F5EDE6;
            --success-border:  #D8C7B0;
            --danger:          #3D2B1F;
            --danger-dim:      #F5EDE6;
            --danger-border:   #D8C7B0;
            --info:            #6B3E1F;
            --info-dim:        #F5EDE6;
            --info-border:     #D8C7B0;
            --warning:         #8B5A3C;
            --warning-dim:     #F5EDE6;
            --warning-border:  #D8C7B0;

            /* Text */
            --text-primary:    #2A1C12;
            --text-secondary:  #5C4030;
            --text-muted:      #8A7060;

            /* Layout & Typography */
            --font-sans:       'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-serif:      'Playfair Display', Georgia, serif;
            --sidebar-w:       260px;
            --header-h:        68px;
            --radius:          14px;
            --radius-sm:       8px;
            --radius-pill:     999px;
            --transition:      .22s cubic-bezier(.4,0,.2,1);
        }

        /* ── Reset & Base ────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--font-sans);
            background: var(--bg-body);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, .h1, .h2, .h3, .h4 {
            color: var(--brand-ink);
            font-weight: 700;
        }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-body); }
        ::-webkit-scrollbar-thumb { background: #D8C7B0; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--brand-choco); }

        /* ═══════════════════════════════════════════════════════
           SIDEBAR (White + Chocolate Brown)
        ═══════════════════════════════════════════════════════ */
        .admin-sidebar {
            width: var(--sidebar-w);
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            min-height: 100vh;
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 120;
            transition: transform var(--transition);
            box-shadow: 2px 0 16px rgba(61,43,31,.04);
        }
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(42, 28, 18, 0.42);
            z-index: 110;
        }
        .sidebar-overlay.show { display: block; }

        /* Logo zone */
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 18px 20px 16px;
            border-bottom: 1px solid var(--sidebar-border);
            background: linear-gradient(135deg, #F5EDE6 0%, #FFFFFF 100%);
            text-decoration: none;
        }
        .sidebar-logo-img {
            width: 44px; height: 44px;
            border-radius: 12px;
            border: 2px solid var(--brand-gold);
            object-fit: cover;
            box-shadow: 0 2px 8px var(--brand-gold-glow);
        }
        .sidebar-logo-text strong {
            display: block;
            font-size: .95rem; font-weight: 800;
            color: var(--brand-dark);
            letter-spacing: -.02em;
            font-family: var(--font-serif);
        }
        .sidebar-logo-text span {
            font-size: .65rem;
            color: var(--brand-choco);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .12em;
        }

        /* Nav */
        .sidebar-nav {
            padding: 16px 12px;
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .sidebar-section-label {
            font-size: .64rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .12em;
            color: var(--brand-muted);
            padding: 14px 10px 6px;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 9px 12px;
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            text-decoration: none;
            font-size: .86rem;
            font-weight: 500;
            transition: all var(--transition);
            position: relative;
        }
        .sidebar-nav a i {
            font-size: 1.15rem;
            width: 22px;
            text-align: center;
            flex-shrink: 0;
            color: var(--brand-muted);
            transition: color var(--transition);
        }
        .sidebar-nav a:hover {
            background: #F5EDE6;
            color: var(--brand-choco);
            transform: translateX(2px);
        }
        .sidebar-nav a:hover i {
            color: var(--brand-choco);
        }
        .sidebar-nav a.active {
            background: var(--brand-choco);
            color: #FFFFFF;
            font-weight: 600;
            box-shadow: 0 4px 14px rgba(107,62,31,.25);
        }
        .sidebar-nav a.active i {
            color: #FFFFFF;
        }

        .sidebar-divider {
            height: 1px;
            background: var(--sidebar-border);
            margin: 10px 10px;
        }

        /* User block */
        .sidebar-user {
            padding: 14px 16px;
            border-top: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            gap: 10px;
            background: #F5EDE6;
        }
        .sidebar-user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--brand-choco), var(--brand-dark));
            display: flex; align-items: center; justify-content: center;
            font-size: .8rem; font-weight: 800;
            color: #FFFFFF;
            flex-shrink: 0;
            border: 2px solid var(--brand-gold);
            box-shadow: 0 2px 6px rgba(61,43,31,.15);
        }
        .sidebar-user-info { flex: 1; min-width: 0; }
        .sidebar-user-info strong {
            display: block;
            font-size: .8rem; font-weight: 700;
            color: var(--brand-ink);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .sidebar-user-info span {
            font-size: .68rem;
            color: var(--brand-muted);
        }
        .sidebar-logout-btn {
            background: none; border: none; cursor: pointer;
            color: var(--brand-muted); font-size: 1.15rem;
            padding: 6px; border-radius: 6px;
            transition: color var(--transition), background var(--transition);
            display: flex; align-items: center; justify-content: center;
        }
        .sidebar-logout-btn:hover {
            color: var(--danger);
            background: var(--danger-dim);
        }

        /* ═══════════════════════════════════════════════════════
           MAIN WRAPPER & HEADER
        ═══════════════════════════════════════════════════════ */
        .admin-wrapper {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header */
        .admin-header {
            height: var(--header-h);
            background: var(--header-bg);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--header-border);
            position: sticky; top: 0; z-index: 50;
            display: flex; align-items: center;
            padding: 0 32px;
            gap: 16px;
            box-shadow: 0 2px 8px rgba(61,43,31,.03);
        }
        .sidebar-toggle-btn {
            display: none;
            background: none; border: none;
            font-size: 1.4rem; color: var(--brand-choco);
            cursor: pointer;
            width: 44px; height: 44px;
            align-items: center; justify-content: center;
            border-radius: 8px;
        }
        .admin-header-greeting { flex: 1; }
        .admin-header-greeting h2 {
            font-size: 1.05rem; font-weight: 700;
            color: var(--brand-ink); line-height: 1.2;
            font-family: var(--font-sans);
        }
        .admin-header-greeting p {
            font-size: .75rem; color: var(--brand-muted); margin-top: 1px;
        }
        .admin-header-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 14px;
            background: #F5EDE6;
            border: 1px solid #D8C7B0;
            border-radius: var(--radius-pill);
            font-size: .7rem; font-weight: 700;
            color: #6B3E1F;
            letter-spacing: .05em;
        }
        .admin-header-badge::before {
            content: '';
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #6B3E1F;
            animation: pulse-dot 2s infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: .4; transform: scale(.7); }
        }

        /* Content */
        .admin-main { flex: 1; padding: 30px 32px; }

        /* ═══════════════════════════════════════════════════════
           SHARED SURFACES & CARDS (White + Warm Wheat Border)
        ═══════════════════════════════════════════════════════ */
        .stat-card,
        .card-surface,
        .panel {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
            padding: 22px;
            transition: border-color var(--transition), box-shadow var(--transition), transform var(--transition);
        }
        .panel { padding: 0; }
        .stat-card:hover,
        .card-surface:hover,
        .panel:hover {
            border-color: var(--card-border-h);
            box-shadow: var(--card-shadow-h);
        }

        /* ── Alerts ──────────────────────────────────────────── */
        .alert {
            border-radius: var(--radius-sm);
            padding: 13px 18px;
            font-size: .88rem;
            margin-bottom: 22px;
            display: flex; align-items: center; gap: 10px;
            font-weight: 500;
        }
        .alert-success {
            background: var(--success-dim);
            border: 1px solid var(--success-border);
            color: #3D2B1F;
        }
        .alert-danger {
            background: var(--danger-dim);
            border: 1px solid var(--brand-choco);
            color: #3D2B1F;
        }
        .alert ul { list-style: none; padding: 0; margin: 0; }

        /* ── Status chips ────────────────────────────────────── */
        .chip {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px;
            border-radius: var(--radius-pill);
            font-size: .68rem; font-weight: 700;
            letter-spacing: .04em;
            white-space: nowrap;
        }
        .chip-dot { width: 5px; height: 5px; border-radius: 50%; }
        .chip-pending   { background: #F5EDE6; color: #8B5A3C; border: 1px solid #D8C7B0; }
        .chip-pending .chip-dot { background: #8B5A3C; }
        .chip-confirmed { background: #EDE4D8; color: #6B3E1F; border: 1px solid #D8C7B0; }
        .chip-confirmed .chip-dot { background: #6B3E1F; }
        .chip-preparing { background: #E8D5C4; color: #5C4030; border: 1px solid #C4A484; }
        .chip-preparing .chip-dot { background: #5C4030; }
        .chip-ready     { background: #6B3E1F; color: #FFFFFF; border: 1px solid #6B3E1F; }
        .chip-ready .chip-dot { background: #FFFFFF; }
        .chip-completed { background: #3D2B1F; color: #FFFFFF; border: 1px solid #3D2B1F; }
        .chip-completed .chip-dot { background: #FFFFFF; }
        .chip-cancelled { background: #FFFFFF; color: #8A7060; border: 1px solid #D8C7B0; }
        .chip-cancelled .chip-dot { background: #8A7060; }

        /* Active/Hidden chips */
        .chip-active  { background: #6B3E1F; color: #FFFFFF; border: 1px solid #6B3E1F; }
        .chip-active .chip-dot { background: #FFFFFF; }
        .chip-hidden  { background: #FFFFFF; color: #8A7060; border: 1px solid #D8C7B0; }
        .chip-hidden .chip-dot { background: #8A7060; }

        /* ── Tables (Both .table and .saas-table) ─────────────── */
        .saas-table,
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
            background: #FFFFFF;
        }
        .saas-table th,
        .table th {
            font-size: .7rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .08em;
            color: var(--brand-choco);
            padding: 12px 18px;
            border-bottom: 1px solid var(--card-border);
            text-align: left;
            background: #F5EDE6;
        }
        .saas-table td,
        .table td {
            padding: 14px 18px;
            font-size: .875rem;
            color: var(--text-primary);
            border-bottom: 1px solid #D8C7B0;
            vertical-align: middle;
            background: #FFFFFF;
        }
        .saas-table tbody tr:last-child td,
        .table tbody tr:last-child td {
            border-bottom: none;
        }
        .saas-table tbody tr:hover td,
        .table tbody tr:hover td {
            background: #F5EDE6;
        }
        .saas-table a,
        .table a {
            color: var(--brand-choco);
            text-decoration: none;
            font-weight: 600;
        }
        .saas-table a:hover,
        .table a:hover {
            color: var(--brand-dark);
            text-decoration: underline;
        }

        /* ── Forms ───────────────────────────────────────────── */
        .form-label {
            display: block;
            font-size: .75rem; font-weight: 700;
            color: var(--brand-ink-soft);
            margin-bottom: 6px;
            text-transform: uppercase; letter-spacing: .05em;
        }
        .form-control,
        .form-select {
            width: 100%;
            background: #FFFFFF;
            border: 1px solid #D8C7B0;
            border-radius: var(--radius-sm);
            color: var(--brand-ink);
            padding: 9px 13px;
            font-size: .875rem;
            font-family: inherit;
            transition: border-color var(--transition), box-shadow var(--transition);
        }
        .form-control::placeholder { color: #A48F7E; }
        .form-control:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--brand-choco);
            box-shadow: 0 0 0 3px rgba(107,62,31,.14);
            background: #FFFFFF;
        }
        .form-select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236B3E1F' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 36px;
            cursor: pointer;
            appearance: none;
        }
        .form-select option { background: #FFFFFF; color: var(--brand-ink); }

        .form-check { display: flex; align-items: center; gap: 9px; }
        .form-check-input {
            width: 17px; height: 17px;
            accent-color: var(--brand-choco);
            cursor: pointer; flex-shrink: 0;
        }
        .form-check-label { font-size: .875rem; color: var(--brand-ink-soft); cursor: pointer; font-weight: 500; }

        .row { display: flex; flex-wrap: wrap; margin-right: -8px; margin-left: -8px; }
        .g-3 { gap: 16px; }
        .g-4 { gap: 24px; }
        .row.g-3 > *, .row.g-4 > * { padding-right: 8px; padding-left: 8px; }
        .col-12 { flex: 0 0 100% !important; max-width: 100%; }
        .col-md-1 { flex: 0 0 8.333%; max-width: 8.333%; min-width: 90px; }
        .col-md-2 { flex: 0 0 16.666%; max-width: 16.666%; min-width: 130px; }
        .col-md-3 { flex: 0 0 25%; max-width: 25%; min-width: 160px; }
        .col-md-4 { flex: 0 0 33.333%; max-width: 33.333%; min-width: 190px; }
        .col-md-6 { flex: 0 0 50%; max-width: 50%; min-width: 240px; }
        .col-lg-4 { flex: 0 0 33.333%; max-width: 33.333%; min-width: 240px; }
        .col-lg-5 { flex: 0 0 41.666%; max-width: 41.666%; }
        .col-lg-7 { flex: 0 0 58.333%; max-width: 58.333%; }
        .col-lg-8 { flex: 0 0 66.666%; max-width: 66.666%; min-width: 280px; }

        /* ── Buttons (Warm chocolate primary, elegant white ghost) ── */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 7px;
            padding: 9px 18px;
            border-radius: var(--radius-sm);
            font-size: .855rem; font-weight: 600;
            cursor: pointer; border: 1px solid transparent;
            transition: all var(--transition);
            text-decoration: none; font-family: inherit;
            white-space: nowrap;
        }
        .btn-success,
        .btn-primary {
            background: var(--brand-choco);
            color: #FFFFFF !important;
            border-color: var(--brand-choco);
            box-shadow: 0 2px 8px rgba(107,62,31,.2);
        }
        .btn-success:hover,
        .btn-primary:hover {
            background: #522E15;
            border-color: #522E15;
            box-shadow: 0 6px 18px rgba(107,62,31,.3);
            transform: translateY(-1px);
        }
        .btn-ghost,
        .btn-outline-secondary {
            background: #FFFFFF;
            color: var(--brand-ink-soft) !important;
            border: 1px solid var(--card-border);
        }
        .btn-ghost:hover,
        .btn-outline-secondary:hover {
            background: #F5EDE6;
            color: var(--brand-choco) !important;
            border-color: var(--brand-choco);
        }
        .btn-danger,
        .btn-outline-danger {
            background: #FFFFFF;
            color: #3D2B1F !important;
            border: 1px solid #3D2B1F;
        }
        .btn-danger:hover,
        .btn-outline-danger:hover {
            background: #3D2B1F;
            color: #FFFFFF !important;
        }
        .btn-sm { padding: 5px 13px; font-size: .78rem; }

        /* Utility classes */
        .w-100 { width: 100%; }
        .mb-0 { margin-bottom: 0; }
        .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 16px; }
        .mb-4 { margin-bottom: 24px; }
        .mt-4 { margin-top: 24px; }
        .fw-bold { font-weight: 700; }
        .text-muted { color: var(--brand-muted) !important; }
        .text-end { text-align: right; }
        .d-flex { display: flex; }
        .d-inline { display: inline; }
        .d-inline-flex { display: inline-flex; }
        .align-items-center { align-items: center; }
        .align-items-end { align-items: flex-end; }
        .justify-content-between { justify-content: space-between; }
        .gap-2 { gap: 8px; }
        .gap-3 { gap: 12px; }
        .align-middle { vertical-align: middle; }
        .fs-3 { font-size: 1.75rem; }

        /* ── Page header ─────────────────────────────────────── */
        .page-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap; gap: 12px;
        }
        .page-header h1,
        h1.h3 {
            font-size: 1.55rem; font-weight: 700;
            letter-spacing: -.02em;
            color: var(--brand-ink);
            font-family: var(--font-serif);
        }
        .page-header p {
            font-size: .8rem; color: var(--brand-muted); margin-top: 3px;
        }

        /* Panel header */
        .panel-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 22px;
            border-bottom: 1px solid var(--card-border);
            background: #F5EDE6;
        }
        .panel-header h2,
        .stat-card h2 {
            font-size: .95rem; font-weight: 700;
            color: var(--brand-ink);
            display: flex; align-items: center; gap: 8px;
        }
        .panel-header h2 i { color: var(--brand-choco); }

        /* Empty state */
        .empty-state {
            text-align: center; padding: 40px 20px;
            color: var(--brand-muted); font-size: .85rem;
        }
        .empty-state i { font-size: 2.2rem; display: block; margin-bottom: 10px; color: var(--brand-gold); }

        /* Amount */
        .amount-cell { font-weight: 700; color: var(--brand-ink); font-size: .88rem; }

        /* Pagination override */
        .pagination, nav[role="navigation"] { margin-top: 18px; }
        nav[role="navigation"] a,
        nav[role="navigation"] span {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 36px; height: 36px;
            padding: 0 12px;
            border-radius: 8px;
            font-size: .82rem; font-weight: 600;
            margin: 0 2px; text-decoration: none;
            border: 1px solid var(--card-border);
            color: var(--text-secondary);
            background: #FFFFFF;
            transition: all var(--transition);
        }
        nav[role="navigation"] a:hover {
            background: #F5EDE6;
            color: var(--brand-choco);
            border-color: var(--brand-choco);
        }
        nav[role="navigation"] span[aria-current="page"] {
            background: var(--brand-choco);
            color: #FFFFFF;
            border-color: var(--brand-choco);
            box-shadow: 0 2px 8px rgba(107,62,31,.2);
        }
        nav[role="navigation"] span[aria-disabled="true"] { opacity: .4; cursor: not-allowed; }

        /* Fade-in */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in   { animation: fadeInUp .32s ease both; }
        .fade-in-1 { animation-delay: .05s; }
        .fade-in-2 { animation-delay: .10s; }
        .fade-in-3 { animation-delay: .15s; }
        .fade-in-4 { animation-delay: .20s; }
        .fade-in-5 { animation-delay: .25s; }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar-toggle-btn { display: inline-flex; }
            .admin-sidebar { transform: translateX(-100%); }
            .admin-sidebar.open { transform: translateX(0); }
            .admin-wrapper { margin-left: 0; }
            .admin-header { padding: 0 16px; }
            .admin-main { padding: 18px 16px; }
            .admin-header-badge { display: none; }
            .admin-header-greeting h2 { font-size: .95rem; }
            .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-6,
            .col-lg-4, .col-lg-5, .col-lg-7, .col-lg-8 { flex: 0 0 100% !important; max-width: 100% !important; min-width: 0 !important; }
            .page-header { align-items: stretch; }
            .page-header .btn { width: 100%; }
            .btn { min-height: 42px; }
            .saas-table, .table { min-width: 640px; }
        }
        @media (max-width: 575.98px) {
            .admin-main { padding: 14px 12px; }
            .admin-header-greeting p { display: none; }
            h1.h3, .page-header h1 { font-size: 1.3rem; }
            form .d-flex.gap-2 { flex-direction: column; }
            form .d-flex.gap-2 .btn { width: 100%; }
            .form-control, .form-select { min-height: 44px; font-size: 16px; }
        }
    </style>
    @stack('styles')
</head>
<body>

    {{-- ── SIDEBAR ────────────────────────────────────────── --}}
    <aside class="admin-sidebar" id="adminSidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Father Care Bakery" class="sidebar-logo-img">
            <div class="sidebar-logo-text">
                <strong>Father Care</strong>
                <span>Bakery Admin</span>
            </div>
        </a>

        <nav class="sidebar-nav">
            <span class="sidebar-section-label">Overview</span>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="ph ph-gauge"></i> Dashboard
            </a>

            <span class="sidebar-section-label">Catalogue</span>
            <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <i class="ph ph-cookie"></i> Products
            </a>
            <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="ph ph-tag"></i> Categories
            </a>

            <span class="sidebar-section-label">Operations</span>
            <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="ph ph-receipt"></i> Orders
            </a>
            <a href="{{ route('admin.inventory.index') }}" class="{{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                <i class="ph ph-package"></i> Inventory
            </a>
            <a href="{{ route('admin.customers.index') }}" class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                <i class="ph ph-users"></i> Customers
            </a>
            <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="ph ph-chart-line"></i> Reports
            </a>

            <div class="sidebar-divider"></div>
            <a href="{{ route('customer.home') }}" target="_blank">
                <i class="ph ph-storefront"></i> View Storefront
            </a>
        </nav>

        <div class="sidebar-user">
            <div class="sidebar-user-avatar">
                {{ strtoupper(substr(auth()->user()?->name ?? 'A', 0, 1)) }}
            </div>
            <div class="sidebar-user-info">
                <strong>{{ auth()->user()?->name ?? 'Admin' }}</strong>
                <span>Administrator</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-logout-btn" title="Sign out">
                    <i class="ph ph-sign-out"></i>
                </button>
            </form>
        </div>
    </aside>
    <div class="sidebar-overlay" id="sidebarOverlay" hidden></div>

    {{-- ── MAIN WRAPPER ───────────────────────────────────── --}}
    <div class="admin-wrapper">
        <header class="admin-header">
            <button class="sidebar-toggle-btn" id="sidebarToggle" title="Toggle menu">
                <i class="ph ph-list"></i>
            </button>
            <div class="admin-header-greeting">
                <h2 id="headerGreeting">Welcome</h2>
                <p id="headerDate"></p>
            </div>
            <div class="admin-header-badge">LIVE BAKERY</div>
        </header>

        <main class="admin-main">
            @if(session('success'))
                <div class="alert alert-success fade-in">
                    <i class="ph ph-check-circle" style="font-size:1.2rem;"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger fade-in">
                    <i class="ph ph-warning-circle" style="font-size:1.2rem;"></i>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            const greetEl = document.getElementById('headerGreeting');
            const dateEl  = document.getElementById('headerDate');
            const h = new Date().getHours();
            const greet = h < 12 ? 'Good morning' : h < 17 ? 'Good afternoon' : 'Good evening';
            if (greetEl) {
                greetEl.textContent = greet + ', {{ addslashes(auth()->user()?->name ?? "Admin") }} 👋';
            }
            if (dateEl) {
                dateEl.textContent = new Date().toLocaleDateString('en-GB', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
            }

            const toggleBtn = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');

            const closeSidebar = () => {
                sidebar?.classList.remove('open');
                overlay?.classList.remove('show');
                if (overlay) overlay.hidden = true;
            };
            const openSidebar = () => {
                sidebar?.classList.add('open');
                overlay?.classList.add('show');
                if (overlay) overlay.hidden = false;
            };

            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', () => {
                    sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
                });
            }
            overlay?.addEventListener('click', closeSidebar);
            sidebar?.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', closeSidebar);
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>

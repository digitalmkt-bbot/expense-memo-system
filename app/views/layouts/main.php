<?php $u = user(); ?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' — ' : '' ?><?= e(config('app.name')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=IBM+Plex+Sans+Thai:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            /* Design tokens — Modern Light SaaS */
            --emm-bg:           #f6f7fb;
            --emm-surface:      #ffffff;
            --emm-surface-2:    #fbfbfd;
            --emm-border:       #e6e8ef;
            --emm-border-soft:  #eef0f5;
            --emm-text:         #0f172a;
            --emm-text-muted:   #64748b;
            --emm-text-soft:    #94a3b8;
            --emm-primary:      #5b6cff;
            --emm-primary-700:  #4a59e0;
            --emm-primary-50:   #eef0ff;
            --emm-success:      #10b981;
            --emm-success-50:   #ecfdf5;
            --emm-warning:      #f59e0b;
            --emm-warning-50:   #fff7ed;
            --emm-danger:       #ef4444;
            --emm-danger-50:    #fef2f2;
            --emm-info:         #06b6d4;
            --emm-info-50:      #ecfeff;
            --emm-orange:       #f97316;
            --emm-orange-50:    #fff7ed;
            --emm-violet:       #8b5cf6;
            --emm-violet-50:    #f5f3ff;
            --emm-teal:         #14b8a6;
            --emm-teal-50:      #f0fdfa;
            --emm-pink:         #ec4899;
            --emm-pink-50:      #fdf2f8;
            --emm-shadow-sm:    0 1px 2px rgba(15,23,42,.04);
            --emm-shadow:       0 1px 3px rgba(15,23,42,.06), 0 8px 24px rgba(15,23,42,.04);
            --emm-shadow-lg:    0 4px 12px rgba(15,23,42,.08), 0 20px 40px rgba(15,23,42,.06);
            --emm-radius:       12px;
            --emm-radius-lg:    16px;
        }
        * { box-sizing: border-box; }
        html, body { background: var(--emm-bg); color: var(--emm-text); }
        body {
            font-family: 'Inter', 'IBM Plex Sans Thai', -apple-system, BlinkMacSystemFont, "Segoe UI", "Sarabun", Arial, sans-serif;
            font-size: 14px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        h1,h2,h3,h4,h5,h6 { color: var(--emm-text); font-weight: 600; letter-spacing: -.01em; }
        a { color: var(--emm-primary); text-decoration: none; }
        a:hover { color: var(--emm-primary-700); }

        /* Layout shell */
        .emm-app { display: grid; grid-template-columns: 252px 1fr; min-height: 100vh; }

        /* Sidebar */
        .emm-side {
            background: var(--emm-surface);
            border-right: 1px solid var(--emm-border-soft);
            padding: 14px 0;
            position: sticky; top: 0; height: 100vh; overflow-y: auto;
            z-index: 30;
        }
        .emm-backdrop { display: none; }
        .emm-mobile-toggle { display: none; }

        /* Tablet (≤ 991px): collapsible sidebar slides in from left */
        @media (max-width: 991px) {
            .emm-app { grid-template-columns: 1fr; }
            .emm-side {
                position: fixed; top: 0; left: 0;
                width: 280px; max-width: 84vw;
                transform: translateX(-100%);
                transition: transform .25s ease;
                box-shadow: 0 4px 20px rgba(15,23,42,.18);
            }
            body.sidebar-open .emm-side { transform: translateX(0); }
            .emm-backdrop {
                display: block; position: fixed; inset: 0;
                background: rgba(15,23,42,.4); z-index: 25;
                opacity: 0; pointer-events: none;
                transition: opacity .2s ease;
            }
            body.sidebar-open .emm-backdrop { opacity: 1; pointer-events: auto; }
            body.sidebar-open { overflow: hidden; }
            .emm-mobile-toggle {
                display: grid; place-items: center;
                width: 38px; height: 38px; border-radius: 10px;
                border: 1px solid var(--emm-border); background: var(--emm-surface);
                color: var(--emm-text-muted); cursor: pointer; flex-shrink: 0;
            }
        }
        .emm-brand { display: flex; align-items: center; gap: 10px; padding: 8px 18px 18px; }
        .emm-brand .logo {
            width: 34px; height: 34px; border-radius: 10px;
            background: linear-gradient(135deg, var(--emm-primary), #7c8cff);
            color: #fff; display: grid; place-items: center;
            box-shadow: 0 6px 14px rgba(91,108,255,.28);
        }
        .emm-brand .logo i { font-size: 18px; }
        .emm-brand .label { line-height: 1.15; }
        .emm-brand .label strong { display: block; font-weight: 600; font-size: 14px; color: var(--emm-text); }
        .emm-brand .label small { color: var(--emm-text-soft); font-size: 11px; }

        .emm-side .group-title {
            padding: 18px 22px 6px; font-size: 11px; letter-spacing: .08em;
            text-transform: uppercase; color: var(--emm-text-soft); font-weight: 600;
        }
        .emm-side .nav { padding: 0 10px; }
        .emm-side .nav-link {
            display: flex; align-items: center; gap: 11px;
            padding: 9px 12px; border-radius: 10px; margin-bottom: 2px;
            color: var(--emm-text-muted); font-weight: 500; font-size: 13.5px;
            transition: background .15s ease, color .15s ease;
        }
        .emm-side .nav-link i { font-size: 16px; width: 20px; text-align: center; }
        .emm-side .nav-link:hover { background: var(--emm-bg); color: var(--emm-text); }
        .emm-side .nav-link.active {
            background: var(--emm-primary-50); color: var(--emm-primary);
        }
        .emm-side .nav-link.active i { color: var(--emm-primary); }
        .emm-side .badge-count {
            margin-left: auto; background: var(--emm-bg); color: var(--emm-text-muted);
            font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 999px;
        }
        .emm-side .nav-link.active .badge-count { background: #fff; color: var(--emm-primary); }

        /* Topbar */
        .emm-main { display: flex; flex-direction: column; min-width: 0; }
        .emm-top {
            display: flex; align-items: center; justify-content: space-between;
            gap: 12px;
            padding: 14px 28px; background: var(--emm-bg);
            position: sticky; top: 0; z-index: 10;
        }
        .emm-top .top-left { display: flex; align-items: center; gap: 12px; min-width: 0; flex: 1; }
        .emm-top .crumbs { color: var(--emm-text-soft); font-size: 12.5px; margin-bottom: 2px; }
        .emm-top h6 { margin: 0; font-size: 18px; font-weight: 600; color: var(--emm-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .emm-top .actions { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
        .emm-top .icon-btn {
            width: 38px; height: 38px; border-radius: 10px;
            border: 1px solid var(--emm-border); background: var(--emm-surface);
            display: grid; place-items: center; color: var(--emm-text-muted);
            transition: all .15s ease;
        }
        .emm-top .icon-btn:hover { color: var(--emm-text); border-color: var(--emm-text-soft); }
        .emm-top .user-pill {
            display: flex; align-items: center; gap: 10px;
            padding: 5px 14px 5px 5px; border-radius: 999px;
            background: var(--emm-surface); border: 1px solid var(--emm-border);
            cursor: pointer;
        }
        .emm-top .user-pill .avatar {
            width: 28px; height: 28px; border-radius: 50%;
            background: linear-gradient(135deg, #5b6cff, #7c8cff);
            color: #fff; display: grid; place-items: center; font-weight: 600; font-size: 12px; flex-shrink: 0;
        }
        .emm-top .user-pill .meta { line-height: 1.1; }
        .emm-top .user-pill .meta strong { font-size: 13px; color: var(--emm-text); display: block; font-weight: 600; }
        .emm-top .user-pill .meta small { font-size: 11px; color: var(--emm-text-soft); }

        /* Content */
        .emm-content { padding: 8px 28px 36px; }

        /* Tablet/Mobile */
        @media (max-width: 991px) {
            .emm-top { padding: 12px 16px; }
            .emm-content { padding: 8px 16px 32px; }
            .emm-top h6 { font-size: 16px; }
            .emm-top .icon-btn[title="Home"] { display: none; }
            .emm-top .user-pill .meta { display: none; }
            .emm-top .user-pill { padding: 5px; }
        }
        @media (max-width: 575px) {
            .emm-top { padding: 10px 12px; }
            .emm-content { padding: 6px 12px 28px; }
            .emm-top .icon-btn[title="Notifications"] { display: none; }
        }

        /* Cards */
        .emm-card {
            background: var(--emm-surface);
            border: 1px solid var(--emm-border-soft);
            border-radius: var(--emm-radius);
            box-shadow: var(--emm-shadow-sm);
        }
        .emm-card-header {
            padding: 16px 20px; border-bottom: 1px solid var(--emm-border-soft);
            display: flex; align-items: center; justify-content: space-between;
        }
        .emm-card-header h5, .emm-card-header strong { font-size: 14.5px; font-weight: 600; margin: 0; }
        .emm-card-body { padding: 20px; }

        /* KPI Cards */
        .kpi-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 14px; }
        @media (max-width: 1199px) { .kpi-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 575px)  { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }
        .kpi {
            background: var(--emm-surface); border: 1px solid var(--emm-border-soft);
            border-radius: var(--emm-radius); padding: 18px;
            box-shadow: var(--emm-shadow-sm);
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .kpi:hover { transform: translateY(-1px); box-shadow: var(--emm-shadow); }
        .kpi .icon {
            width: 36px; height: 36px; border-radius: 10px;
            display: grid; place-items: center; font-size: 16px;
            margin-bottom: 12px;
        }
        .kpi .icon.primary { background: var(--emm-primary-50); color: var(--emm-primary); }
        .kpi .icon.success { background: var(--emm-success-50); color: var(--emm-success); }
        .kpi .icon.warning { background: var(--emm-warning-50); color: var(--emm-warning); }
        .kpi .icon.danger  { background: var(--emm-danger-50);  color: var(--emm-danger); }
        .kpi .icon.info    { background: var(--emm-info-50);    color: var(--emm-info); }
        .kpi .icon.muted   { background: var(--emm-bg);         color: var(--emm-text-muted); }
        .kpi .icon.orange  { background: var(--emm-orange-50);  color: var(--emm-orange); }
        .kpi .icon.violet  { background: var(--emm-violet-50);  color: var(--emm-violet); }
        .kpi .icon.teal    { background: var(--emm-teal-50);    color: var(--emm-teal); }
        .kpi .icon.pink    { background: var(--emm-pink-50);    color: var(--emm-pink); }
        .kpi .label { color: var(--emm-text-muted); font-size: 12.5px; font-weight: 500; }
        .kpi .value { font-size: 26px; font-weight: 700; color: var(--emm-text); margin-top: 4px; letter-spacing: -.02em; }
        .kpi .delta { font-size: 11.5px; color: var(--emm-text-soft); margin-top: 4px; }
        .kpi .delta.up { color: var(--emm-success); }
        .kpi .delta.down { color: var(--emm-danger); }

        /* Hero KPI cards (bigger, with sparkline) */
        .hero-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
        @media (max-width: 1199px) { .hero-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 575px)  { .hero-grid { grid-template-columns: 1fr; } }
        .hero-kpi {
            background: var(--emm-surface); border: 1px solid var(--emm-border-soft);
            border-radius: var(--emm-radius-lg); padding: 22px;
            box-shadow: var(--emm-shadow-sm);
            position: relative; overflow: hidden;
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .hero-kpi:hover { transform: translateY(-2px); box-shadow: var(--emm-shadow); }
        .hero-kpi.gradient-violet  { background: linear-gradient(135deg, #f5f3ff, #ffffff); border-color: #ddd6fe; }
        .hero-kpi.gradient-orange  { background: linear-gradient(135deg, #fff7ed, #ffffff); border-color: #fed7aa; }
        .hero-kpi.gradient-teal    { background: linear-gradient(135deg, #f0fdfa, #ffffff); border-color: #99f6e4; }
        .hero-kpi.gradient-pink    { background: linear-gradient(135deg, #fdf2f8, #ffffff); border-color: #fbcfe8; }
        .hero-kpi .head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
        .hero-kpi .ico {
            width: 38px; height: 38px; border-radius: 10px;
            display: grid; place-items: center; font-size: 17px;
        }
        .hero-kpi .trend {
            font-size: 11.5px; font-weight: 600; padding: 4px 9px; border-radius: 999px;
            display: inline-flex; align-items: center; gap: 4px;
        }
        .hero-kpi .trend.up   { background: var(--emm-success-50); color: var(--emm-success); }
        .hero-kpi .trend.down { background: var(--emm-danger-50);  color: var(--emm-danger); }
        .hero-kpi .trend.flat { background: var(--emm-bg); color: var(--emm-text-muted); }
        .hero-kpi .lab { color: var(--emm-text-muted); font-size: 13px; font-weight: 500; }
        .hero-kpi .num { font-size: 30px; font-weight: 700; letter-spacing: -.025em; margin-top: 4px; }
        .hero-kpi .sub { color: var(--emm-text-soft); font-size: 12px; margin-top: 4px; }
        .hero-kpi .spark {
            position: absolute; right: -10px; bottom: -10px; opacity: .55; pointer-events: none;
        }

        /* Period selector */
        .period-pills { display: inline-flex; gap: 4px; background: var(--emm-surface); border: 1px solid var(--emm-border); border-radius: 10px; padding: 3px; }
        .period-pills a { padding: 6px 12px; font-size: 12.5px; font-weight: 500; color: var(--emm-text-muted); border-radius: 7px; }
        .period-pills a.active { background: var(--emm-primary); color: #fff; }

        /* Chart container */
        .chart-card { background: var(--emm-surface); border: 1px solid var(--emm-border-soft); border-radius: var(--emm-radius); }
        .chart-head { padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--emm-border-soft); }
        .chart-head .ttl { font-weight: 600; font-size: 14.5px; }
        .chart-head .ttl small { display: block; font-weight: 400; font-size: 12px; color: var(--emm-text-soft); margin-top: 2px; }
        .chart-body { padding: 18px 20px 14px; }

        /* Activity feed */
        .activity-item {
            display: flex; gap: 12px; padding: 12px 16px;
            border-bottom: 1px solid var(--emm-border-soft);
        }
        .activity-item:last-child { border-bottom: 0; }
        .activity-item .dot {
            flex-shrink: 0; width: 30px; height: 30px; border-radius: 8px;
            display: grid; place-items: center; font-size: 12px;
        }
        .activity-item .dot.approved      { background: var(--emm-success-50); color: var(--emm-success); }
        .activity-item .dot.rejected      { background: var(--emm-danger-50); color: var(--emm-danger); }
        .activity-item .dot.submitted     { background: var(--emm-primary-50); color: var(--emm-primary); }
        .activity-item .dot.revision_required { background: var(--emm-warning-50); color: var(--emm-warning); }
        .activity-item .ttext { flex: 1; min-width: 0; }
        .activity-item .ttext strong { font-size: 13px; display: block; line-height: 1.3; }
        .activity-item .ttext small { color: var(--emm-text-soft); font-size: 11.5px; }

        /* Top categories list */
        .cat-row { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--emm-border-soft); }
        .cat-row:last-child { border-bottom: 0; }
        .cat-row .cat-name { flex: 1; min-width: 0; font-size: 13px; }
        .cat-row .cat-name small { color: var(--emm-text-soft); font-size: 11px; display: block; }
        .cat-row .bar { flex: 1.5; height: 6px; background: var(--emm-bg); border-radius: 999px; overflow: hidden; }
        .cat-row .bar > span { display: block; height: 100%; border-radius: 999px;
            background: linear-gradient(90deg, var(--emm-primary), #8b5cf6);
        }
        .cat-row:nth-child(2n) .bar > span { background: linear-gradient(90deg, var(--emm-orange), #f59e0b); }
        .cat-row:nth-child(3n) .bar > span { background: linear-gradient(90deg, var(--emm-teal), #06b6d4); }
        .cat-row:nth-child(4n) .bar > span { background: linear-gradient(90deg, var(--emm-pink), #f472b6); }
        .cat-row .cat-amt { width: 110px; text-align: right; font-size: 13px; font-weight: 600; font-variant-numeric: tabular-nums; }

        /* Buttons */
        .btn {
            font-weight: 500; font-size: 13.5px; border-radius: 10px;
            padding: 8px 16px; transition: all .15s ease; border: 1px solid transparent;
        }
        .btn-primary {
            background: var(--emm-primary); border-color: var(--emm-primary); color: #fff;
        }
        .btn-primary:hover { background: var(--emm-primary-700); border-color: var(--emm-primary-700); color: #fff; }
        .btn-light {
            background: var(--emm-surface); border-color: var(--emm-border); color: var(--emm-text);
        }
        .btn-light:hover { background: var(--emm-bg); border-color: var(--emm-text-soft); }
        .btn-outline-primary { color: var(--emm-primary); border-color: var(--emm-primary); }
        .btn-outline-primary:hover { background: var(--emm-primary); color: #fff; }
        .btn-outline-secondary { color: var(--emm-text-muted); border-color: var(--emm-border); background: var(--emm-surface); }
        .btn-outline-secondary:hover { background: var(--emm-bg); color: var(--emm-text); border-color: var(--emm-text-soft); }
        .btn-success { background: var(--emm-success); border-color: var(--emm-success); color: #fff; }
        .btn-success:hover { background: #059669; border-color: #059669; color: #fff; }
        .btn-warning { background: var(--emm-warning); border-color: var(--emm-warning); color: #1f2937; }
        .btn-warning:hover { background: #d97706; border-color: #d97706; color: #fff; }
        .btn-danger { background: var(--emm-danger); border-color: var(--emm-danger); color: #fff; }
        .btn-outline-danger { color: var(--emm-danger); border-color: #fecaca; background: var(--emm-surface); }
        .btn-outline-danger:hover { background: var(--emm-danger); color: #fff; border-color: var(--emm-danger); }
        .btn-outline-warning { color: #b45309; border-color: #fed7aa; background: var(--emm-surface); }
        .btn-outline-warning:hover { background: var(--emm-warning); color: #fff; }
        .btn-dark { background: var(--emm-text); border-color: var(--emm-text); color: #fff; }
        .btn-sm { padding: 6px 12px; font-size: 12.5px; border-radius: 8px; }
        .btn-lg { padding: 12px 22px; font-size: 14.5px; border-radius: 12px; }

        /* Form controls */
        .form-control, .form-select {
            border: 1px solid var(--emm-border);
            border-radius: 10px; font-size: 13.5px; padding: 9px 12px;
            background: var(--emm-surface); color: var(--emm-text);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--emm-primary);
            box-shadow: 0 0 0 4px var(--emm-primary-50);
        }
        .form-control-sm, .form-select-sm { padding: 7px 10px; font-size: 12.5px; border-radius: 8px; }
        .form-label { font-size: 12.5px; font-weight: 500; color: var(--emm-text-muted); margin-bottom: 5px; }

        /* Tables */
        .emm-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .emm-table thead th {
            background: var(--emm-surface-2); color: var(--emm-text-soft);
            font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .04em;
            padding: 11px 14px; border-bottom: 1px solid var(--emm-border-soft); text-align: left;
        }
        .emm-table tbody td {
            padding: 13px 14px; border-bottom: 1px solid var(--emm-border-soft);
            font-size: 13.5px; color: var(--emm-text); vertical-align: middle;
        }
        .emm-table tbody tr:last-child td { border-bottom: 0; }
        .emm-table tbody tr:hover td { background: var(--emm-surface-2); }
        .emm-table .text-end { text-align: right; }
        .emm-table .text-center { text-align: center; }
        .emm-table tfoot td {
            padding: 12px 14px; background: var(--emm-surface-2);
            border-top: 1px solid var(--emm-border-soft); font-weight: 600;
        }

        /* Status pills */
        .status {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 999px; font-size: 11.5px; font-weight: 500;
            background: var(--emm-bg); color: var(--emm-text-muted);
        }
        .status::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
        .status.draft     { background: #f1f5f9; color: #475569; }
        .status.submitted { background: var(--emm-primary-50); color: var(--emm-primary); }
        .status.manager_approved,
        .status.accounting_checked,
        .status.director_approved { background: var(--emm-info-50); color: #0e7490; }
        .status.approved  { background: var(--emm-success-50); color: #047857; }
        .status.revision_required,
        .status.pending_payment,
        .status.partially_paid { background: var(--emm-warning-50); color: #b45309; }
        .status.rejected  { background: var(--emm-danger-50); color: #b91c1c; }
        .status.paid      { background: var(--emm-success-50); color: #047857; }
        .status.closed    { background: #f1f5f9; color: #1e293b; }
        .status.cancelled { background: #f1f5f9; color: #94a3b8; }
        .status.unpaid    { background: var(--emm-bg); color: var(--emm-text-muted); }

        .role-tag {
            display: inline-block; padding: 1px 8px; border-radius: 999px;
            font-size: 10.5px; font-weight: 600; text-transform: uppercase; letter-spacing: .03em;
            background: var(--emm-primary-50); color: var(--emm-primary);
        }

        /* Section title */
        .page-title { display: flex; align-items: center; justify-content: space-between; margin: 8px 0 18px; gap: 12px; flex-wrap: wrap; }
        .page-title h3 { font-size: 22px; font-weight: 600; margin: 0; letter-spacing: -.01em; }
        .page-title .meta { font-size: 13px; color: var(--emm-text-soft); }
        @media (max-width: 575px) {
            .page-title h3 { font-size: 18px; }
            .page-title .meta { font-size: 12px; }
            .page-title { margin: 4px 0 14px; }
        }

        /* Avatar circle */
        .avatar-sm { width: 24px; height: 24px; border-radius: 50%;
            background: var(--emm-primary-50); color: var(--emm-primary);
            display: inline-grid; place-items: center; font-size: 11px; font-weight: 600; }

        /* Alerts */
        .alert {
            border: 1px solid transparent; border-radius: 10px;
            padding: 11px 14px; font-size: 13.5px;
        }
        .alert-success { background: var(--emm-success-50); color: #047857; border-color: #d1fae5; }
        .alert-info    { background: var(--emm-info-50);    color: #0e7490; border-color: #cffafe; }
        .alert-warning { background: var(--emm-warning-50); color: #b45309; border-color: #fed7aa; }
        .alert-danger  { background: var(--emm-danger-50);  color: #b91c1c; border-color: #fecaca; }

        /* Tabs */
        .emm-tabs { display: flex; gap: 4px; border-bottom: 1px solid var(--emm-border-soft); margin-bottom: 18px; }
        .emm-tabs a {
            padding: 10px 14px; font-size: 13.5px; color: var(--emm-text-muted);
            font-weight: 500; border-bottom: 2px solid transparent; margin-bottom: -1px;
        }
        .emm-tabs a:hover { color: var(--emm-text); }
        .emm-tabs a.active { color: var(--emm-primary); border-color: var(--emm-primary); }

        /* Sticky form panels on desktop only */
        @media (min-width: 992px) {
            .sticky-on-desktop { position: sticky; top: 80px; }
        }

        /* Misc */
        .text-muted { color: var(--emm-text-muted) !important; }
        .text-soft  { color: var(--emm-text-soft); }
        .divider    { height: 1px; background: var(--emm-border-soft); margin: 12px 0; }
        .money      { font-variant-numeric: tabular-nums; font-weight: 600; }
        hr { border-color: var(--emm-border-soft); }

        /* ─── Responsive overrides ─── */
        @media (max-width: 991px) {
            /* Hero KPI: 2 columns on tablet */
            .hero-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .hero-kpi { padding: 18px; }
            .hero-kpi .num { font-size: 24px; }
            .hero-kpi .spark { width: 120px; height: 36px; }

            /* KPI grid */
            .kpi-grid { gap: 10px; }
            .kpi { padding: 14px; }
            .kpi .value { font-size: 22px; }

            /* Card paddings */
            .emm-card-body { padding: 14px; }
            .emm-card-header { padding: 12px 14px; }

            /* Tables: smaller padding/font */
            .emm-table thead th { padding: 9px 10px; font-size: 10.5px; }
            .emm-table tbody td { padding: 10px 10px; font-size: 12.5px; }

            /* Period pills wrap */
            .period-pills { flex-wrap: wrap; }

            /* Chart card body */
            .chart-body { padding: 12px 14px 10px; }

            /* Footer area on memo edit */
            .row.g-3 > .col-lg-8, .row.g-3 > .col-lg-4 { width: 100%; }
        }
        @media (max-width: 575px) {
            /* Mobile single column */
            .hero-grid { grid-template-columns: 1fr; gap: 10px; }
            .hero-kpi .spark { display: none; }

            /* KPI 2 columns on phone */
            .kpi-grid { grid-template-columns: repeat(2, 1fr); }

            /* Buttons smaller */
            .btn { padding: 7px 12px; font-size: 12.5px; }
            .btn-sm { padding: 5px 10px; font-size: 11.5px; }
            .btn-lg { padding: 10px 18px; font-size: 13.5px; }

            /* Tables really compact */
            .emm-table thead th { padding: 8px 8px; font-size: 10px; }
            .emm-table tbody td { padding: 9px 8px; font-size: 12px; }

            /* Page-title: stack actions */
            .page-title > div:last-child { width: 100%; }
            .page-title .d-flex { flex-wrap: wrap; }

            /* Hide some columns on phone — using attribute selector for opt-in */
            .emm-table th.hide-sm, .emm-table td.hide-sm { display: none; }

            /* Form labels smaller */
            .form-label { font-size: 11.5px; }

            /* Activity feed */
            .activity-item { padding: 10px 12px; }
            .activity-item .ttext strong { font-size: 12.5px; }
            .activity-item .ttext small { font-size: 11px; }

            /* Status pills more compact */
            .status { font-size: 10.5px; padding: 2px 8px; }
            .role-tag { font-size: 9.5px; padding: 1px 6px; }
        }
    </style>
</head>
<body>
<div class="emm-backdrop" id="sidebarBackdrop"></div>
<div class="emm-app">
    <?php $cur = $_SERVER['REQUEST_URI']; $isActive = fn($p) => str_contains($cur, $p) ? 'active' : ''; ?>

    <aside class="emm-side" id="sidebar">
        <div class="emm-brand">
            <div class="logo"><i class="bi bi-receipt-cutoff"></i></div>
            <div class="label"><strong>Memo System</strong><small>LOVE ISLAND · ANDAMAN</small></div>
        </div>

        <nav class="nav flex-column">
            <a class="nav-link <?= str_ends_with($cur, '/dashboard') || str_ends_with($cur, '/') ? 'active' : '' ?>" href="<?= url('/dashboard') ?>">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>
        </nav>

        <div class="group-title">Memos</div>
        <nav class="nav flex-column">
            <a class="nav-link <?= preg_match('#/memos$|/memos\?#', $cur) ? 'active' : '' ?>" href="<?= url('/memos') ?>">
                <i class="bi bi-files"></i> My Memos
            </a>
            <a class="nav-link <?= str_contains($cur, '/memos/create') ? 'active' : '' ?>" href="<?= url('/memos/create') ?>">
                <i class="bi bi-plus-circle"></i> Create Memo
            </a>
        </nav>

        <?php if (in_array($u['role'], ['manager','director','accounting','admin'])): ?>
            <div class="group-title">Workflow</div>
            <nav class="nav flex-column">
                <a class="nav-link <?= str_contains($cur, '/approvals') ? 'active' : '' ?>" href="<?= url('/approvals') ?>">
                    <i class="bi bi-check2-square"></i> Approvals
                </a>
                <?php if (in_array($u['role'], ['accounting','admin'])): ?>
                    <a class="nav-link <?= str_contains($cur, '/payments') ? 'active' : '' ?>" href="<?= url('/payments') ?>">
                        <i class="bi bi-cash-stack"></i> Payments
                    </a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>

        <div class="group-title">Insights</div>
        <nav class="nav flex-column">
            <a class="nav-link <?= str_contains($cur, '/reports') ? 'active' : '' ?>" href="<?= url('/reports') ?>">
                <i class="bi bi-bar-chart"></i> Reports
            </a>
        </nav>

        <?php if ($u['role'] === 'admin'): ?>
            <div class="group-title">Master Data</div>
            <nav class="nav flex-column">
                <a class="nav-link <?= str_contains($cur, '/master/users') ? 'active' : '' ?>" href="<?= url('/master/users') ?>"><i class="bi bi-people"></i> Users</a>
                <a class="nav-link <?= str_contains($cur, '/master/categories') ? 'active' : '' ?>" href="<?= url('/master/categories') ?>"><i class="bi bi-tags"></i> Categories</a>
                <a class="nav-link <?= str_contains($cur, '/master/suppliers') ? 'active' : '' ?>" href="<?= url('/master/suppliers') ?>"><i class="bi bi-truck"></i> Suppliers</a>
                <a class="nav-link <?= str_contains($cur, '/master/projects') ? 'active' : '' ?>" href="<?= url('/master/projects') ?>"><i class="bi bi-folder"></i> Projects</a>
                <a class="nav-link <?= str_contains($cur, '/master/approval-rules') ? 'active' : '' ?>" href="<?= url('/master/approval-rules') ?>"><i class="bi bi-diagram-3"></i> Approval Rules</a>
            </nav>
        <?php endif; ?>
    </aside>

    <div class="emm-main">
        <header class="emm-top">
            <div class="top-left">
                <button class="emm-mobile-toggle" id="sidebarToggle" aria-label="Toggle menu">
                    <i class="bi bi-list" style="font-size: 20px;"></i>
                </button>
                <div style="min-width: 0;">
                    <?php if (!empty($pageBreadcrumb)): ?><div class="crumbs"><?= e($pageBreadcrumb) ?></div><?php endif; ?>
                    <h6><?= isset($pageTitle) ? e($pageTitle) : '' ?></h6>
                </div>
            </div>
            <div class="actions">
                <button class="icon-btn" title="Notifications"><i class="bi bi-bell"></i></button>
                <a class="icon-btn" href="<?= url('/dashboard') ?>" title="Home"><i class="bi bi-house"></i></a>
                <div class="dropdown">
                    <button class="user-pill" data-bs-toggle="dropdown">
                        <span class="avatar"><?= strtoupper(substr($u['full_name'] ?? 'U', 0, 1)) ?></span>
                        <span class="meta">
                            <strong><?= e($u['full_name']) ?></strong>
                            <small><?= e($u['role']) ?></small>
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text small text-muted"><?= e($u['email']) ?></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= url('/logout') ?>"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="emm-content">
            <?php foreach ((array) flash() as $type => $msg): ?>
                <div class="alert alert-<?= $type === 'error' ? 'danger' : e($type) ?> mb-3">
                    <?= e($msg) ?>
                </div>
            <?php endforeach; $_SESSION['_flash'] = []; ?>

            <?= $content ?>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
// Mobile sidebar toggle
(function () {
    var toggle = document.getElementById('sidebarToggle');
    var backdrop = document.getElementById('sidebarBackdrop');
    var body = document.body;
    function openSidebar()  { body.classList.add('sidebar-open'); }
    function closeSidebar() { body.classList.remove('sidebar-open'); }
    if (toggle)   toggle.addEventListener('click', function (e) { e.stopPropagation(); openSidebar(); });
    if (backdrop) backdrop.addEventListener('click', closeSidebar);
    // Close sidebar when clicking a nav-link (mobile)
    document.querySelectorAll('.emm-side .nav-link').forEach(function (a) {
        a.addEventListener('click', function () {
            if (window.innerWidth <= 991) closeSidebar();
        });
    });
    // Close on resize to desktop
    window.addEventListener('resize', function () {
        if (window.innerWidth > 991) closeSidebar();
    });
})();
</script>
</body>
</html>

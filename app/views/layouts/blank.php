<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(config('app.name')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=IBM+Plex+Sans+Thai:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --emm-bg: #f6f7fb; --emm-surface: #fff; --emm-text: #0f172a;
            --emm-text-muted: #64748b; --emm-text-soft: #94a3b8;
            --emm-border: #e6e8ef; --emm-primary: #5b6cff; --emm-primary-700: #4a59e0;
            --emm-primary-50: #eef0ff; --emm-success: #10b981;
            --emm-success-50: #ecfdf5; --emm-danger: #ef4444; --emm-danger-50: #fef2f2;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh; background: var(--emm-bg);
            font-family: 'Inter', 'IBM Plex Sans Thai', -apple-system, BlinkMacSystemFont, "Sarabun", sans-serif;
            color: var(--emm-text); -webkit-font-smoothing: antialiased;
        }
        .auth { display: grid; grid-template-columns: 1fr 1.1fr; min-height: 100vh; }
        @media (max-width: 991px) { .auth { grid-template-columns: 1fr; } .auth-hero { display: none; } }

        .auth-form { display: flex; align-items: center; justify-content: center; padding: 32px; }
        .auth-form .panel {
            width: 100%; max-width: 400px;
        }
        .brand-row { display: flex; align-items: center; gap: 12px; margin-bottom: 38px; }
        .brand-logo {
            width: 40px; height: 40px; border-radius: 12px;
            background: linear-gradient(135deg, #5b6cff, #7c8cff);
            display: grid; place-items: center; color: #fff; box-shadow: 0 8px 18px rgba(91,108,255,.25);
        }
        .brand-logo i { font-size: 19px; }
        .brand-name strong { display: block; font-size: 15px; font-weight: 600; }
        .brand-name small { color: var(--emm-text-soft); font-size: 11.5px; }

        h1.title { font-size: 28px; font-weight: 700; margin: 0 0 8px; letter-spacing: -.02em; }
        p.subtitle { color: var(--emm-text-muted); margin: 0 0 28px; font-size: 14px; }

        .form-control {
            border: 1px solid var(--emm-border); border-radius: 10px;
            padding: 11px 14px; font-size: 14px; width: 100%;
            background: var(--emm-surface); color: var(--emm-text);
        }
        .form-control:focus { outline: none; border-color: var(--emm-primary); box-shadow: 0 0 0 4px var(--emm-primary-50); }
        .form-label { font-size: 12.5px; font-weight: 500; color: var(--emm-text-muted); margin-bottom: 6px; display: block; }

        .btn-primary {
            background: var(--emm-primary); color: #fff; border: 0;
            padding: 12px; border-radius: 10px; font-weight: 500; font-size: 14px;
            width: 100%; cursor: pointer; transition: background .15s ease;
        }
        .btn-primary:hover { background: var(--emm-primary-700); }
        .alert {
            border: 1px solid; border-radius: 10px; padding: 10px 14px; font-size: 13px; margin-bottom: 16px;
        }
        .alert-danger  { background: var(--emm-danger-50);  color: #b91c1c; border-color: #fecaca; }
        .alert-success { background: var(--emm-success-50); color: #047857; border-color: #d1fae5; }

        .demo-pills {
            display: flex; flex-wrap: wrap; gap: 6px; margin-top: 14px;
        }
        .demo-pills span {
            background: var(--emm-bg); color: var(--emm-text-muted);
            border: 1px solid var(--emm-border); border-radius: 999px;
            padding: 4px 10px; font-size: 11.5px;
            cursor: pointer; transition: all .15s ease;
        }
        .demo-pills span:hover { color: var(--emm-primary); border-color: var(--emm-primary); background: var(--emm-primary-50); }

        /* Hero side */
        .auth-hero {
            background: radial-gradient(at 20% 20%, #6873ff 0%, transparent 50%),
                        radial-gradient(at 80% 70%, #8b5cf6 0%, transparent 50%),
                        linear-gradient(135deg, #4a59e0 0%, #6366f1 60%, #8b5cf6 100%);
            color: #fff; position: relative; overflow: hidden;
            display: flex; align-items: center; justify-content: center; padding: 64px;
        }
        .auth-hero::before {
            content: ''; position: absolute; inset: 0;
            background:
              radial-gradient(circle at 30% 90%, rgba(255,255,255,.08) 0, transparent 30%),
              radial-gradient(circle at 80% 10%, rgba(255,255,255,.10) 0, transparent 25%);
            pointer-events: none;
        }
        .auth-hero .inner { position: relative; max-width: 460px; }
        .auth-hero h2 {
            font-size: 34px; font-weight: 700; line-height: 1.2;
            margin: 0 0 16px; letter-spacing: -.02em;
        }
        .auth-hero p { font-size: 15px; opacity: .85; line-height: 1.6; }
        .auth-hero .feature {
            display: flex; align-items: flex-start; gap: 12px; margin-top: 16px;
        }
        .auth-hero .feature i {
            width: 32px; height: 32px; border-radius: 8px;
            background: rgba(255,255,255,.15); display: grid; place-items: center;
            font-size: 14px; flex-shrink: 0;
        }
        .auth-hero .feature .text { font-size: 13.5px; opacity: .9; line-height: 1.45; }
        .auth-hero .feature .text strong { display: block; font-weight: 600; opacity: 1; margin-bottom: 1px; font-size: 14px; }

        .footer-note {
            margin-top: 28px; padding-top: 18px; border-top: 1px solid var(--emm-border);
            font-size: 11.5px; color: var(--emm-text-soft);
        }
        .footer-note code { background: var(--emm-bg); padding: 2px 6px; border-radius: 5px; font-size: 11px; color: var(--emm-text); }
    </style>
</head>
<body>
<?= $content ?>
</body>
</html>

<?php $u = user(); ?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' — ' : '' ?><?= e(config('app.name')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --emm-sidebar-bg: #1f2a44;
            --emm-sidebar-fg: #cfd6e4;
            --emm-accent: #4f7cff;
        }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Sarabun", Arial, sans-serif; background: #f5f6f8; }
        .emm-wrap { display: flex; min-height: 100vh; }
        .emm-side { width: 250px; background: var(--emm-sidebar-bg); color: var(--emm-sidebar-fg); flex-shrink: 0; }
        .emm-side .brand { padding: 20px 18px; border-bottom: 1px solid rgba(255,255,255,.07); }
        .emm-side .brand h5 { color: #fff; margin: 0; font-weight: 600; font-size: 16px; }
        .emm-side .brand small { color: #97a3bd; }
        .emm-side .nav-link { color: var(--emm-sidebar-fg); padding: 9px 18px; border-left: 3px solid transparent; }
        .emm-side .nav-link:hover, .emm-side .nav-link.active { background: rgba(255,255,255,.05); color: #fff; border-left-color: var(--emm-accent); }
        .emm-side .nav-link i { width: 20px; }
        .emm-side .group-title { padding: 18px 18px 6px; font-size: 11px; text-transform: uppercase; color: #6b7898; letter-spacing: .05em; }
        .emm-main { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .emm-topbar { background: #fff; border-bottom: 1px solid #e5e7eb; padding: 10px 20px; display: flex; align-items: center; justify-content: space-between; }
        .emm-content { padding: 24px; flex: 1; }
        .table-sm td, .table-sm th { padding: 8px 10px; font-size: 14px; }
        .card { border: 1px solid #e5e7eb; box-shadow: 0 1px 2px rgba(0,0,0,.03); }
        .form-label { font-weight: 500; font-size: 14px; }
    </style>
</head>
<body>
<div class="emm-wrap">
    <aside class="emm-side">
        <div class="brand">
            <h5><i class="bi bi-receipt-cutoff"></i> Memo System</h5>
            <small>LOVE ISLAND / ANDAMAN</small>
        </div>
        <nav class="nav flex-column py-2">
            <?php $cur = $_SERVER['REQUEST_URI']; $isActive = fn($p) => str_contains($cur, $p) ? 'active' : ''; ?>

            <a class="nav-link <?= $isActive('/dashboard') ?>" href="<?= url('/dashboard') ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <div class="group-title">Memo</div>
            <a class="nav-link <?= $isActive('/memos') ?>" href="<?= url('/memos') ?>">
                <i class="bi bi-file-earmark-text"></i> My Memos
            </a>
            <a class="nav-link <?= $isActive('/memos/create') ?>" href="<?= url('/memos/create') ?>">
                <i class="bi bi-plus-circle"></i> Create Memo
            </a>

            <?php if (in_array($u['role'], ['manager','director','accounting','admin'])): ?>
                <div class="group-title">Approval</div>
                <a class="nav-link <?= $isActive('/approvals') ?>" href="<?= url('/approvals') ?>">
                    <i class="bi bi-check2-square"></i> Pending Approval
                </a>
            <?php endif; ?>

            <?php if (in_array($u['role'], ['accounting','admin'])): ?>
                <div class="group-title">Accounting</div>
                <a class="nav-link <?= $isActive('/payments') ?>" href="<?= url('/payments') ?>">
                    <i class="bi bi-cash-stack"></i> Payments
                </a>
            <?php endif; ?>

            <div class="group-title">Reports</div>
            <a class="nav-link <?= $isActive('/reports') ?>" href="<?= url('/reports') ?>">
                <i class="bi bi-bar-chart"></i> Reports
            </a>

            <?php if ($u['role'] === 'admin'): ?>
                <div class="group-title">Master Data</div>
                <a class="nav-link" href="<?= url('/master/users') ?>"><i class="bi bi-people"></i> Users</a>
                <a class="nav-link" href="<?= url('/master/categories') ?>"><i class="bi bi-tags"></i> Categories</a>
                <a class="nav-link" href="<?= url('/master/suppliers') ?>"><i class="bi bi-truck"></i> Suppliers</a>
                <a class="nav-link" href="<?= url('/master/projects') ?>"><i class="bi bi-folder"></i> Projects</a>
                <a class="nav-link" href="<?= url('/master/approval-rules') ?>"><i class="bi bi-diagram-3"></i> Approval Rules</a>
            <?php endif; ?>
        </nav>
    </aside>

    <div class="emm-main">
        <header class="emm-topbar">
            <div>
                <h6 class="mb-0"><?= isset($pageTitle) ? e($pageTitle) : '' ?></h6>
                <?php if (isset($pageSubtitle)): ?><small class="text-muted"><?= e($pageSubtitle) ?></small><?php endif; ?>
            </div>
            <div class="dropdown">
                <button class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i>
                    <?= e($u['full_name']) ?>
                    <span class="badge bg-secondary ms-1"><?= e($u['role']) ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-item-text small text-muted"><?= e($u['email']) ?></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?= url('/logout') ?>"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                </ul>
            </div>
        </header>

        <main class="emm-content">
            <?php foreach ((array) flash() as $type => $msg): ?>
                <div class="alert alert-<?= $type === 'error' ? 'danger' : e($type) ?> alert-dismissible fade show">
                    <?= e($msg) ?>
                    <button class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; $_SESSION['_flash'] = []; ?>

            <?= $content ?>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

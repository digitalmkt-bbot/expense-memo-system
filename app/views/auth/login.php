<div class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card shadow-sm login-card w-100">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <div class="display-6 mb-2"><i class="bi bi-receipt-cutoff text-primary"></i></div>
                <h4 class="mb-0">Expense Memo System</h4>
                <small class="text-muted">LOVE ISLAND / ANDAMAN SUNDAY</small>
            </div>

            <?php if ($err = flash('error')): ?>
                <div class="alert alert-danger py-2"><?= e($err) ?></div>
            <?php endif; ?>
            <?php if ($ok = flash('success')): ?>
                <div class="alert alert-success py-2"><?= e($ok) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= url('/login') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-in-right"></i> เข้าสู่ระบบ / Sign in
                </button>
            </form>

            <hr class="my-4">
            <small class="text-muted d-block mb-1"><strong>Demo accounts</strong> (password = <code>password123</code>):</small>
            <small class="text-muted d-block">admin@loveandaman.com (Admin)</small>
            <small class="text-muted d-block">mkt.staff@loveandaman.com (Requester)</small>
            <small class="text-muted d-block">mkt.manager@loveandaman.com (Manager)</small>
            <small class="text-muted d-block">acc.lo@loveandaman.com (Accounting)</small>
            <small class="text-muted d-block">director.lo@loveandaman.com (Director)</small>
        </div>
    </div>
</div>

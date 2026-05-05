<div class="auth">
    <div class="auth-form">
        <div class="panel">
            <div class="brand-row">
                <div class="brand-logo"><i class="bi bi-receipt-cutoff"></i></div>
                <div class="brand-name">
                    <strong>Memo System</strong>
                    <small>LOVE ISLAND · ANDAMAN</small>
                </div>
            </div>

            <h1 class="title">Welcome back</h1>
            <p class="subtitle">เข้าสู่ระบบเพื่อจัดการ Memo ค่าใช้จ่ายขององค์กร</p>

            <?php if ($err = flash('error')): ?>
                <div class="alert alert-danger"><i class="bi bi-exclamation-circle"></i> <?= e($err) ?></div>
            <?php endif; ?>
            <?php if ($ok = flash('success')): ?>
                <div class="alert alert-success"><i class="bi bi-check-circle"></i> <?= e($ok) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= url('/login') ?>">
                <?= csrf_field() ?>
                <div style="margin-bottom: 14px;">
                    <label class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control" required autofocus placeholder="you@loveandaman.com">
                </div>
                <div style="margin-bottom: 18px;">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn-primary">
                    <i class="bi bi-arrow-right-short"></i> เข้าสู่ระบบ / Sign in
                </button>
            </form>

            <div class="footer-note">
                <strong>Demo accounts</strong> — password <code>password123</code>
                <div class="demo-pills">
                    <span onclick="fill('admin@loveandaman.com')">admin (Admin)</span>
                    <span onclick="fill('mkt.staff@loveandaman.com')">mkt.staff (Requester)</span>
                    <span onclick="fill('mkt.manager@loveandaman.com')">mkt.manager (Manager)</span>
                    <span onclick="fill('acc.lo@loveandaman.com')">acc.lo (Accounting)</span>
                    <span onclick="fill('director.lo@loveandaman.com')">director.lo (Director)</span>
                </div>
            </div>
        </div>
    </div>

    <aside class="auth-hero">
        <div class="inner">
            <h2>Streamline your expense memos in minutes, not days.</h2>
            <p>ระบบ Memo ค่าใช้จ่ายครบวงจร — Approval workflow, payment tracking, และ reports สำหรับ LOVE ISLAND และ ANDAMAN SUNDAY</p>

            <div style="margin-top: 28px;">
                <div class="feature">
                    <i class="bi bi-files"></i>
                    <div class="text"><strong>Auto-generate Memo No.</strong>Format <code style="opacity:.85; background: rgba(255,255,255,.1); padding: 1px 6px; border-radius: 4px;">LO-MKT-2026-05-001</code></div>
                </div>
                <div class="feature">
                    <i class="bi bi-shield-check"></i>
                    <div class="text"><strong>3-tier approval</strong>Manager → Accounting → Director ตาม amount tier</div>
                </div>
                <div class="feature">
                    <i class="bi bi-graph-up-arrow"></i>
                    <div class="text"><strong>Reports & insights</strong>By Company, Department, Category, Monthly Summary</div>
                </div>
                <div class="feature">
                    <i class="bi bi-cash-coin"></i>
                    <div class="text"><strong>Payment tracking</strong>Record payments, upload slips, partial payments รองรับ</div>
                </div>
            </div>
        </div>
    </aside>
</div>

<script>
function fill(email) {
    document.querySelector('input[name="email"]').value = email;
    document.querySelector('input[name="password"]').value = 'password123';
    document.querySelector('input[name="password"]').focus();
}
</script>

<div class="page-title">
    <div>
        <h3>Memos</h3>
        <div class="meta">รายการ Memo ค่าใช้จ่ายทั้งหมด</div>
    </div>
    <div>
        <a href="<?= url('/memos/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Create Memo
        </a>
    </div>
</div>

<form method="get" class="emm-card mb-3">
    <div class="emm-card-body" style="padding: 14px 16px;">
        <div class="row g-2">
            <div class="col-lg-3 col-md-6">
                <input type="text" name="q" class="form-control form-control-sm" placeholder="🔍 Search memo no. หรือ subject" value="<?= e($filter['keyword'] ?? '') ?>">
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <?php foreach (['draft','submitted','manager_approved','accounting_checked','approved','pending_payment','partially_paid','paid','closed','rejected','revision_required','cancelled'] as $s): ?>
                        <option value="<?= $s ?>" <?= ($filter['status'] ?? '') === $s ? 'selected' : '' ?>><?= ucwords(str_replace('_', ' ', $s)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <select name="company" class="form-select form-select-sm">
                    <option value="">All Company</option>
                    <?php foreach ($companies as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($filter['company_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['company_code']) ?> — <?= e($c['company_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-lg-2 col-md-3 col-6"><input type="date" name="from" class="form-control form-control-sm" value="<?= e($filter['date_from'] ?? '') ?>"></div>
            <div class="col-lg-2 col-md-3 col-6"><input type="date" name="to" class="form-control form-control-sm" value="<?= e($filter['date_to'] ?? '') ?>"></div>
            <div class="col-lg-1 col-12">
                <button class="btn btn-sm btn-outline-primary w-100"><i class="bi bi-funnel"></i> Filter</button>
            </div>
        </div>
    </div>
</form>

<?php $u = user(); $deletableStatuses = ['draft','cancelled','rejected']; ?>
<div class="emm-card">
    <div class="table-responsive">
        <table class="emm-table">
            <thead>
                <tr>
                    <th>Memo No.</th>
                    <th>Date</th>
                    <th>Company / Dept</th>
                    <th>Subject · Type</th>
                    <th>Requester</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">Net</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$memos): ?>
                    <tr><td colspan="10" class="text-center" style="padding: 40px 14px; color: var(--emm-text-soft);">
                        <i class="bi bi-inbox" style="font-size: 32px; display: block; margin-bottom: 8px; opacity: .5;"></i>
                        ไม่พบ Memo ตามเงื่อนไข
                    </td></tr>
                <?php endif; ?>
                <?php foreach ($memos as $m): ?>
                    <tr>
                        <td><a href="<?= url('/memos/' . $m['id']) ?>" style="font-weight: 600;"><?= e($m['memo_no'] ?? 'DRAFT-#'. $m['id']) ?></a></td>
                        <td><span class="text-muted"><?= format_date($m['memo_date']) ?></span></td>
                        <td>
                            <span class="role-tag" style="background: var(--emm-bg); color: var(--emm-text-muted);"><?= e($m['company_code']) ?></span>
                            <span class="text-muted small"><?= e($m['department_code']) ?></span>
                        </td>
                        <td>
                            <div><?= e($m['subject']) ?></div>
                            <small class="text-soft"><?= e(memo_type_label($m['memo_type'])) ?></small>
                        </td>
                        <td>
                            <span class="avatar-sm"><?= strtoupper(substr($m['requester_name'] ?? 'U', 0, 1)) ?></span>
                            <span class="text-muted small"><?= e($m['requester_name']) ?></span>
                        </td>
                        <td class="text-end money"><?= format_money($m['total_amount']) ?></td>
                        <td class="text-end money"><?= format_money($m['net_amount']) ?></td>
                        <td><span class="status <?= e($m['status']) ?>"><?= strtoupper(str_replace('_', ' ', $m['status'])) ?></span></td>
                        <td><span class="status <?= e($m['payment_status']) ?>"><?= strtoupper(str_replace('_', ' ', $m['payment_status'])) ?></span></td>
                        <td style="white-space: nowrap;">
                            <a href="<?= url('/memos/' . $m['id']) ?>" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
                            <?php
                                $canDelete = $u['role'] === 'admin'
                                    || ($m['requester_id'] == $u['id'] && in_array($m['status'], $deletableStatuses, true));
                            ?>
                            <?php if ($canDelete): ?>
                                <form method="post" action="<?= url('/memos/' . $m['id'] . '/delete') ?>" style="display:inline;" onsubmit="return confirm('ลบ Memo นี้? — ไม่สามารถกู้คืนได้');">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

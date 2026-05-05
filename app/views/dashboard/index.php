<div class="page-title">
    <div>
        <h3>Dashboard</h3>
        <div class="meta">ภาพรวมระบบ Memo ค่าใช้จ่าย — <?= date('d M Y') ?></div>
    </div>
    <div>
        <a href="<?= url('/memos/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Create Memo
        </a>
    </div>
</div>

<div class="kpi-grid mb-4">
    <div class="kpi">
        <div class="icon muted"><i class="bi bi-files"></i></div>
        <div class="label">All Memos</div>
        <div class="value"><?= number_format($kpi['total']) ?></div>
        <div class="delta">All-time submissions</div>
    </div>
    <div class="kpi">
        <div class="icon primary"><i class="bi bi-send"></i></div>
        <div class="label">Submitted</div>
        <div class="value"><?= number_format($kpi['submitted']) ?></div>
        <div class="delta">Awaiting approval</div>
    </div>
    <div class="kpi">
        <div class="icon warning"><i class="bi bi-clock-history"></i></div>
        <div class="label">Pending Pay</div>
        <div class="value"><?= number_format($kpi['pending_pay']) ?></div>
        <div class="delta">Approved · awaiting cash</div>
    </div>
    <div class="kpi">
        <div class="icon success"><i class="bi bi-check2-circle"></i></div>
        <div class="label">Paid</div>
        <div class="value"><?= number_format($kpi['paid']) ?></div>
        <div class="delta">Payments completed</div>
    </div>
    <div class="kpi">
        <div class="icon danger"><i class="bi bi-x-circle"></i></div>
        <div class="label">Rejected</div>
        <div class="value"><?= number_format($kpi['rejected']) ?></div>
        <div class="delta">Returned by reviewers</div>
    </div>
    <div class="kpi">
        <div class="icon info"><i class="bi bi-cash-coin"></i></div>
        <div class="label">This Month (THB)</div>
        <div class="value money"><?= format_money($kpi['this_month']) ?></div>
        <div class="delta"><?= date('M Y') ?> · net amount</div>
    </div>
</div>

<div class="emm-card">
    <div class="emm-card-header">
        <strong>Recent Memos</strong>
        <a href="<?= url('/memos') ?>" class="btn btn-sm btn-light">View all <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="table-responsive">
        <table class="emm-table">
            <thead>
                <tr>
                    <th>Memo No.</th>
                    <th>Date</th>
                    <th>Company / Dept</th>
                    <th>Subject</th>
                    <th>Requester</th>
                    <th class="text-end">Net Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$recent): ?>
                    <tr><td colspan="7" class="text-center" style="padding: 40px 14px; color: var(--emm-text-soft);">
                        <i class="bi bi-inbox" style="font-size: 32px; display: block; margin-bottom: 8px; opacity: .5;"></i>
                        ยังไม่มี Memo — สร้าง Memo แรกของคุณ
                    </td></tr>
                <?php endif; ?>
                <?php foreach ($recent as $m): ?>
                    <tr>
                        <td><a href="<?= url('/memos/' . $m['id']) ?>" style="font-weight: 600;"><?= e($m['memo_no'] ?? 'DRAFT') ?></a></td>
                        <td><span class="text-muted"><?= format_date($m['memo_date']) ?></span></td>
                        <td><span class="role-tag" style="background: var(--emm-bg); color: var(--emm-text-muted);"><?= e($m['company_code']) ?></span> <span class="text-muted small"><?= e($m['department_code']) ?></span></td>
                        <td><?= e($m['subject']) ?></td>
                        <td>
                            <span class="avatar-sm"><?= strtoupper(substr($m['requester_name'] ?? 'U', 0, 1)) ?></span>
                            <span class="text-muted small"><?= e($m['requester_name']) ?></span>
                        </td>
                        <td class="text-end money"><?= format_money($m['net_amount']) ?></td>
                        <td><span class="status <?= e($m['status']) ?>"><?= strtoupper(str_replace('_', ' ', $m['status'])) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

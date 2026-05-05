<div class="page-title">
    <div>
        <h3>Payments</h3>
        <div class="meta">บันทึกการจ่ายเงิน · ติดตามสถานะการจ่าย</div>
    </div>
</div>

<div class="emm-tabs">
    <a class="<?= $tab === 'pending' ? 'active' : '' ?>" href="?tab=pending"><i class="bi bi-clock-history"></i> Pending Payment</a>
    <a class="<?= $tab === 'partial' ? 'active' : '' ?>" href="?tab=partial"><i class="bi bi-pie-chart"></i> Partially Paid</a>
    <a class="<?= $tab === 'paid' ? 'active' : '' ?>" href="?tab=paid"><i class="bi bi-check2-circle"></i> Paid</a>
</div>

<div class="emm-card">
    <div class="table-responsive">
        <table class="emm-table">
            <thead>
                <tr>
                    <th>Memo No.</th><th>Approved</th><th>Required Pay</th>
                    <th>Company</th><th>Subject</th><th>Requester</th>
                    <th class="text-end">Net</th><th>Status</th><th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$memos): ?>
                    <tr><td colspan="9" class="text-center" style="padding: 40px 14px; color: var(--emm-text-soft);">
                        <i class="bi bi-cash-stack" style="font-size: 32px; display: block; margin-bottom: 8px; opacity: .4;"></i>
                        ไม่มี Memo ใน Tab นี้
                    </td></tr>
                <?php endif; ?>
                <?php foreach ($memos as $m): ?>
                    <?php $overdue = $m['required_payment_date'] && strtotime($m['required_payment_date']) < time(); ?>
                    <tr>
                        <td><a href="<?= url('/memos/' . $m['id']) ?>" style="font-weight: 600;"><?= e($m['memo_no']) ?></a></td>
                        <td class="text-muted small"><?= format_date($m['approved_at']) ?></td>
                        <td><span class="text-muted small <?= $overdue ? 'text-danger' : '' ?>" style="<?= $overdue ? 'color: var(--emm-danger); font-weight: 600;' : '' ?>"><?= format_date($m['required_payment_date']) ?> <?= $overdue ? '⚠️' : '' ?></span></td>
                        <td><span class="role-tag" style="background: var(--emm-bg); color: var(--emm-text-muted);"><?= e($m['company_code']) ?></span> <small class="text-muted"><?= e($m['department_code']) ?></small></td>
                        <td><?= e($m['subject']) ?></td>
                        <td>
                            <span class="avatar-sm"><?= strtoupper(substr($m['requester_name'] ?? 'U', 0, 1)) ?></span>
                            <span class="text-muted small"><?= e($m['requester_name']) ?></span>
                        </td>
                        <td class="text-end money"><?= format_money($m['net_amount']) ?></td>
                        <td><span class="status <?= e($m['payment_status']) ?>"><?= strtoupper(str_replace('_', ' ', $m['payment_status'])) ?></span></td>
                        <td>
                            <?php if ($m['payment_status'] !== 'paid'): ?>
                                <a href="<?= url('/memos/' . $m['id'] . '/payment') ?>" class="btn btn-sm btn-warning"><i class="bi bi-cash"></i> Pay</a>
                            <?php else: ?>
                                <a href="<?= url('/memos/' . $m['id']) ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

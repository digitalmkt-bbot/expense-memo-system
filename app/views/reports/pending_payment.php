<div class="page-title">
    <div>
        <h3>Pending Payment</h3>
        <div class="meta">Memo ที่รอจ่ายเงิน — ทั้งระบบ</div>
    </div>
    <a href="<?= url('/reports') ?>" class="btn btn-light"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="emm-card">
    <div class="table-responsive">
        <table class="emm-table">
            <thead><tr><th>Memo No.</th><th>Approved</th><th>Required Pay</th><th>Company</th><th>Subject</th><th>Requester</th><th class="text-end">Net</th><th>Status</th><th></th></tr></thead>
            <tbody>
                <?php if (!$rows): ?><tr><td colspan="9" class="text-center text-muted" style="padding:30px;">ไม่มี Memo รอจ่ายเงิน</td></tr><?php endif; ?>
                <?php foreach ($rows as $m): $overdue = $m['required_payment_date'] && strtotime($m['required_payment_date']) < time(); ?>
                    <tr>
                        <td><a href="<?= url('/memos/' . $m['id']) ?>" style="font-weight: 600;"><?= e($m['memo_no']) ?></a></td>
                        <td class="text-muted small"><?= format_date($m['approved_at']) ?></td>
                        <td><span class="text-muted small" style="<?= $overdue ? 'color: var(--emm-danger); font-weight: 600;' : '' ?>"><?= format_date($m['required_payment_date']) ?> <?= $overdue ? '⚠️' : '' ?></span></td>
                        <td><span class="role-tag" style="background: var(--emm-bg); color: var(--emm-text-muted);"><?= e($m['company_code']) ?></span> <small class="text-muted"><?= e($m['department_code']) ?></small></td>
                        <td><?= e($m['subject']) ?></td>
                        <td><span class="text-muted small"><?= e($m['requester_name']) ?></span></td>
                        <td class="text-end money"><?= format_money($m['net_amount']) ?></td>
                        <td><span class="status <?= e($m['payment_status']) ?>"><?= strtoupper(str_replace('_', ' ', $m['payment_status'])) ?></span></td>
                        <td><a href="<?= url('/memos/' . $m['id']) ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-cash"></i></a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

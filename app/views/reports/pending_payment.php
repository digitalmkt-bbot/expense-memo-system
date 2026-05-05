<div class="card"><div class="table-responsive">
<table class="table table-hover table-sm mb-0">
    <thead class="table-light"><tr>
        <th>Memo No.</th><th>Approved</th><th>Required Pay</th><th>Company</th><th>Subject</th>
        <th>Requester</th><th class="text-end">Net</th><th>Pay Status</th><th></th>
    </tr></thead>
    <tbody>
        <?php if (!$rows): ?><tr><td colspan="9" class="text-center text-muted py-3">ไม่มี</td></tr><?php endif; ?>
        <?php foreach ($rows as $m): ?>
            <tr>
                <td><a href="<?= url('/memos/' . $m['id']) ?>"><strong><?= e($m['memo_no']) ?></strong></a></td>
                <td><small><?= format_date($m['approved_at']) ?></small></td>
                <td><small class="<?= ($m['required_payment_date'] && strtotime($m['required_payment_date']) < time()) ? 'text-danger fw-bold' : '' ?>"><?= format_date($m['required_payment_date']) ?></small></td>
                <td><small><?= e($m['company_code']) ?>/<?= e($m['department_code']) ?></small></td>
                <td><?= e($m['subject']) ?></td>
                <td><small><?= e($m['requester_name']) ?></small></td>
                <td class="text-end"><strong><?= format_money($m['net_amount']) ?></strong></td>
                <td><?= status_badge($m['payment_status']) ?></td>
                <td><a href="<?= url('/memos/' . $m['id']) ?>" class="btn btn-sm btn-outline-warning">View</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div></div>

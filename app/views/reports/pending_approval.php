<div class="card"><div class="table-responsive">
<table class="table table-hover table-sm mb-0">
    <thead class="table-light"><tr>
        <th>Memo No.</th><th>Date</th><th>Submitted</th><th>Company</th><th>Subject</th>
        <th>Requester</th><th class="text-end">Net</th><th>Status</th><th></th>
    </tr></thead>
    <tbody>
        <?php if (!$rows): ?><tr><td colspan="9" class="text-center text-muted py-3">ไม่มี</td></tr><?php endif; ?>
        <?php foreach ($rows as $m): ?>
            <tr>
                <td><a href="<?= url('/memos/' . $m['id']) ?>"><strong><?= e($m['memo_no']) ?></strong></a></td>
                <td><?= format_date($m['memo_date']) ?></td>
                <td><small><?= format_datetime($m['submitted_at']) ?></small></td>
                <td><small><?= e($m['company_code']) ?>/<?= e($m['department_code']) ?></small></td>
                <td><?= e($m['subject']) ?></td>
                <td><small><?= e($m['requester_name']) ?></small></td>
                <td class="text-end"><strong><?= format_money($m['net_amount']) ?></strong></td>
                <td><?= status_badge($m['status']) ?></td>
                <td><a href="<?= url('/memos/' . $m['id']) ?>" class="btn btn-sm btn-outline-primary">View</a></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div></div>

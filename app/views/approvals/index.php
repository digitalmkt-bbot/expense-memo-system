<div class="alert alert-info">
    คุณคือ <strong><?= e($role) ?></strong> — แสดง Memo ที่อยู่ในขั้นรออนุมัติของคุณ
    <?php if ($role === 'manager'):    ?> (Memo ที่ submitted) <?php endif; ?>
    <?php if ($role === 'accounting'): ?> (Memo ที่ manager_approved) <?php endif; ?>
    <?php if ($role === 'director'):   ?> (Memo ที่ accounting_checked) <?php endif; ?>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th>Memo No.</th><th>Date</th><th>Company / Dept</th><th>Subject</th>
                    <th>Requester</th><th>Type</th>
                    <th class="text-end">Net</th><th>Status</th><th>Submitted</th><th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$memos): ?>
                    <tr><td colspan="10" class="text-center text-muted py-4">ไม่มี Memo ที่รอการอนุมัติของคุณ</td></tr>
                <?php endif; ?>
                <?php foreach ($memos as $m): ?>
                    <tr>
                        <td><a href="<?= url('/memos/' . $m['id']) ?>"><strong><?= e($m['memo_no']) ?></strong></a></td>
                        <td><?= format_date($m['memo_date']) ?></td>
                        <td><small><?= e($m['company_code']) ?> / <?= e($m['department_code']) ?></small></td>
                        <td><?= e($m['subject']) ?></td>
                        <td><small><?= e($m['requester_name']) ?></small></td>
                        <td><small><?= e(memo_type_label($m['memo_type'])) ?></small></td>
                        <td class="text-end"><strong><?= format_money($m['net_amount']) ?></strong></td>
                        <td><?= status_badge($m['status']) ?></td>
                        <td><small><?= format_datetime($m['submitted_at']) ?></small></td>
                        <td>
                            <a href="<?= url('/memos/' . $m['id']) ?>" class="btn btn-sm btn-primary">
                                <i class="bi bi-check2-square"></i> Review
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

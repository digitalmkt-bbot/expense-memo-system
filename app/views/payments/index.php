<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link <?= $tab === 'pending' ? 'active' : '' ?>" href="?tab=pending">Pending Payment</a></li>
    <li class="nav-item"><a class="nav-link <?= $tab === 'partial' ? 'active' : '' ?>" href="?tab=partial">Partially Paid</a></li>
    <li class="nav-item"><a class="nav-link <?= $tab === 'paid' ? 'active' : '' ?>" href="?tab=paid">Paid</a></li>
</ul>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th>Memo No.</th><th>Approved Date</th><th>Required Pay</th>
                    <th>Company / Dept</th><th>Subject</th><th>Requester</th>
                    <th class="text-end">Net</th><th>Pay Status</th><th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$memos): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">ไม่มี Memo ใน Tab นี้</td></tr>
                <?php endif; ?>
                <?php foreach ($memos as $m): ?>
                    <tr>
                        <td><a href="<?= url('/memos/' . $m['id']) ?>"><strong><?= e($m['memo_no']) ?></strong></a></td>
                        <td><small><?= format_date($m['approved_at']) ?></small></td>
                        <td><small class="<?= ($m['required_payment_date'] && strtotime($m['required_payment_date']) < time()) ? 'text-danger' : '' ?>">
                            <?= format_date($m['required_payment_date']) ?></small></td>
                        <td><small><?= e($m['company_code']) ?> / <?= e($m['department_code']) ?></small></td>
                        <td><?= e($m['subject']) ?></td>
                        <td><small><?= e($m['requester_name']) ?></small></td>
                        <td class="text-end"><strong><?= format_money($m['net_amount']) ?></strong></td>
                        <td><?= status_badge($m['payment_status']) ?></td>
                        <td>
                            <?php if ($m['payment_status'] !== 'paid'): ?>
                                <a href="<?= url('/memos/' . $m['id'] . '/payment') ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-cash"></i> Pay
                                </a>
                            <?php else: ?>
                                <a href="<?= url('/memos/' . $m['id']) ?>" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

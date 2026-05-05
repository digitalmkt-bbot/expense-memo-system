<div class="row g-3">
    <div class="col-md-2 col-6">
        <div class="card h-100"><div class="card-body">
            <small class="text-muted">All Memos</small>
            <h3 class="mb-0"><?= e($kpi['total']) ?></h3>
        </div></div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card h-100"><div class="card-body">
            <small class="text-muted">Submitted</small>
            <h3 class="mb-0 text-primary"><?= e($kpi['submitted']) ?></h3>
        </div></div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card h-100"><div class="card-body">
            <small class="text-muted">Pending Pay</small>
            <h3 class="mb-0 text-warning"><?= e($kpi['pending_pay']) ?></h3>
        </div></div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card h-100"><div class="card-body">
            <small class="text-muted">Paid</small>
            <h3 class="mb-0 text-success"><?= e($kpi['paid']) ?></h3>
        </div></div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card h-100"><div class="card-body">
            <small class="text-muted">Rejected</small>
            <h3 class="mb-0 text-danger"><?= e($kpi['rejected']) ?></h3>
        </div></div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card h-100"><div class="card-body">
            <small class="text-muted">This Month (THB)</small>
            <h4 class="mb-0"><?= format_money($kpi['this_month']) ?></h4>
        </div></div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong>Recent Memos</strong>
        <a href="<?= url('/memos') ?>" class="btn btn-sm btn-outline-primary">View all</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-sm mb-0">
            <thead class="table-light">
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
                    <tr><td colspan="7" class="text-center text-muted py-4">ยังไม่มี Memo</td></tr>
                <?php endif; ?>
                <?php foreach ($recent as $m): ?>
                    <tr>
                        <td><a href="<?= url('/memos/' . $m['id']) ?>"><?= e($m['memo_no'] ?? 'DRAFT') ?></a></td>
                        <td><?= format_date($m['memo_date']) ?></td>
                        <td><small><?= e($m['company_code']) ?> / <?= e($m['department_code']) ?></small></td>
                        <td><?= e($m['subject']) ?></td>
                        <td><small><?= e($m['requester_name']) ?></small></td>
                        <td class="text-end"><?= format_money($m['net_amount']) ?></td>
                        <td><?= status_badge($m['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

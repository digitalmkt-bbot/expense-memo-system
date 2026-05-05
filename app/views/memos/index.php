<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <a href="<?= url('/memos/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Create Memo
    </a>
</div>

<form method="get" class="card mb-3">
    <div class="card-body p-3">
        <div class="row g-2">
            <div class="col-md-3">
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Search memo no. / subject" value="<?= e($filter['keyword'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <?php foreach (['draft','submitted','manager_approved','accounting_checked','approved','pending_payment','partially_paid','paid','closed','rejected','revision_required','cancelled'] as $s): ?>
                        <option value="<?= $s ?>" <?= ($filter['status'] ?? '') === $s ? 'selected' : '' ?>><?= strtoupper(str_replace('_', ' ', $s)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="company" class="form-select form-select-sm">
                    <option value="">All Company</option>
                    <?php foreach ($companies as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($filter['company_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['company_code']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2"><input type="date" name="from" class="form-control form-control-sm" value="<?= e($filter['date_from'] ?? '') ?>"></div>
            <div class="col-md-2"><input type="date" name="to"   class="form-control form-control-sm" value="<?= e($filter['date_to'] ?? '') ?>"></div>
            <div class="col-md-1"><button class="btn btn-sm btn-outline-primary w-100">Filter</button></div>
        </div>
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th>Memo No.</th>
                    <th>Date</th>
                    <th>Company / Dept</th>
                    <th>Subject</th>
                    <th>Type</th>
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
                    <tr><td colspan="11" class="text-center text-muted py-4">ไม่พบ Memo</td></tr>
                <?php endif; ?>
                <?php foreach ($memos as $m): ?>
                    <tr>
                        <td><a href="<?= url('/memos/' . $m['id']) ?>"><strong><?= e($m['memo_no'] ?? 'DRAFT-#'. $m['id']) ?></strong></a></td>
                        <td><?= format_date($m['memo_date']) ?></td>
                        <td><small><?= e($m['company_code']) ?> / <?= e($m['department_code']) ?></small></td>
                        <td><?= e($m['subject']) ?></td>
                        <td><small class="text-muted"><?= e(memo_type_label($m['memo_type'])) ?></small></td>
                        <td><small><?= e($m['requester_name']) ?></small></td>
                        <td class="text-end"><?= format_money($m['total_amount']) ?></td>
                        <td class="text-end"><strong><?= format_money($m['net_amount']) ?></strong></td>
                        <td><?= status_badge($m['status']) ?></td>
                        <td><small><?= e($m['payment_status']) ?></small></td>
                        <td>
                            <a href="<?= url('/memos/' . $m['id']) ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

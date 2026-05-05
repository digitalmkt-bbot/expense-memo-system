<?php $u = user(); $canEdit = $memo['requester_id'] == $u['id'] && in_array($memo['status'], ['draft','revision_required']); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0"><?= e($memo['memo_no'] ?? 'DRAFT') ?> <?= status_badge($memo['status']) ?></h4>
        <small class="text-muted"><?= e(memo_type_label($memo['memo_type'])) ?></small>
    </div>
    <div>
        <a href="<?= url('/memos/' . $memo['id'] . '/pdf') ?>" target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-printer"></i> Export / Print PDF
        </a>
        <?php if ($canEdit): ?>
            <a href="<?= url('/memos/' . $memo['id'] . '/edit') ?>" class="btn btn-warning"><i class="bi bi-pencil"></i> Edit</a>
        <?php endif; ?>
        <a href="<?= url('/memos') ?>" class="btn btn-light"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3"><small class="text-muted">Company</small><br><strong><?= e($memo['company_code']) ?></strong> <?= e($memo['company_name']) ?></div>
                    <div class="col-md-3"><small class="text-muted">Department</small><br><strong><?= e($memo['department_code']) ?></strong></div>
                    <div class="col-md-3"><small class="text-muted">Memo Date</small><br><?= format_date($memo['memo_date']) ?></div>
                    <div class="col-md-3"><small class="text-muted">Required Pay</small><br><?= format_date($memo['required_payment_date']) ?></div>
                    <div class="col-md-6"><small class="text-muted">Project</small><br><?= e($memo['project_code'] ?? '-') ?> <?= e($memo['project_name'] ?? '') ?></div>
                    <div class="col-md-6"><small class="text-muted">Requester</small><br><?= e($memo['requester_name']) ?></div>
                    <div class="col-12"><small class="text-muted">Subject</small><br><h5 class="mb-0"><?= e($memo['subject']) ?></h5></div>
                    <?php if ($memo['objective']): ?><div class="col-md-6"><small class="text-muted">Objective</small><br><?= nl2br(e($memo['objective'])) ?></div><?php endif; ?>
                    <?php if ($memo['description']): ?><div class="col-md-6"><small class="text-muted">Description</small><br><?= nl2br(e($memo['description'])) ?></div><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-white"><strong>Expense Items</strong></div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr><th>#</th><th>Date</th><th>Category</th><th>Item</th><th>Supplier</th>
                        <th class="text-end">Qty</th><th class="text-end">Unit</th><th class="text-end">Amount</th>
                        <th class="text-end">VAT</th><th class="text-end">WHT</th><th class="text-end">Net</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $idx => $i): ?>
                            <tr>
                                <td><?= $idx+1 ?></td>
                                <td><small><?= format_date($i['expense_date']) ?></small></td>
                                <td><small><?= e($i['category_name'] ?? '-') ?></small></td>
                                <td><?= e($i['item_name']) ?></td>
                                <td><small><?= e($i['supplier_name'] ?? '-') ?></small></td>
                                <td class="text-end"><?= format_money($i['quantity']) ?></td>
                                <td class="text-end"><?= format_money($i['unit_price']) ?></td>
                                <td class="text-end"><?= format_money($i['amount']) ?></td>
                                <td class="text-end text-success"><?= format_money($i['vat_amount']) ?></td>
                                <td class="text-end text-danger"><?= format_money($i['wht_amount']) ?></td>
                                <td class="text-end"><strong><?= format_money($i['net_amount']) ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="7" class="text-end">Total:</th>
                            <th class="text-end"><?= format_money($memo['total_amount']) ?></th>
                            <th class="text-end text-success"><?= format_money($memo['vat_amount']) ?></th>
                            <th class="text-end text-danger"><?= format_money($memo['wht_amount']) ?></th>
                            <th class="text-end"><strong><?= format_money($memo['net_amount']) ?></strong></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-white"><strong>Attachments</strong></div>
            <div class="card-body">
                <?php if (!$attachments): ?>
                    <p class="text-muted text-center mb-0">ไม่มีไฟล์แนบ</p>
                <?php else: ?>
                    <ul class="list-group">
                    <?php foreach ($attachments as $a): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>
                                <span class="badge bg-light text-dark me-1"><?= e($a['file_type']) ?></span>
                                <a href="<?= e($a['file_url']) ?>" target="_blank"><?= e($a['file_name']) ?></a>
                            </span>
                            <small class="text-muted"><?= format_datetime($a['uploaded_at']) ?></small>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Approval Actions for Approver -->
        <?php if (in_array($memo['status'], ['submitted','manager_approved','accounting_checked'])
                  && in_array($u['role'], ['manager','accounting','director','admin'])): ?>
        <div class="card border-primary mb-3">
            <div class="card-header bg-primary text-white"><strong><i class="bi bi-check2-square"></i> Approval Action</strong></div>
            <div class="card-body">
                <form method="post" action="<?= url('/memos/' . $memo['id'] . '/approve') ?>" class="mb-2">
                    <?= csrf_field() ?>
                    <textarea name="comment" class="form-control form-control-sm mb-2" rows="2" placeholder="Comment (optional)"></textarea>
                    <button class="btn btn-success w-100" onclick="return confirm('Approve memo นี้?')"><i class="bi bi-check-lg"></i> Approve</button>
                </form>
                <form method="post" action="<?= url('/memos/' . $memo['id'] . '/revision') ?>" class="mb-2">
                    <?= csrf_field() ?>
                    <button class="btn btn-warning w-100" onclick="return confirm('ส่งกลับให้แก้ไข?')"><i class="bi bi-arrow-counterclockwise"></i> Request Revision</button>
                </form>
                <form method="post" action="<?= url('/memos/' . $memo['id'] . '/reject') ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-danger w-100" onclick="return confirm('Reject memo นี้?')"><i class="bi bi-x-circle"></i> Reject</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Payment Action for Accounting -->
        <?php if ($memo['status'] === 'approved' && $memo['payment_status'] !== 'paid'
                  && in_array($u['role'], ['accounting','admin'])): ?>
        <div class="card border-warning mb-3">
            <div class="card-header bg-warning"><strong><i class="bi bi-cash"></i> Record Payment</strong></div>
            <div class="card-body">
                <a href="<?= url('/memos/' . $memo['id'] . '/payment') ?>" class="btn btn-warning w-100">บันทึกการจ่ายเงิน</a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Close Memo -->
        <?php if ($memo['payment_status'] === 'paid' && $memo['status'] !== 'closed'
                  && in_array($u['role'], ['accounting','admin'])): ?>
        <div class="card border-dark mb-3">
            <div class="card-body">
                <form method="post" action="<?= url('/memos/' . $memo['id'] . '/close') ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-dark w-100" onclick="return confirm('ปิด Memo นี้?')"><i class="bi bi-archive"></i> Close Memo</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Summary -->
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Summary</h6>
                <div class="d-flex justify-content-between"><span>Total Amount:</span><strong><?= format_money($memo['total_amount']) ?></strong></div>
                <div class="d-flex justify-content-between"><span>VAT:</span><span class="text-success">+<?= format_money($memo['vat_amount']) ?></span></div>
                <div class="d-flex justify-content-between"><span>WHT:</span><span class="text-danger">-<?= format_money($memo['wht_amount']) ?></span></div>
                <hr>
                <div class="d-flex justify-content-between"><span>Net Amount:</span><h5 class="mb-0"><?= format_money($memo['net_amount']) ?></h5></div>
                <hr>
                <div class="d-flex justify-content-between"><span>Total Paid:</span><strong><?= format_money($totalPaid) ?></strong></div>
                <div class="d-flex justify-content-between"><span>Balance:</span><strong class="<?= $totalPaid >= $memo['net_amount'] ? 'text-success' : 'text-danger' ?>"><?= format_money($memo['net_amount'] - $totalPaid) ?></strong></div>
            </div>
        </div>

        <!-- Approval Log -->
        <div class="card mt-3">
            <div class="card-header bg-white"><strong>Approval History</strong></div>
            <ul class="list-group list-group-flush">
                <?php if (!$logs): ?>
                    <li class="list-group-item text-muted">No history yet</li>
                <?php endif; ?>
                <?php foreach ($logs as $l): ?>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <strong class="text-<?= match($l['action']) {'approved'=>'success','rejected'=>'danger','revision_required'=>'warning',default=>'primary'} ?>">
                                <?= strtoupper(str_replace('_',' ',$l['action'])) ?>
                            </strong>
                            <small class="text-muted"><?= format_datetime($l['action_at']) ?></small>
                        </div>
                        <small><?= e($l['approver_name']) ?> (<?= e($l['approver_role']) ?>)</small>
                        <?php if ($l['comment']): ?><div class="small text-muted mt-1"><?= e($l['comment']) ?></div><?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Payments History -->
        <?php if ($payments): ?>
        <div class="card mt-3">
            <div class="card-header bg-white"><strong>Payment History</strong></div>
            <ul class="list-group list-group-flush">
                <?php foreach ($payments as $p): ?>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <span><strong><?= format_money($p['paid_amount']) ?></strong> via <?= e($p['payment_method']) ?></span>
                            <small><?= format_date($p['payment_date']) ?></small>
                        </div>
                        <small class="text-muted">By <?= e($p['paid_by_name']) ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</div>

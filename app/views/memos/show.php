<?php $u = user(); $canEdit = $memo['requester_id'] == $u['id'] && in_array($memo['status'], ['draft','revision_required']); ?>

<div class="page-title">
    <div>
        <h3><?= e($memo['memo_no'] ?? 'DRAFT') ?> <span class="status <?= e($memo['status']) ?>" style="margin-left: 10px;"><?= strtoupper(str_replace('_', ' ', $memo['status'])) ?></span></h3>
        <div class="meta"><?= e(memo_type_label($memo['memo_type'])) ?> · <?= format_date($memo['memo_date']) ?></div>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('/memos/' . $memo['id'] . '/pdf') ?>" target="_blank" class="btn btn-light">
            <i class="bi bi-printer"></i> Print / PDF
        </a>
        <?php if ($canEdit): ?>
            <a href="<?= url('/memos/' . $memo['id'] . '/edit') ?>" class="btn btn-warning"><i class="bi bi-pencil"></i> Edit</a>
        <?php endif; ?>
        <a href="<?= url('/memos') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="emm-card">
            <div class="emm-card-body">
                <div class="row g-3">
                    <div class="col-md-3"><div class="text-soft small mb-1">Company</div><strong><?= e($memo['company_code']) ?></strong> <span class="text-muted small"><?= e($memo['company_name']) ?></span></div>
                    <div class="col-md-3"><div class="text-soft small mb-1">Department</div><strong><?= e($memo['department_code']) ?></strong></div>
                    <div class="col-md-3"><div class="text-soft small mb-1">Memo Date</div><?= format_date($memo['memo_date']) ?></div>
                    <div class="col-md-3"><div class="text-soft small mb-1">Required Pay</div><?= format_date($memo['required_payment_date']) ?></div>
                    <div class="col-md-6"><div class="text-soft small mb-1">Project</div>
                        <?php $projDisplay = $memo['project_name_text'] ?? null; ?>
                        <?php if ($projDisplay): ?>
                            <?= e($projDisplay) ?>
                        <?php elseif (!empty($memo['project_code'])): ?>
                            <?= e($memo['project_code']) ?> <span class="text-muted"><?= e($memo['project_name']) ?></span>
                        <?php else: ?>
                            <span class="text-soft">—</span>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6"><div class="text-soft small mb-1">Requester</div>
                        <span class="avatar-sm"><?= strtoupper(substr($memo['requester_name'] ?? 'U', 0, 1)) ?></span>
                        <?= e($memo['requester_name']) ?> <span class="text-muted small">(<?= e($memo['requester_email']) ?>)</span>
                    </div>
                    <div class="col-12"><div class="text-soft small mb-1">Subject</div><h5 class="mb-0"><?= e($memo['subject']) ?></h5></div>
                    <?php if ($memo['objective']): ?><div class="col-md-6"><div class="text-soft small mb-1">Objective</div><?= nl2br(e($memo['objective'])) ?></div><?php endif; ?>
                    <?php if ($memo['description']): ?><div class="col-md-6"><div class="text-soft small mb-1">Description</div><?= nl2br(e($memo['description'])) ?></div><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="emm-card mt-3">
            <div class="emm-card-header"><strong>Expense Items</strong> <span class="text-soft small"><?= count($items) ?> รายการ</span></div>
            <div class="table-responsive">
                <table class="emm-table">
                    <thead>
                        <tr><th>#</th><th>Date</th><th>Category</th><th>Item</th><th>Supplier</th>
                        <th class="text-end">Qty</th><th class="text-end">Unit</th><th class="text-end">Amount</th>
                        <th class="text-end">VAT</th><th class="text-end">WHT</th><th class="text-end">Net</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $idx => $i): ?>
                            <tr>
                                <td class="text-muted small"><?= $idx+1 ?></td>
                                <td class="text-muted small"><?= format_date($i['expense_date']) ?></td>
                                <td class="text-muted small"><?= e($i['category_name'] ?? '-') ?></td>
                                <td><?= e($i['item_name']) ?></td>
                                <td class="text-muted small"><?= e($i['supplier_name_text'] ?? $i['supplier_name'] ?? '-') ?></td>
                                <td class="text-end money"><?= format_money($i['quantity']) ?></td>
                                <td class="text-end money"><?= format_money($i['unit_price']) ?></td>
                                <td class="text-end money"><?= format_money($i['amount']) ?></td>
                                <td class="text-end money" style="color: var(--emm-success);"><?= format_money($i['vat_amount']) ?></td>
                                <td class="text-end money" style="color: var(--emm-danger);"><?= format_money($i['wht_amount']) ?></td>
                                <td class="text-end money"><?= format_money($i['net_amount']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" class="text-end">Total:</td>
                            <td class="text-end money"><?= format_money($memo['total_amount']) ?></td>
                            <td class="text-end money" style="color: var(--emm-success);"><?= format_money($memo['vat_amount']) ?></td>
                            <td class="text-end money" style="color: var(--emm-danger);"><?= format_money($memo['wht_amount']) ?></td>
                            <td class="text-end money" style="font-size: 14px;"><?= format_money($memo['net_amount']) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <?php if ($attachments): ?>
        <div class="emm-card mt-3">
            <div class="emm-card-header"><strong>Attachments</strong> <span class="text-soft small"><?= count($attachments) ?> ไฟล์</span></div>
            <div class="emm-card-body">
                <?php foreach ($attachments as $a): ?>
                    <div class="d-flex align-items-center justify-content-between" style="padding: 10px 0; border-bottom: 1px solid var(--emm-border-soft);">
                        <div>
                            <span class="role-tag" style="background: var(--emm-bg); color: var(--emm-text-muted);"><?= e($a['file_type']) ?></span>
                            <a href="<?= e($a['file_url']) ?>" target="_blank" style="margin-left: 8px;"><?= e($a['file_name']) ?></a>
                        </div>
                        <span class="text-soft small"><?= format_datetime($a['uploaded_at']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <?php if (in_array($memo['status'], ['submitted','manager_approved','accounting_checked']) && in_array($u['role'], ['manager','accounting','director','admin'])): ?>
        <div class="emm-card mb-3" style="border: 1px solid var(--emm-primary); border-top-width: 3px;">
            <div class="emm-card-header"><strong style="color: var(--emm-primary);"><i class="bi bi-check2-square"></i> Approval Action</strong></div>
            <div class="emm-card-body">
                <form method="post" action="<?= url('/memos/' . $memo['id'] . '/approve') ?>" class="mb-2">
                    <?= csrf_field() ?>
                    <textarea name="comment" class="form-control form-control-sm mb-2" rows="2" placeholder="Comment (optional)"></textarea>
                    <button class="btn btn-success w-100" onclick="return confirm('Approve?')"><i class="bi bi-check-lg"></i> Approve</button>
                </form>
                <form method="post" action="<?= url('/memos/' . $memo['id'] . '/revision') ?>" class="mb-2">
                    <?= csrf_field() ?>
                    <button class="btn btn-outline-warning w-100" onclick="return confirm('ส่งกลับให้แก้ไข?')"><i class="bi bi-arrow-counterclockwise"></i> Request Revision</button>
                </form>
                <form method="post" action="<?= url('/memos/' . $memo['id'] . '/reject') ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-outline-danger w-100" onclick="return confirm('Reject?')"><i class="bi bi-x-circle"></i> Reject</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($memo['status'] === 'approved' && $memo['payment_status'] !== 'paid' && in_array($u['role'], ['accounting','admin'])): ?>
        <div class="emm-card mb-3" style="border: 1px solid var(--emm-warning); border-top-width: 3px;">
            <div class="emm-card-header"><strong style="color: #b45309;"><i class="bi bi-cash"></i> Record Payment</strong></div>
            <div class="emm-card-body">
                <a href="<?= url('/memos/' . $memo['id'] . '/payment') ?>" class="btn btn-warning w-100">บันทึกการจ่ายเงิน</a>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($memo['payment_status'] === 'paid' && $memo['status'] !== 'closed' && in_array($u['role'], ['accounting','admin'])): ?>
        <div class="emm-card mb-3">
            <div class="emm-card-body">
                <form method="post" action="<?= url('/memos/' . $memo['id'] . '/close') ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-dark w-100" onclick="return confirm('ปิด Memo นี้?')"><i class="bi bi-archive"></i> Close Memo</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <div class="emm-card">
            <div class="emm-card-header"><strong>Summary</strong></div>
            <div class="emm-card-body">
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Total Amount</span><span class="money"><?= format_money($memo['total_amount']) ?></span></div>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">VAT</span><span class="money" style="color: var(--emm-success);">+<?= format_money($memo['vat_amount']) ?></span></div>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">WHT</span><span class="money" style="color: var(--emm-danger);">−<?= format_money($memo['wht_amount']) ?></span></div>
                <div class="divider"></div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted">Net Amount</span>
                    <strong class="money" style="font-size: 18px;"><?= format_money($memo['net_amount']) ?></strong>
                </div>
                <div class="divider"></div>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Total Paid</span><span class="money"><?= format_money($totalPaid) ?></span></div>
                <div class="d-flex justify-content-between"><span class="text-muted">Balance</span>
                    <strong class="money" style="color: <?= $totalPaid >= $memo['net_amount'] ? 'var(--emm-success)' : 'var(--emm-danger)' ?>;"><?= format_money($memo['net_amount'] - $totalPaid) ?></strong>
                </div>
            </div>
        </div>

        <div class="emm-card mt-3">
            <div class="emm-card-header"><strong>Approval History</strong></div>
            <div>
                <?php if (!$logs): ?>
                    <div class="emm-card-body text-muted small text-center">No history yet</div>
                <?php endif; ?>
                <?php foreach ($logs as $l): ?>
                    <div style="padding: 12px 16px; border-bottom: 1px solid var(--emm-border-soft);">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="status <?= match($l['action']) {'approved'=>'approved','rejected'=>'rejected','revision_required'=>'pending_payment',default=>'submitted'} ?>"><?= strtoupper(str_replace('_',' ',$l['action'])) ?></span>
                            <small class="text-soft"><?= format_datetime($l['action_at']) ?></small>
                        </div>
                        <div class="small"><?= e($l['approver_name']) ?> <span class="text-muted">(<?= e($l['approver_role']) ?>)</span></div>
                        <?php if ($l['comment']): ?><div class="small text-muted mt-1" style="font-style: italic;">"<?= e($l['comment']) ?>"</div><?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ($payments): ?>
        <div class="emm-card mt-3">
            <div class="emm-card-header"><strong>Payment History</strong></div>
            <div>
                <?php foreach ($payments as $p): ?>
                    <div style="padding: 12px 16px; border-bottom: 1px solid var(--emm-border-soft);">
                        <div class="d-flex justify-content-between mb-1">
                            <strong class="money"><?= format_money($p['paid_amount']) ?></strong>
                            <small class="text-soft"><?= format_date($p['payment_date']) ?></small>
                        </div>
                        <small class="text-muted">via <?= e($p['payment_method']) ?> · By <?= e($p['paid_by_name']) ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

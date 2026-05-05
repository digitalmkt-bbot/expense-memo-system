<div class="page-title">
    <div>
        <h3>Edit Memo <span class="status <?= e($memo['status']) ?>" style="margin-left: 10px;"><?= strtoupper(str_replace('_', ' ', $memo['status'])) ?></span></h3>
        <div class="meta"><?= e($memo['memo_no'] ?? 'DRAFT') ?> · กรอกข้อมูลให้ครบก่อน Submit</div>
    </div>
    <a href="<?= url('/memos/' . $memo['id']) ?>" class="btn btn-light"><i class="bi bi-eye"></i> View</a>
</div>

<div class="row g-3">
    <div class="col-12">
        <form method="post" action="<?= url('/memos/' . $memo['id']) ?>">
            <?= csrf_field() ?>
            <div class="emm-card">
                <div class="emm-card-header"><strong>Memo Header</strong></div>
                <div class="emm-card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Company</label>
                            <select name="company_id" class="form-select">
                                <?php foreach ($companies as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= $memo['company_id'] == $c['id'] ? 'selected' : '' ?>><?= e($c['company_code']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Department</label>
                            <select name="department_id" class="form-select">
                                <?php foreach ($depts as $d): ?>
                                    <option value="<?= $d['id'] ?>" <?= $memo['department_id'] == $d['id'] ? 'selected' : '' ?>><?= e($d['department_code']) ?> — <?= e($d['department_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Memo Date</label>
                            <input type="date" name="memo_date" class="form-control" value="<?= e($memo['memo_date']) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Required Pay Date</label>
                            <input type="date" name="required_payment_date" class="form-control" value="<?= e($memo['required_payment_date']) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Memo Type</label>
                            <select name="memo_type" class="form-select">
                                <?php foreach (['general_expense','advance_payment','reimbursement','supplier_payment','petty_cash','other'] as $t): ?>
                                    <option value="<?= $t ?>" <?= $memo['memo_type'] === $t ? 'selected' : '' ?>><?= e(memo_type_label($t)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label">Project</label>
                            <select name="project_id" class="form-select">
                                <option value="">— ไม่ระบุ —</option>
                                <?php foreach ($projects as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= $memo['project_id'] == $p['id'] ? 'selected' : '' ?>><?= e($p['project_code']) ?> — <?= e($p['project_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" value="<?= e($memo['subject']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Objective</label>
                            <textarea name="objective" class="form-control" rows="3"><?= e($memo['objective']) ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?= e($memo['description']) ?></textarea>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button class="btn btn-light"><i class="bi bi-save"></i> Save Header</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="col-lg-8">
        <div class="emm-card">
            <div class="emm-card-header"><strong>Expense Items</strong> <span class="text-soft small"><?= count($items) ?> รายการ</span></div>
            <div class="table-responsive">
                <table class="emm-table">
                    <thead>
                        <tr>
                            <th>Date</th><th>Category</th><th>Item</th>
                            <th class="text-end">Qty</th><th class="text-end">Unit</th>
                            <th class="text-end">Amount</th><th class="text-end">VAT</th>
                            <th class="text-end">WHT</th><th class="text-end">Net</th><th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$items): ?>
                            <tr><td colspan="10" class="text-center" style="padding: 30px; color: var(--emm-text-soft);">ยังไม่มีรายการ — เพิ่มทางขวา</td></tr>
                        <?php endif; ?>
                        <?php foreach ($items as $i): ?>
                            <tr>
                                <td><span class="text-muted small"><?= format_date($i['expense_date']) ?></span></td>
                                <td><span class="text-muted small"><?= e($i['category_name'] ?? '-') ?></span></td>
                                <td>
                                    <?= e($i['item_name']) ?>
                                    <?php if ($i['supplier_name']): ?><br><small class="text-soft">@ <?= e($i['supplier_name']) ?></small><?php endif; ?>
                                </td>
                                <td class="text-end money"><?= format_money($i['quantity']) ?></td>
                                <td class="text-end money"><?= format_money($i['unit_price']) ?></td>
                                <td class="text-end money"><?= format_money($i['amount']) ?></td>
                                <td class="text-end money" style="color: var(--emm-success);"><?= format_money($i['vat_amount']) ?></td>
                                <td class="text-end money" style="color: var(--emm-danger);"><?= format_money($i['wht_amount']) ?></td>
                                <td class="text-end money"><?= format_money($i['net_amount']) ?></td>
                                <td>
                                    <form method="post" action="<?= url('/memos/' . $memo['id'] . '/items/' . $i['id'] . '/delete') ?>" onsubmit="return confirm('ลบรายการนี้?')">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-end">Total:</td>
                            <td class="text-end money"><?= format_money($memo['total_amount']) ?></td>
                            <td class="text-end money" style="color: var(--emm-success);"><?= format_money($memo['vat_amount']) ?></td>
                            <td class="text-end money" style="color: var(--emm-danger);"><?= format_money($memo['wht_amount']) ?></td>
                            <td class="text-end money" style="font-size: 14px; color: var(--emm-text);"><?= format_money($memo['net_amount']) ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="emm-card mt-3">
            <div class="emm-card-header"><strong>Attachments</strong> <span class="text-soft small"><?= count($attachments) ?> ไฟล์</span></div>
            <div class="emm-card-body">
                <form method="post" action="<?= url('/memos/' . $memo['id'] . '/attachments') ?>" enctype="multipart/form-data" class="row g-2 align-items-end mb-3">
                    <?= csrf_field() ?>
                    <div class="col-md-4">
                        <label class="form-label">File Type</label>
                        <select name="file_type" class="form-select form-select-sm">
                            <?php foreach (['quotation','invoice','receipt','tax_invoice','payment_slip','booking_confirmation','photo','other'] as $t): ?>
                                <option value="<?= $t ?>"><?= ucwords(str_replace('_',' ',$t)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="file" name="file" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-sm btn-primary w-100"><i class="bi bi-upload"></i> Upload</button>
                    </div>
                </form>

                <?php if (!$attachments): ?>
                    <p class="text-muted text-center my-3 mb-0" style="font-size: 13px;">ยังไม่มีไฟล์แนบ</p>
                <?php else: ?>
                    <?php foreach ($attachments as $a): ?>
                        <div class="d-flex align-items-center justify-content-between" style="padding: 10px 0; border-bottom: 1px solid var(--emm-border-soft);">
                            <div>
                                <span class="role-tag" style="background: var(--emm-bg); color: var(--emm-text-muted);"><?= e($a['file_type']) ?></span>
                                <a href="<?= e($a['file_url']) ?>" target="_blank" style="margin-left: 8px;"><?= e($a['file_name']) ?></a>
                                <span class="text-soft small">(<?= round($a['file_size']/1024) ?> KB)</span>
                            </div>
                            <form method="post" action="<?= url('/attachments/' . $a['id'] . '/delete') ?>">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x"></i></button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="emm-card" style="position: sticky; top: 80px;">
            <div class="emm-card-header"><strong>Add Expense Item</strong></div>
            <form method="post" action="<?= url('/memos/' . $memo['id'] . '/items') ?>" class="emm-card-body">
                <?= csrf_field() ?>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label">Date</label>
                        <input type="date" name="expense_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select form-select-sm">
                            <option value="">—</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= e($c['category_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Item Name *</label>
                        <input type="text" name="item_name" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Supplier</label>
                        <select name="supplier_id" class="form-select form-select-sm">
                            <option value="">—</option>
                            <?php foreach ($suppliers as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= e($s['supplier_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6"><label class="form-label">Qty</label><input type="number" step="0.01" name="quantity" class="form-control form-control-sm" value="1"></div>
                    <div class="col-6"><label class="form-label">Unit</label><input type="text" name="unit" class="form-control form-control-sm"></div>
                    <div class="col-12"><label class="form-label">Unit Price</label><input type="number" step="0.01" name="unit_price" class="form-control form-control-sm" value="0"></div>
                    <div class="col-6">
                        <label class="form-label">VAT</label>
                        <select name="vat_type" class="form-select form-select-sm">
                            <option value="none">None</option>
                            <option value="exclude_vat">Exclude</option>
                            <option value="include_vat">Include</option>
                        </select>
                    </div>
                    <div class="col-6"><label class="form-label">VAT %</label><input type="number" step="0.01" name="vat_rate" class="form-control form-control-sm" value="7"></div>
                    <div class="col-6">
                        <label class="form-label">WHT</label>
                        <select name="wht_type" class="form-select form-select-sm">
                            <option value="none">None</option>
                            <option value="wht_1">1%</option>
                            <option value="wht_3">3%</option>
                            <option value="wht_5">5%</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <div class="col-6"><label class="form-label">WHT %</label><input type="number" step="0.01" name="wht_rate" class="form-control form-control-sm" value="0"></div>
                    <div class="col-12">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select form-select-sm">
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">Cash</option>
                            <option value="company_paid">Company Paid</option>
                            <option value="reimbursement">Reimbursement</option>
                            <option value="payroll">Payroll</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-12"><label class="form-label">Note</label><textarea name="note" class="form-control form-control-sm" rows="2"></textarea></div>
                </div>
                <button class="btn btn-success w-100 mt-3"><i class="bi bi-plus-circle"></i> Add Item</button>
            </form>
        </div>

        <div class="emm-card mt-3">
            <div class="emm-card-body" style="text-align: center;">
                <form method="post" action="<?= url('/memos/' . $memo['id'] . '/submit') ?>" onsubmit="return confirm('Submit Memo เพื่อขออนุมัติ?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-primary w-100 mb-2"><i class="bi bi-send"></i> Submit for Approval</button>
                </form>
                <form method="post" action="<?= url('/memos/' . $memo['id'] . '/cancel') ?>" onsubmit="return confirm('ยกเลิก Memo?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-outline-danger w-100"><i class="bi bi-x-circle"></i> Cancel Memo</button>
                </form>
            </div>
        </div>
    </div>
</div>

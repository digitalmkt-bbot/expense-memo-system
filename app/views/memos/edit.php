<div class="row g-4">
    <!-- Header form -->
    <div class="col-12">
        <form method="post" action="<?= url('/memos/' . $memo['id']) ?>">
            <?= csrf_field() ?>
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong>Memo Header — <?= e($memo['memo_no'] ?? 'DRAFT') ?></strong>
                    <span><?= status_badge($memo['status']) ?></span>
                </div>
                <div class="card-body row g-3">
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
                        <label class="form-label">Required Payment Date</label>
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
                <div class="card-footer bg-white text-end">
                    <a href="<?= url('/memos/' . $memo['id']) ?>" class="btn btn-light">Back</a>
                    <button class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Items -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white"><strong>Expense Items</strong></div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th><th>Category</th><th>Item</th>
                            <th class="text-end">Qty</th><th class="text-end">Unit Price</th>
                            <th class="text-end">Amount</th><th class="text-end">VAT</th>
                            <th class="text-end">WHT</th><th class="text-end">Net</th><th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$items): ?>
                            <tr><td colspan="10" class="text-center text-muted py-3">ยังไม่มีรายการ — เพิ่มรายการด้านขวา</td></tr>
                        <?php endif; ?>
                        <?php foreach ($items as $i): ?>
                            <tr>
                                <td><small><?= format_date($i['expense_date']) ?></small></td>
                                <td><small><?= e($i['category_name'] ?? '-') ?></small></td>
                                <td><?= e($i['item_name']) ?>
                                    <?php if ($i['supplier_name']): ?><br><small class="text-muted">@ <?= e($i['supplier_name']) ?></small><?php endif; ?>
                                </td>
                                <td class="text-end"><?= format_money($i['quantity']) ?></td>
                                <td class="text-end"><?= format_money($i['unit_price']) ?></td>
                                <td class="text-end"><?= format_money($i['amount']) ?></td>
                                <td class="text-end text-success"><?= format_money($i['vat_amount']) ?></td>
                                <td class="text-end text-danger"><?= format_money($i['wht_amount']) ?></td>
                                <td class="text-end"><strong><?= format_money($i['net_amount']) ?></strong></td>
                                <td>
                                    <form method="post" action="<?= url('/memos/' . $memo['id'] . '/items/' . $i['id'] . '/delete') ?>" onsubmit="return confirm('ลบรายการนี้?')">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="5" class="text-end">Total:</th>
                            <th class="text-end"><?= format_money($memo['total_amount']) ?></th>
                            <th class="text-end text-success"><?= format_money($memo['vat_amount']) ?></th>
                            <th class="text-end text-danger"><?= format_money($memo['wht_amount']) ?></th>
                            <th class="text-end"><strong><?= format_money($memo['net_amount']) ?></strong></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Attachments -->
        <div class="card mt-3">
            <div class="card-header bg-white"><strong>Attachments</strong></div>
            <div class="card-body">
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
                    <p class="text-muted text-center my-2">ยังไม่มีไฟล์แนบ</p>
                <?php else: ?>
                    <ul class="list-group">
                    <?php foreach ($attachments as $a): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <span class="badge bg-light text-dark"><?= e($a['file_type']) ?></span>
                                <a href="<?= e($a['file_url']) ?>" target="_blank"><?= e($a['file_name']) ?></a>
                                <small class="text-muted">(<?= round($a['file_size']/1024) ?> KB)</small>
                            </span>
                            <form method="post" action="<?= url('/attachments/' . $a['id'] . '/delete') ?>">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x"></i></button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add Item form + actions -->
    <div class="col-lg-4">
        <div class="card sticky-top" style="top:18px;">
            <div class="card-header bg-white"><strong>Add Expense Item</strong></div>
            <form method="post" action="<?= url('/memos/' . $memo['id'] . '/items') ?>" class="card-body">
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
                    <div class="col-6">
                        <label class="form-label">Quantity</label>
                        <input type="number" step="0.01" name="quantity" class="form-control form-control-sm" value="1">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Unit</label>
                        <input type="text" name="unit" class="form-control form-control-sm">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Unit Price</label>
                        <input type="number" step="0.01" name="unit_price" class="form-control form-control-sm" value="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label">VAT Type</label>
                        <select name="vat_type" class="form-select form-select-sm">
                            <option value="none">None</option>
                            <option value="exclude_vat">Exclude VAT</option>
                            <option value="include_vat">Include VAT</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">VAT Rate %</label>
                        <input type="number" step="0.01" name="vat_rate" class="form-control form-control-sm" value="7">
                    </div>
                    <div class="col-6">
                        <label class="form-label">WHT Type</label>
                        <select name="wht_type" class="form-select form-select-sm">
                            <option value="none">None</option>
                            <option value="wht_1">WHT 1%</option>
                            <option value="wht_3">WHT 3%</option>
                            <option value="wht_5">WHT 5%</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">WHT Rate %</label>
                        <input type="number" step="0.01" name="wht_rate" class="form-control form-control-sm" value="0">
                    </div>
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
                    <div class="col-12">
                        <label class="form-label">Note</label>
                        <textarea name="note" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                </div>
                <button class="btn btn-success w-100 mt-3"><i class="bi bi-plus-circle"></i> Add Item</button>
            </form>
        </div>

        <!-- Submit / Cancel -->
        <div class="card mt-3">
            <div class="card-body text-center">
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

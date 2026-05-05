<div class="page-title">
    <div>
        <h3>Record Payment</h3>
        <div class="meta"><?= e($memo['memo_no']) ?> · <?= e($memo['subject']) ?></div>
    </div>
    <a href="<?= url('/memos/' . $memo['id']) ?>" class="btn btn-light"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="emm-card">
            <div class="emm-card-header"><strong>Payment Details</strong></div>
            <div class="emm-card-body">
                <form method="post" action="<?= url('/memos/' . $memo['id'] . '/payment') ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Payment Date *</label>
                            <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Paid Amount *</label>
                            <input type="number" step="0.01" name="paid_amount" class="form-control" value="<?= number_format($memo['net_amount'] - $totalPaid, 2, '.', '') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Payment Method *</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cash">Cash</option>
                                <option value="company_bank">Company Bank</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="payroll">Payroll</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6"><label class="form-label">Paid To Name</label><input type="text" name="paid_to_name" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Bank Name</label><input type="text" name="bank_name" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Bank Account Name</label><input type="text" name="bank_account_name" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Bank Account Number</label><input type="text" name="bank_account_number" class="form-control"></div>
                        <div class="col-12">
                            <label class="form-label">Payment Slip (PDF/JPG/PNG)</label>
                            <input type="file" name="slip" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Note</label>
                            <textarea name="payment_note" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="text-end mt-4">
                        <a href="<?= url('/memos/' . $memo['id']) ?>" class="btn btn-light">Cancel</a>
                        <button class="btn btn-warning" onclick="return confirm('บันทึกการจ่ายเงินนี้?')">
                            <i class="bi bi-cash-stack"></i> Save Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="emm-card">
            <div class="emm-card-header"><strong>Memo Summary</strong></div>
            <div class="emm-card-body">
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Subject</span><strong style="text-align: right;"><?= e($memo['subject']) ?></strong></div>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Requester</span><span><?= e($memo['requester_name']) ?></span></div>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Net Amount</span><strong class="money"><?= format_money($memo['net_amount']) ?></strong></div>
                <div class="d-flex justify-content-between mb-2"><span class="text-muted">Total Paid</span><span class="money"><?= format_money($totalPaid) ?></span></div>
                <div class="divider"></div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">Balance Due</span>
                    <strong class="money" style="font-size: 18px; color: <?= ($memo['net_amount'] - $totalPaid) > 0 ? 'var(--emm-danger)' : 'var(--emm-success)' ?>;">
                        <?= format_money($memo['net_amount'] - $totalPaid) ?>
                    </strong>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header bg-warning">
                <strong>Record Payment — <?= e($memo['memo_no']) ?></strong>
            </div>
            <div class="card-body">
                <div class="row mb-3 g-2 small">
                    <div class="col-md-4"><span class="text-muted">Subject:</span> <strong><?= e($memo['subject']) ?></strong></div>
                    <div class="col-md-4"><span class="text-muted">Requester:</span> <?= e($memo['requester_name']) ?></div>
                    <div class="col-md-4"><span class="text-muted">Net Amount:</span> <strong><?= format_money($memo['net_amount']) ?></strong> THB</div>
                    <div class="col-md-4"><span class="text-muted">Total Paid:</span> <?= format_money($totalPaid) ?> THB</div>
                    <div class="col-md-4"><span class="text-muted">Balance:</span> <strong class="<?= ($memo['net_amount']-$totalPaid) > 0 ? 'text-danger' : 'text-success' ?>"><?= format_money($memo['net_amount'] - $totalPaid) ?></strong></div>
                </div>

                <form method="post" action="<?= url('/memos/' . $memo['id'] . '/payment') ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Payment Date *</label>
                            <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Paid Amount *</label>
                            <input type="number" step="0.01" name="paid_amount" class="form-control" value="<?= format_money($memo['net_amount'] - $totalPaid) ?>" required>
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
                        <div class="col-md-6">
                            <label class="form-label">Paid To Name</label>
                            <input type="text" name="paid_to_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bank Account Name</label>
                            <input type="text" name="bank_account_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bank Account Number</label>
                            <input type="text" name="bank_account_number" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Payment Slip (PDF/JPG/PNG)</label>
                            <input type="file" name="slip" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Note</label>
                            <textarea name="payment_note" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <a href="<?= url('/memos/' . $memo['id']) ?>" class="btn btn-light">Cancel</a>
                        <button class="btn btn-warning" onclick="return confirm('บันทึกการจ่ายเงินนี้?')">
                            <i class="bi bi-cash-stack"></i> Save Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

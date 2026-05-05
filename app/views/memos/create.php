<?php $u = user(); ?>
<form method="post" action="<?= url('/memos') ?>" class="row g-3">
    <?= csrf_field() ?>

    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white"><strong>Memo Header</strong></div>
            <div class="card-body row g-3">
                <div class="col-md-3">
                    <label class="form-label">Company *</label>
                    <select name="company_id" id="company_id" class="form-select" required>
                        <?php foreach ($companies as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $u['company_id'] == $c['id'] ? 'selected' : '' ?>><?= e($c['company_code']) ?> — <?= e($c['company_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Department *</label>
                    <select name="department_id" id="department_id" class="form-select" required>
                        <?php foreach ($depts as $d): ?>
                            <option value="<?= $d['id'] ?>" <?= $u['department_id'] == $d['id'] ? 'selected' : '' ?>><?= e($d['department_code']) ?> — <?= e($d['department_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Memo Date *</label>
                    <input type="date" name="memo_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Required Payment Date</label>
                    <input type="date" name="required_payment_date" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Memo Type *</label>
                    <select name="memo_type" class="form-select" required>
                        <option value="general_expense">General Expense / ค่าใช้จ่ายทั่วไป</option>
                        <option value="advance_payment">Advance Payment / ขอเงินสำรอง</option>
                        <option value="reimbursement">Reimbursement / เบิกคืน</option>
                        <option value="supplier_payment">Supplier Payment</option>
                        <option value="petty_cash">Petty Cash</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Project / Campaign / Trip</label>
                    <select name="project_id" class="form-select">
                        <option value="">— ไม่ระบุ —</option>
                        <?php foreach ($projects as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= e($p['project_code']) ?> — <?= e($p['project_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Requester</label>
                    <input type="text" class="form-control" value="<?= e($u['full_name']) ?>" disabled>
                </div>

                <div class="col-12">
                    <label class="form-label">Subject *</label>
                    <input type="text" name="subject" class="form-control" placeholder="หัวข้อเรื่อง" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Objective / วัตถุประสงค์</label>
                    <textarea name="objective" class="form-control" rows="3"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Description / รายละเอียด</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="card-footer bg-white text-end">
                <a href="<?= url('/memos') ?>" class="btn btn-light">Cancel</a>
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-save"></i> Save Draft & Continue
                </button>
            </div>
        </div>
    </div>
</form>

<script>
// Cascade Department by Company
document.getElementById('company_id').addEventListener('change', async function() {
    const companyId = this.value;
    const sel = document.getElementById('department_id');
    sel.innerHTML = '<option>Loading...</option>';
    const res = await fetch('<?= url('/api/companies/') ?>' + companyId + '/departments');
    const data = await res.json();
    sel.innerHTML = '';
    data.forEach(d => {
        const opt = document.createElement('option');
        opt.value = d.id;
        opt.textContent = d.department_code + ' — ' + d.department_name;
        sel.appendChild(opt);
    });
});
</script>

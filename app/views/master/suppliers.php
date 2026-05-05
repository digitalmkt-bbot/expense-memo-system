<div class="page-title">
    <div>
        <h3>Suppliers</h3>
        <div class="meta">รายชื่อ Supplier / Vendor / ผู้รับเงิน — <?= count($suppliers) ?> suppliers</div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="emm-card">
            <div class="emm-card-header"><strong>All Suppliers</strong></div>
            <div class="table-responsive">
                <table class="emm-table">
                    <thead><tr><th>Code</th><th>Name · Contact</th><th>Tax ID</th><th>Bank</th><th></th></tr></thead>
                    <tbody>
                        <?php foreach ($suppliers as $s): ?>
                            <tr>
                                <td><span class="role-tag" style="background: var(--emm-bg); color: var(--emm-text-muted);"><?= e($s['supplier_code'] ?? '—') ?></span></td>
                                <td>
                                    <strong><?= e($s['supplier_name']) ?></strong>
                                    <?php if (!empty($s['contact_name'])): ?><br><small class="text-soft"><?= e($s['contact_name']) ?> · <?= e($s['phone']) ?></small><?php endif; ?>
                                </td>
                                <td class="text-muted small"><?= e($s['tax_id']) ?></td>
                                <td class="text-muted small"><?= e($s['bank_name']) ?> <?= e($s['bank_account_number']) ?></td>
                                <td><button class="btn btn-sm btn-outline-secondary" onclick='edit(<?= json_encode($s) ?>)'><i class="bi bi-pencil"></i></button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="emm-card">
            <div class="emm-card-header"><strong id="ttl">Add Supplier</strong></div>
            <form method="post" action="<?= url('/master/suppliers') ?>" class="emm-card-body">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="id">
                <div class="row g-2">
                    <div class="col-12"><label class="form-label">Code</label><input name="supplier_code" id="supplier_code" class="form-control form-control-sm"></div>
                    <div class="col-12"><label class="form-label">Name *</label><input name="supplier_name" id="supplier_name" class="form-control form-control-sm" required></div>
                    <div class="col-6"><label class="form-label">Contact</label><input name="contact_name" id="contact_name" class="form-control form-control-sm"></div>
                    <div class="col-6"><label class="form-label">Phone</label><input name="phone" id="phone" class="form-control form-control-sm"></div>
                    <div class="col-12"><label class="form-label">Email</label><input name="email" id="email" class="form-control form-control-sm"></div>
                    <div class="col-12"><label class="form-label">Tax ID</label><input name="tax_id" id="tax_id" class="form-control form-control-sm"></div>
                    <div class="col-12"><label class="form-label">Bank Name</label><input name="bank_name" id="bank_name" class="form-control form-control-sm"></div>
                    <div class="col-12"><label class="form-label">Account Name</label><input name="bank_account_name" id="bank_account_name" class="form-control form-control-sm"></div>
                    <div class="col-12"><label class="form-label">Account No.</label><input name="bank_account_number" id="bank_account_number" class="form-control form-control-sm"></div>
                    <div class="col-12"><label style="display: flex; align-items: center; gap: 8px;"><input type="checkbox" name="is_active" id="is_active" value="1" checked> <span class="form-label" style="margin: 0;">Active</span></label></div>
                </div>
                <button class="btn btn-primary btn-sm w-100 mt-3"><i class="bi bi-save"></i> Save</button>
            </form>
        </div>
    </div>
</div>

<script>
function edit(s) {
    document.getElementById('ttl').textContent = 'Edit Supplier #' + s.id;
    for (const k of ['id','supplier_code','supplier_name','contact_name','phone','email','tax_id','bank_name','bank_account_name','bank_account_number'])
        document.getElementById(k).value = s[k] || '';
    document.getElementById('is_active').checked = !!s.is_active;
}
</script>

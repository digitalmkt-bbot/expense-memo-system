<div class="row g-3">
    <div class="col-md-8">
        <div class="card"><div class="card-header bg-white"><strong>Suppliers</strong></div>
        <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead class="table-light"><tr><th>Code</th><th>Name</th><th>Contact</th><th>Tax ID</th><th>Bank</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($suppliers as $s): ?>
                <tr>
                    <td><?= e($s['supplier_code']) ?></td>
                    <td><strong><?= e($s['supplier_name']) ?></strong>
                        <br><small class="text-muted"><?= e($s['phone']) ?> · <?= e($s['email']) ?></small></td>
                    <td><?= e($s['contact_name']) ?></td>
                    <td><?= e($s['tax_id']) ?></td>
                    <td><small><?= e($s['bank_name']) ?> <?= e($s['bank_account_number']) ?></small></td>
                    <td><button class="btn btn-sm btn-outline-primary" onclick='edit(<?= json_encode($s) ?>)'><i class="bi bi-pencil"></i></button></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card"><div class="card-header bg-white"><strong id="ttl">Add Supplier</strong></div>
        <form method="post" action="<?= url('/master/suppliers') ?>" class="card-body">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="id">
            <div class="mb-2"><label class="form-label">Code</label><input name="supplier_code" id="supplier_code" class="form-control form-control-sm"></div>
            <div class="mb-2"><label class="form-label">Name *</label><input name="supplier_name" id="supplier_name" class="form-control form-control-sm" required></div>
            <div class="mb-2"><label class="form-label">Contact</label><input name="contact_name" id="contact_name" class="form-control form-control-sm"></div>
            <div class="mb-2"><label class="form-label">Phone</label><input name="phone" id="phone" class="form-control form-control-sm"></div>
            <div class="mb-2"><label class="form-label">Email</label><input name="email" id="email" class="form-control form-control-sm"></div>
            <div class="mb-2"><label class="form-label">Tax ID</label><input name="tax_id" id="tax_id" class="form-control form-control-sm"></div>
            <div class="mb-2"><label class="form-label">Bank Name</label><input name="bank_name" id="bank_name" class="form-control form-control-sm"></div>
            <div class="mb-2"><label class="form-label">Bank Acc Name</label><input name="bank_account_name" id="bank_account_name" class="form-control form-control-sm"></div>
            <div class="mb-2"><label class="form-label">Bank Acc No.</label><input name="bank_account_number" id="bank_account_number" class="form-control form-control-sm"></div>
            <div class="mb-3 form-check"><input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" checked><label class="form-check-label">Active</label></div>
            <button class="btn btn-primary btn-sm w-100"><i class="bi bi-save"></i> Save</button>
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

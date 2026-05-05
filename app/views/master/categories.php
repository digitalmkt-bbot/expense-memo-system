<div class="row g-3">
    <div class="col-md-7">
        <div class="card"><div class="card-header bg-white"><strong>Expense Categories</strong></div>
        <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead class="table-light"><tr><th>Code</th><th>Name</th><th>Acc Code</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($categories as $c): ?>
                <tr>
                    <td><strong><?= e($c['category_code']) ?></strong></td>
                    <td><?= e($c['category_name']) ?></td>
                    <td><?= e($c['accounting_code']) ?></td>
                    <td><?= $c['is_active'] ? 'Active' : '-' ?></td>
                    <td><button class="btn btn-sm btn-outline-primary" onclick='edit(<?= json_encode($c) ?>)'><i class="bi bi-pencil"></i></button></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div></div>
    </div>
    <div class="col-md-5">
        <div class="card"><div class="card-header bg-white"><strong id="ttl">Add Category</strong></div>
        <form method="post" action="<?= url('/master/categories') ?>" class="card-body">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="id">
            <div class="mb-2"><label class="form-label">Code *</label><input name="category_code" id="category_code" class="form-control form-control-sm" required></div>
            <div class="mb-2"><label class="form-label">Name *</label><input name="category_name" id="category_name" class="form-control form-control-sm" required></div>
            <div class="mb-2"><label class="form-label">Accounting Code</label><input name="accounting_code" id="accounting_code" class="form-control form-control-sm"></div>
            <div class="mb-3 form-check"><input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" checked><label class="form-check-label">Active</label></div>
            <button class="btn btn-primary btn-sm w-100"><i class="bi bi-save"></i> Save</button>
        </form>
        </div>
    </div>
</div>
<script>
function edit(c) {
    document.getElementById('ttl').textContent = 'Edit #' + c.id;
    for (const k of ['id','category_code','category_name','accounting_code']) document.getElementById(k).value = c[k] || '';
    document.getElementById('is_active').checked = !!c.is_active;
}
</script>

<div class="page-title">
    <div>
        <h3>Expense Categories</h3>
        <div class="meta">หมวดค่าใช้จ่าย — <?= count($categories) ?> categories</div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="emm-card">
            <div class="emm-card-header"><strong>All Categories</strong></div>
            <div class="table-responsive">
                <table class="emm-table">
                    <thead><tr><th>Code</th><th>Name</th><th>Acc Code</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        <?php foreach ($categories as $c): ?>
                            <tr>
                                <td><span class="role-tag"><?= e($c['category_code']) ?></span></td>
                                <td><?= e($c['category_name']) ?></td>
                                <td class="text-muted small"><?= e($c['accounting_code'] ?? '-') ?></td>
                                <td><?= $c['is_active'] ? '<span class="status approved">Active</span>' : '<span class="status cancelled">Inactive</span>' ?></td>
                                <td><button class="btn btn-sm btn-outline-secondary" onclick='edit(<?= json_encode($c) ?>)'><i class="bi bi-pencil"></i></button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="emm-card">
            <div class="emm-card-header"><strong id="ttl">Add Category</strong></div>
            <form method="post" action="<?= url('/master/categories') ?>" class="emm-card-body">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="id">
                <div class="row g-2">
                    <div class="col-12"><label class="form-label">Code *</label><input name="category_code" id="category_code" class="form-control form-control-sm" required></div>
                    <div class="col-12"><label class="form-label">Name *</label><input name="category_name" id="category_name" class="form-control form-control-sm" required></div>
                    <div class="col-12"><label class="form-label">Accounting Code</label><input name="accounting_code" id="accounting_code" class="form-control form-control-sm"></div>
                    <div class="col-12"><label style="display: flex; align-items: center; gap: 8px;"><input type="checkbox" name="is_active" id="is_active" value="1" checked> <span class="form-label" style="margin: 0;">Active</span></label></div>
                </div>
                <button class="btn btn-primary btn-sm w-100 mt-3"><i class="bi bi-save"></i> Save</button>
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

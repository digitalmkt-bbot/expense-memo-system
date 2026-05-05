<div class="row g-3">
    <div class="col-md-8">
        <div class="card"><div class="card-header bg-white"><strong>Projects / Campaigns / Trips</strong></div>
        <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead class="table-light"><tr><th>Code</th><th>Name</th><th>Company</th><th>Period</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($projects as $p): ?>
                <tr>
                    <td><strong><?= e($p['project_code']) ?></strong></td>
                    <td><?= e($p['project_name']) ?></td>
                    <td><small><?= e($p['company_code']) ?></small></td>
                    <td><small><?= format_date($p['start_date']) ?> - <?= format_date($p['end_date']) ?></small></td>
                    <td><?= e($p['status']) ?></td>
                    <td><button class="btn btn-sm btn-outline-primary" onclick='edit(<?= json_encode($p) ?>)'><i class="bi bi-pencil"></i></button></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card"><div class="card-header bg-white"><strong id="ttl">Add Project</strong></div>
        <form method="post" action="<?= url('/master/projects') ?>" class="card-body">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="id">
            <div class="mb-2"><label class="form-label">Code *</label><input name="project_code" id="project_code" class="form-control form-control-sm" required></div>
            <div class="mb-2"><label class="form-label">Name *</label><input name="project_name" id="project_name" class="form-control form-control-sm" required></div>
            <div class="mb-2"><label class="form-label">Company</label>
                <select name="company_id" id="company_id" class="form-select form-select-sm">
                    <option value="">—</option>
                    <?php foreach ($companies as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= e($c['company_code']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-2"><label class="form-label">Start</label><input type="date" name="start_date" id="start_date" class="form-control form-control-sm"></div>
            <div class="mb-2"><label class="form-label">End</label><input type="date" name="end_date" id="end_date" class="form-control form-control-sm"></div>
            <div class="mb-3"><label class="form-label">Status</label>
                <select name="status" id="status" class="form-select form-select-sm">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <button class="btn btn-primary btn-sm w-100"><i class="bi bi-save"></i> Save</button>
        </form>
        </div>
    </div>
</div>
<script>
function edit(p) {
    document.getElementById('ttl').textContent = 'Edit Project #' + p.id;
    for (const k of ['id','project_code','project_name','company_id','start_date','end_date','status'])
        document.getElementById(k).value = p[k] || '';
}
</script>

<div class="page-title">
    <div>
        <h3>Projects</h3>
        <div class="meta">Project / Campaign / Trip — <?= count($projects) ?> projects</div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="emm-card">
            <div class="emm-card-header"><strong>All Projects</strong></div>
            <div class="table-responsive">
                <table class="emm-table">
                    <thead><tr><th>Code</th><th>Name</th><th>Company</th><th>Period</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        <?php foreach ($projects as $p): ?>
                            <tr>
                                <td><span class="role-tag" style="background: var(--emm-bg); color: var(--emm-text-muted);"><?= e($p['project_code']) ?></span></td>
                                <td><?= e($p['project_name']) ?></td>
                                <td class="text-muted small"><?= e($p['company_code'] ?? '-') ?></td>
                                <td class="text-muted small"><?= format_date($p['start_date']) ?> → <?= format_date($p['end_date']) ?></td>
                                <td><span class="status <?= $p['status'] === 'active' ? 'approved' : 'cancelled' ?>"><?= e($p['status']) ?></span></td>
                                <td><button class="btn btn-sm btn-outline-secondary" onclick='edit(<?= json_encode($p) ?>)'><i class="bi bi-pencil"></i></button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="emm-card">
            <div class="emm-card-header"><strong id="ttl">Add Project</strong></div>
            <form method="post" action="<?= url('/master/projects') ?>" class="emm-card-body">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="id">
                <div class="row g-2">
                    <div class="col-12"><label class="form-label">Code *</label><input name="project_code" id="project_code" class="form-control form-control-sm" required></div>
                    <div class="col-12"><label class="form-label">Name *</label><input name="project_name" id="project_name" class="form-control form-control-sm" required></div>
                    <div class="col-12"><label class="form-label">Company</label>
                        <select name="company_id" id="company_id" class="form-select form-select-sm">
                            <option value="">—</option>
                            <?php foreach ($companies as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= e($c['company_code']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6"><label class="form-label">Start</label><input type="date" name="start_date" id="start_date" class="form-control form-control-sm"></div>
                    <div class="col-6"><label class="form-label">End</label><input type="date" name="end_date" id="end_date" class="form-control form-control-sm"></div>
                    <div class="col-12"><label class="form-label">Status</label>
                        <select name="status" id="status" class="form-select form-select-sm">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
                <button class="btn btn-primary btn-sm w-100 mt-3"><i class="bi bi-save"></i> Save</button>
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

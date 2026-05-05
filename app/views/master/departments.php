<div class="page-title">
    <div>
        <h3>Departments</h3>
        <div class="meta">แผนกในแต่ละบริษัท</div>
    </div>
</div>

<div class="emm-card">
    <div class="table-responsive">
        <table class="emm-table">
            <thead><tr><th>Company</th><th>Code</th><th>Name</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($departments as $d): ?>
                <tr>
                    <td><span class="role-tag"><?= e($d['company_code']) ?></span></td>
                    <td><span class="role-tag" style="background: var(--emm-bg); color: var(--emm-text-muted);"><?= e($d['department_code']) ?></span></td>
                    <td><?= e($d['department_name']) ?></td>
                    <td><?= $d['is_active'] ? '<span class="status approved">Active</span>' : '<span class="status cancelled">Inactive</span>' ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

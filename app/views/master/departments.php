<div class="card">
    <div class="card-header bg-white"><strong>Departments</strong></div>
    <div class="table-responsive">
    <table class="table table-sm mb-0">
        <thead class="table-light"><tr><th>Company</th><th>Code</th><th>Name</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($departments as $d): ?>
            <tr>
                <td><small><?= e($d['company_code']) ?></small></td>
                <td><strong><?= e($d['department_code']) ?></strong></td>
                <td><?= e($d['department_name']) ?></td>
                <td><?= $d['is_active'] ? 'Active' : 'Inactive' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

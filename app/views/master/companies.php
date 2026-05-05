<div class="page-title">
    <div>
        <h3>Companies</h3>
        <div class="meta">บริษัทในระบบ</div>
    </div>
</div>

<div class="emm-card">
    <div class="table-responsive">
        <table class="emm-table">
            <thead><tr><th>Code</th><th>Name</th><th>Tax ID</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($companies as $c): ?>
                <tr>
                    <td><span class="role-tag"><?= e($c['company_code']) ?></span></td>
                    <td><strong><?= e($c['company_name']) ?></strong></td>
                    <td class="text-muted small"><?= e($c['tax_id']) ?></td>
                    <td><?= $c['is_active'] ? '<span class="status approved">Active</span>' : '<span class="status cancelled">Inactive</span>' ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

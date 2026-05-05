<div class="card">
    <div class="card-header bg-white"><strong>Companies</strong></div>
    <div class="table-responsive">
    <table class="table table-sm mb-0">
        <thead class="table-light"><tr><th>Code</th><th>Name</th><th>Tax ID</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($companies as $c): ?>
            <tr>
                <td><strong><?= e($c['company_code']) ?></strong></td>
                <td><?= e($c['company_name']) ?></td>
                <td><?= e($c['tax_id']) ?></td>
                <td><?= $c['is_active'] ? 'Active' : 'Inactive' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

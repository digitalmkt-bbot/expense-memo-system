<div class="alert alert-info">
    Approval Rules ใช้ตัดสินว่า Memo จำนวนเงินไหนต้องผ่านใครบ้าง<br>
    <small>แก้ไข Rules ใน DB หรือเพิ่ม UI ภายหลัง — Default seeded จาก <code>02_seed.sql</code></small>
</div>

<div class="card"><div class="table-responsive">
<table class="table table-hover table-sm mb-0">
    <thead class="table-light"><tr>
        <th>Company</th><th>Memo Type</th>
        <th class="text-end">Min Amount</th><th class="text-end">Max Amount</th>
        <th class="text-center">Level</th><th>Approver Role</th><th>Status</th>
    </tr></thead>
    <tbody>
        <?php foreach ($rules as $r): ?>
            <tr>
                <td><strong><?= e($r['company_code'] ?? 'ALL') ?></strong></td>
                <td><?= e($r['memo_type'] ?? 'ALL') ?></td>
                <td class="text-end"><?= format_money($r['min_amount']) ?></td>
                <td class="text-end"><?= $r['max_amount'] ? format_money($r['max_amount']) : '∞' ?></td>
                <td class="text-center"><span class="badge bg-secondary"><?= e($r['approval_level']) ?></span></td>
                <td><strong class="text-primary"><?= e($r['approver_role']) ?></strong></td>
                <td><?= $r['is_active'] ? '<span class="text-success">Active</span>' : '<span class="text-muted">Inactive</span>' ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div></div>

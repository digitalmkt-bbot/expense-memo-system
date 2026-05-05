<div class="page-title">
    <div>
        <h3>Approval Rules</h3>
        <div class="meta">กฎการอนุมัติตามจำนวนเงิน</div>
    </div>
</div>

<div class="alert alert-info mb-3">
    <i class="bi bi-info-circle"></i> Approval Rules ใช้ตัดสินว่า Memo จำนวนเงินไหนต้องผ่านใครบ้าง — แก้ไขได้ใน DB หรือเพิ่ม UI ภายหลัง
</div>

<div class="emm-card">
    <div class="table-responsive">
        <table class="emm-table">
            <thead>
                <tr>
                    <th>Company</th><th>Memo Type</th>
                    <th class="text-end">Min Amount</th><th class="text-end">Max Amount</th>
                    <th class="text-center">Level</th><th>Approver Role</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rules as $r): ?>
                    <tr>
                        <td><span class="role-tag"><?= e($r['company_code'] ?? 'ALL') ?></span></td>
                        <td class="text-muted small"><?= e($r['memo_type'] ?? 'ALL') ?></td>
                        <td class="text-end money"><?= format_money($r['min_amount']) ?></td>
                        <td class="text-end money"><?= $r['max_amount'] ? format_money($r['max_amount']) : '∞' ?></td>
                        <td class="text-center"><span class="role-tag" style="background: var(--emm-bg); color: var(--emm-text-muted);">L<?= e($r['approval_level']) ?></span></td>
                        <td><strong style="color: var(--emm-primary);"><?= e($r['approver_role']) ?></strong></td>
                        <td><?= $r['is_active'] ? '<span class="status approved">Active</span>' : '<span class="status cancelled">Inactive</span>' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

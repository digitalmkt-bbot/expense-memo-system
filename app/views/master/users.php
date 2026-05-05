<div class="page-title">
    <div>
        <h3>Users</h3>
        <div class="meta">จัดการผู้ใช้งานระบบ — <?= count($users) ?> users</div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="emm-card">
            <div class="emm-card-header"><strong>All Users</strong></div>
            <div class="table-responsive">
                <table class="emm-table">
                    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Company / Dept</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <?php
                                // Director with NULL company_id → "All Companies"
                                $companyDisplay = $u['company_code'] ?? null;
                                if (!$companyDisplay && $u['role'] === 'director') {
                                    $companyDisplay = '🌐 All Companies';
                                } elseif (!$companyDisplay) {
                                    $companyDisplay = '-';
                                }
                            ?>
                            <tr>
                                <td>
                                    <span class="avatar-sm"><?= strtoupper(substr($u['full_name'] ?? 'U', 0, 1)) ?></span>
                                    <strong><?= e($u['full_name']) ?></strong>
                                    <?php if (!empty($u['position'])): ?><br><small class="text-soft"><?= e($u['position']) ?></small><?php endif; ?>
                                </td>
                                <td><span class="text-muted small"><?= e($u['email']) ?></span></td>
                                <td><span class="role-tag"><?= e($u['role']) ?></span></td>
                                <td><span class="text-muted small"><?= e($companyDisplay) ?> <?= !empty($u['department_code']) ? '/ ' . e($u['department_code']) : '' ?></span></td>
                                <td><?= $u['is_active'] ? '<span class="status approved">Active</span>' : '<span class="status cancelled">Inactive</span>' ?></td>
                                <td style="white-space: nowrap;">
                                    <button class="btn btn-sm btn-outline-secondary" onclick='editUser(<?= json_encode($u) ?>)' title="Edit"><i class="bi bi-pencil"></i></button>
                                    <?php $self = (int) user()['id'] === (int) $u['id']; ?>
                                    <?php if (!$self): ?>
                                        <form method="post" action="<?= url('/master/users/' . $u['id'] . '/toggle') ?>" style="display:inline;" onsubmit="return confirm('<?= $u['is_active'] ? 'ปิดใช้งาน' : 'เปิดใช้งาน' ?> user นี้?')">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-sm <?= $u['is_active'] ? 'btn-outline-warning' : 'btn-outline-success' ?>" title="<?= $u['is_active'] ? 'Disable' : 'Enable' ?>">
                                                <i class="bi <?= $u['is_active'] ? 'bi-pause-circle' : 'bi-play-circle' ?>"></i>
                                            </button>
                                        </form>
                                        <form method="post" action="<?= url('/master/users/' . $u['id'] . '/delete') ?>" style="display:inline;" onsubmit="return confirm('ลบ user นี้?\n\n⚠️ ลบได้เฉพาะ user ที่ยังไม่มีข้อมูลในระบบ\nหากมีข้อมูลแล้ว ระบบจะแนะนำให้ปิดใช้งานแทน')">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-soft small" style="margin-left: 4px;" title="Cannot modify yourself">— self —</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="emm-card">
            <div class="emm-card-header"><strong id="userFormTitle">Add User</strong></div>
            <form method="post" action="<?= url('/master/users') ?>" class="emm-card-body">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="user_id">
                <div class="row g-2">
                    <div class="col-12"><label class="form-label">Full Name *</label><input name="full_name" id="full_name" class="form-control form-control-sm" required></div>
                    <div class="col-12"><label class="form-label">Email *</label><input type="email" name="email" id="email" class="form-control form-control-sm" required></div>
                    <div class="col-12"><label class="form-label">Password</label><input type="password" name="password" class="form-control form-control-sm" placeholder="ปล่อยว่าง = ไม่เปลี่ยน"></div>
                    <div class="col-6"><label class="form-label">Phone</label><input name="phone" id="phone" class="form-control form-control-sm"></div>
                    <div class="col-6"><label class="form-label">Position</label><input name="position" id="position" class="form-control form-control-sm"></div>
                    <div class="col-12"><label class="form-label">Role *</label>
                        <select name="role" id="role" class="form-select form-select-sm" required>
                            <option value="requester">Requester</option>
                            <option value="manager">Manager</option>
                            <option value="accounting">Accounting</option>
                            <option value="director">Director</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="col-6"><label class="form-label">Company</label>
                        <select name="company_id" id="company_id" class="form-select form-select-sm">
                            <option value="">🌐 All Companies (Director)</option>
                            <?php foreach ($companies as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= e($c['company_code']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6"><label class="form-label">Department</label>
                        <select name="department_id" id="department_id" class="form-select form-select-sm">
                            <option value="">—</option>
                            <?php foreach ($depts as $d): ?>
                                <option value="<?= $d['id'] ?>" data-company="<?= $d['company_id'] ?>"><?= e($d['department_code']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12"><small class="text-soft" style="font-size: 11.5px;">
                        💡 <strong>Director ที่ดูแลทั้ง 2 บริษัท</strong> — เลือก "🌐 All Companies" เพื่อให้ approve memo จากทุกบริษัท
                    </small></div>
                    <div class="col-12">
                        <label style="display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" name="is_active" id="is_active" value="1" checked> <span class="form-label" style="margin: 0;">Active</span>
                        </label>
                    </div>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-primary btn-sm flex-fill"><i class="bi bi-save"></i> Save</button>
                    <button type="reset" onclick="resetUserForm()" class="btn btn-light btn-sm">Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editUser(u) {
    document.getElementById('userFormTitle').textContent = 'Edit User #' + u.id;
    for (const k of ['user_id','full_name','email','phone','position','role','company_id','department_id']) {
        const el = document.getElementById(k);
        if (el) el.value = (k === 'user_id' ? u.id : u[k]) || '';
    }
    document.getElementById('is_active').checked = !!u.is_active;
}
function resetUserForm() {
    document.getElementById('userFormTitle').textContent = 'Add User';
    document.getElementById('user_id').value = '';
}
</script>

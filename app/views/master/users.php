<div class="row g-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between">
                <strong>Users (<?= count($users) ?>)</strong>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light"><tr>
                        <th>Name</th><th>Email</th><th>Role</th><th>Company/Dept</th><th>Status</th><th></th>
                    </tr></thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?= e($u['full_name']) ?>
                                    <br><small class="text-muted"><?= e($u['position'] ?? '') ?></small></td>
                                <td><small><?= e($u['email']) ?></small></td>
                                <td><span class="badge bg-secondary"><?= e($u['role']) ?></span></td>
                                <td><small><?= e($u['company_code'] ?? '-') ?> / <?= e($u['department_code'] ?? '-') ?></small></td>
                                <td><?= $u['is_active'] ? '<span class="text-success">Active</span>' : '<span class="text-muted">Inactive</span>' ?></td>
                                <td><button class="btn btn-sm btn-outline-primary" onclick='editUser(<?= json_encode($u) ?>)'><i class="bi bi-pencil"></i></button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white"><strong id="userFormTitle">Add User</strong></div>
            <form method="post" action="<?= url('/master/users') ?>" class="card-body">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="user_id">
                <div class="mb-2"><label class="form-label">Full Name *</label><input name="full_name" id="full_name" class="form-control form-control-sm" required></div>
                <div class="mb-2"><label class="form-label">Email *</label><input type="email" name="email" id="email" class="form-control form-control-sm" required></div>
                <div class="mb-2"><label class="form-label">Password</label><input type="password" name="password" class="form-control form-control-sm" placeholder="ปล่อยว่าง = ไม่เปลี่ยน"></div>
                <div class="mb-2"><label class="form-label">Phone</label><input name="phone" id="phone" class="form-control form-control-sm"></div>
                <div class="mb-2"><label class="form-label">Position</label><input name="position" id="position" class="form-control form-control-sm"></div>
                <div class="mb-2">
                    <label class="form-label">Role *</label>
                    <select name="role" id="role" class="form-select form-select-sm" required>
                        <option value="requester">Requester</option>
                        <option value="manager">Manager</option>
                        <option value="accounting">Accounting</option>
                        <option value="director">Director</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Company</label>
                    <select name="company_id" id="company_id" class="form-select form-select-sm">
                        <option value="">—</option>
                        <?php foreach ($companies as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= e($c['company_code']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label">Department</label>
                    <select name="department_id" id="department_id" class="form-select form-select-sm">
                        <option value="">—</option>
                        <?php foreach ($depts as $d): ?>
                            <option value="<?= $d['id'] ?>" data-company="<?= $d['company_id'] ?>"><?= e($d['department_code']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3 form-check"><input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" checked><label class="form-check-label">Active</label></div>
                <div class="d-flex gap-2">
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
    document.getElementById('user_id').value     = u.id;
    document.getElementById('full_name').value   = u.full_name || '';
    document.getElementById('email').value       = u.email || '';
    document.getElementById('phone').value       = u.phone || '';
    document.getElementById('position').value    = u.position || '';
    document.getElementById('role').value        = u.role;
    document.getElementById('company_id').value  = u.company_id || '';
    document.getElementById('department_id').value = u.department_id || '';
    document.getElementById('is_active').checked = !!u.is_active;
}
function resetUserForm() {
    document.getElementById('userFormTitle').textContent = 'Add User';
    document.getElementById('user_id').value = '';
}
</script>

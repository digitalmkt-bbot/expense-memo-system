<div class="page-title">
    <div>
        <h3>Pending Approval</h3>
        <div class="meta">Memo ที่รอการอนุมัติของคุณ — Role: <strong><?= e($role) ?></strong></div>
    </div>
    <span class="role-tag" style="font-size: 12px; padding: 4px 12px;"><?= count($memos) ?> รายการ</span>
</div>

<div class="alert alert-info mb-3" style="display: flex; gap: 10px;">
    <i class="bi bi-info-circle" style="font-size: 16px; flex-shrink: 0;"></i>
    <div>
        <strong>คุณคือ <?= e($role) ?></strong> —
        <?php if ($role === 'manager'):    ?>แสดง Memo ที่ submitted (รอ Manager อนุมัติ)<?php endif; ?>
        <?php if ($role === 'accounting'): ?>แสดง Memo ที่ Manager อนุมัติแล้ว (รอบัญชีตรวจ)<?php endif; ?>
        <?php if ($role === 'director'):   ?>แสดง Memo ที่บัญชีตรวจแล้ว (รอ Director อนุมัติ)<?php endif; ?>
        <?php if ($role === 'admin'):      ?>แสดง Memo ทุกขั้นที่รออนุมัติ<?php endif; ?>
    </div>
</div>

<div class="emm-card">
    <div class="table-responsive">
        <table class="emm-table">
            <thead>
                <tr>
                    <th>Memo No.</th><th>Date</th><th>Company</th><th>Subject · Type</th>
                    <th>Requester</th><th class="text-end">Net</th><th>Status</th><th>Submitted</th><th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$memos): ?>
                    <tr><td colspan="9" class="text-center" style="padding: 40px 14px; color: var(--emm-text-soft);">
                        <i class="bi bi-check-circle" style="font-size: 32px; display: block; margin-bottom: 8px; color: var(--emm-success); opacity: .5;"></i>
                        ไม่มี Memo รออนุมัติของคุณ
                    </td></tr>
                <?php endif; ?>
                <?php foreach ($memos as $m): ?>
                    <tr>
                        <td><a href="<?= url('/memos/' . $m['id']) ?>" style="font-weight: 600;"><?= e($m['memo_no']) ?></a></td>
                        <td class="text-muted small"><?= format_date($m['memo_date']) ?></td>
                        <td><span class="role-tag" style="background: var(--emm-bg); color: var(--emm-text-muted);"><?= e($m['company_code']) ?></span> <small class="text-muted"><?= e($m['department_code']) ?></small></td>
                        <td>
                            <div><?= e($m['subject']) ?></div>
                            <small class="text-soft"><?= e(memo_type_label($m['memo_type'])) ?></small>
                        </td>
                        <td>
                            <span class="avatar-sm"><?= strtoupper(substr($m['requester_name'] ?? 'U', 0, 1)) ?></span>
                            <span class="text-muted small"><?= e($m['requester_name']) ?></span>
                        </td>
                        <td class="text-end money"><?= format_money($m['net_amount']) ?></td>
                        <td><span class="status <?= e($m['status']) ?>"><?= strtoupper(str_replace('_', ' ', $m['status'])) ?></span></td>
                        <td class="text-muted small"><?= format_datetime($m['submitted_at']) ?></td>
                        <td>
                            <a href="<?= url('/memos/' . $m['id']) ?>" class="btn btn-sm btn-primary">
                                <i class="bi bi-arrow-right"></i> Review
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

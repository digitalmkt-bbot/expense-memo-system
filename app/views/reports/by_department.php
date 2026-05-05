<div class="page-title">
    <div>
        <h3>Expense by Department</h3>
        <div class="meta">รวมค่าใช้จ่ายต่อแผนก</div>
    </div>
    <a href="<?= url('/reports') ?>" class="btn btn-light"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="emm-card">
    <div class="table-responsive">
        <table class="emm-table">
            <thead><tr><th>Company</th><th>Code</th><th>Department</th><th class="text-end">Memos</th><th class="text-end">Net</th></tr></thead>
            <tbody>
                <?php $sum = 0; foreach ($rows as $r): $sum += $r['net']; ?>
                    <tr>
                        <td><small class="text-muted"><?= e($r['company_code']) ?></small></td>
                        <td><span class="role-tag" style="background: var(--emm-bg); color: var(--emm-text-muted);"><?= e($r['department_code']) ?></span></td>
                        <td><?= e($r['department_name']) ?></td>
                        <td class="text-end"><?= number_format($r['memo_count']) ?></td>
                        <td class="text-end money"><?= format_money($r['net']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot><tr><td colspan="4" class="text-end">Total:</td><td class="text-end money" style="font-size: 14px;"><?= format_money($sum) ?></td></tr></tfoot>
        </table>
    </div>
</div>

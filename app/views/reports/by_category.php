<div class="page-title">
    <div>
        <h3>Expense by Category</h3>
        <div class="meta">แยกตามหมวดค่าใช้จ่าย</div>
    </div>
    <a href="<?= url('/reports') ?>" class="btn btn-light"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="emm-card">
    <div class="table-responsive">
        <table class="emm-table">
            <thead><tr><th>Code</th><th>Category</th><th class="text-end">Items</th><th class="text-end">Amount</th><th class="text-end">Net</th></tr></thead>
            <tbody>
                <?php $sumN = 0; $sumT = 0; foreach ($rows as $r): $sumN += $r['net']; $sumT += $r['total']; ?>
                    <tr>
                        <td><span class="role-tag" style="background: var(--emm-bg); color: var(--emm-text-muted);"><?= e($r['category_code']) ?></span></td>
                        <td><?= e($r['category_name']) ?></td>
                        <td class="text-end"><?= number_format($r['item_count']) ?></td>
                        <td class="text-end money"><?= format_money($r['total']) ?></td>
                        <td class="text-end money"><?= format_money($r['net']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot><tr><td colspan="3" class="text-end">Total:</td><td class="text-end money"><?= format_money($sumT) ?></td><td class="text-end money" style="font-size: 14px;"><?= format_money($sumN) ?></td></tr></tfoot>
        </table>
    </div>
</div>

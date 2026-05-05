<div class="page-title">
    <div>
        <h3>Expense by Company</h3>
        <div class="meta">รวมค่าใช้จ่ายต่อบริษัท</div>
    </div>
    <a href="<?= url('/reports') ?>" class="btn btn-light"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<div class="emm-card">
    <div class="table-responsive">
        <table class="emm-table">
            <thead>
                <tr><th>Code</th><th>Company Name</th><th class="text-end">Memos</th><th class="text-end">Total</th><th class="text-end">Net Amount</th></tr>
            </thead>
            <tbody>
                <?php $sumNet = 0; $sumTotal = 0; foreach ($rows as $r): $sumNet += $r['net']; $sumTotal += $r['total']; ?>
                    <tr>
                        <td><span class="role-tag"><?= e($r['company_code']) ?></span></td>
                        <td><?= e($r['company_name']) ?></td>
                        <td class="text-end"><?= number_format($r['memo_count']) ?></td>
                        <td class="text-end money"><?= format_money($r['total']) ?></td>
                        <td class="text-end money"><?= format_money($r['net']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end">Grand Total:</td>
                    <td class="text-end money"><?= format_money($sumTotal) ?></td>
                    <td class="text-end money" style="font-size: 14px;"><?= format_money($sumNet) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

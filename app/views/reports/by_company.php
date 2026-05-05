<div class="card"><div class="table-responsive">
<table class="table table-hover table-sm mb-0">
    <thead class="table-light"><tr>
        <th>Company Code</th><th>Company Name</th>
        <th class="text-end">Memo Count</th>
        <th class="text-end">Total Amount</th>
        <th class="text-end">Net Amount</th>
    </tr></thead>
    <tbody>
        <?php $sumNet = 0; $sumTotal = 0; foreach ($rows as $r): $sumNet += $r['net']; $sumTotal += $r['total']; ?>
            <tr>
                <td><strong><?= e($r['company_code']) ?></strong></td>
                <td><?= e($r['company_name']) ?></td>
                <td class="text-end"><?= number_format($r['memo_count']) ?></td>
                <td class="text-end"><?= format_money($r['total']) ?></td>
                <td class="text-end"><strong><?= format_money($r['net']) ?></strong></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot class="table-light"><tr>
        <th colspan="3" class="text-end">Grand Total:</th>
        <th class="text-end"><?= format_money($sumTotal) ?></th>
        <th class="text-end"><?= format_money($sumNet) ?></th>
    </tr></tfoot>
</table>
</div></div>

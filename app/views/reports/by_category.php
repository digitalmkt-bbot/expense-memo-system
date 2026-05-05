<div class="card"><div class="table-responsive">
<table class="table table-hover table-sm mb-0">
    <thead class="table-light"><tr>
        <th>Category</th><th>Name</th>
        <th class="text-end">Items</th>
        <th class="text-end">Amount</th>
        <th class="text-end">Net</th>
    </tr></thead>
    <tbody>
        <?php $sumN = 0; $sumT = 0; foreach ($rows as $r): $sumN += $r['net']; $sumT += $r['total']; ?>
            <tr>
                <td><strong><?= e($r['category_code']) ?></strong></td>
                <td><?= e($r['category_name']) ?></td>
                <td class="text-end"><?= number_format($r['item_count']) ?></td>
                <td class="text-end"><?= format_money($r['total']) ?></td>
                <td class="text-end"><?= format_money($r['net']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot class="table-light"><tr>
        <th colspan="3" class="text-end">Total:</th>
        <th class="text-end"><?= format_money($sumT) ?></th>
        <th class="text-end"><?= format_money($sumN) ?></th>
    </tr></tfoot>
</table>
</div></div>

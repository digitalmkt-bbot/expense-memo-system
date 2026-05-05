<div class="card"><div class="table-responsive">
<table class="table table-hover table-sm mb-0">
    <thead class="table-light"><tr>
        <th>Company</th><th>Dept Code</th><th>Department</th>
        <th class="text-end">Memos</th><th class="text-end">Net</th>
    </tr></thead>
    <tbody>
        <?php $sum = 0; foreach ($rows as $r): $sum += $r['net']; ?>
            <tr>
                <td><small><?= e($r['company_code']) ?></small></td>
                <td><strong><?= e($r['department_code']) ?></strong></td>
                <td><?= e($r['department_name']) ?></td>
                <td class="text-end"><?= number_format($r['memo_count']) ?></td>
                <td class="text-end"><?= format_money($r['net']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot class="table-light"><tr>
        <th colspan="4" class="text-end">Total:</th>
        <th class="text-end"><?= format_money($sum) ?></th>
    </tr></tfoot>
</table>
</div></div>

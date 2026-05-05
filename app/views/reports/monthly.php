<form method="get" class="mb-3">
    <label class="form-label">Year</label>
    <input type="number" name="year" value="<?= e($year) ?>" class="form-control form-control-sm d-inline-block w-auto">
    <button class="btn btn-sm btn-primary">Filter</button>
</form>

<div class="card"><div class="table-responsive">
<table class="table table-hover table-sm mb-0">
    <thead class="table-light"><tr>
        <th>Month</th><th>Company</th>
        <th class="text-end">Memo Count</th>
        <th class="text-end">Net Amount</th>
    </tr></thead>
    <tbody>
        <?php $monthName = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; ?>
        <?php $sum = 0; foreach ($rows as $r): $sum += $r['net']; ?>
            <tr>
                <td><?= e($monthName[$r['m']-1] ?? $r['m']) ?> <?= e($year) ?></td>
                <td><strong><?= e($r['company_code']) ?></strong></td>
                <td class="text-end"><?= number_format($r['cnt']) ?></td>
                <td class="text-end"><?= format_money($r['net']) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="4" class="text-center text-muted">ไม่มีข้อมูล</td></tr><?php endif; ?>
    </tbody>
    <tfoot class="table-light"><tr><th colspan="3" class="text-end">Year Total:</th><th class="text-end"><?= format_money($sum) ?></th></tr></tfoot>
</table>
</div></div>

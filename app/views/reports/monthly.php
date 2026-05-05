<div class="page-title">
    <div>
        <h3>Monthly Summary</h3>
        <div class="meta">สรุปยอดรายเดือน — ปี <?= e($year) ?></div>
    </div>
    <a href="<?= url('/reports') ?>" class="btn btn-light"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<form method="get" class="emm-card mb-3">
    <div class="emm-card-body" style="padding: 12px 16px; display: flex; gap: 10px; align-items: center;">
        <label class="form-label mb-0" style="margin-bottom:0!important;">Year</label>
        <input type="number" name="year" value="<?= e($year) ?>" class="form-control form-control-sm" style="width: 120px;">
        <button class="btn btn-sm btn-primary"><i class="bi bi-funnel"></i> Filter</button>
    </div>
</form>

<div class="emm-card">
    <div class="table-responsive">
        <table class="emm-table">
            <thead><tr><th>Month</th><th>Company</th><th class="text-end">Memos</th><th class="text-end">Net Amount</th></tr></thead>
            <tbody>
                <?php $monthName = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; ?>
                <?php $sum = 0; foreach ($rows as $r): $sum += $r['net']; ?>
                    <tr>
                        <td><?= e($monthName[$r['m']-1] ?? $r['m']) ?> <?= e($year) ?></td>
                        <td><span class="role-tag"><?= e($r['company_code']) ?></span></td>
                        <td class="text-end"><?= number_format($r['cnt']) ?></td>
                        <td class="text-end money"><?= format_money($r['net']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$rows): ?><tr><td colspan="4" class="text-center text-muted" style="padding:30px;">ไม่มีข้อมูล</td></tr><?php endif; ?>
            </tbody>
            <tfoot><tr><td colspan="3" class="text-end">Year Total:</td><td class="text-end money" style="font-size: 14px;"><?= format_money($sum) ?></td></tr></tfoot>
        </table>
    </div>
</div>

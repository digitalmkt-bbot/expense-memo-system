<?php
$mc = $kpi['month_change'] ?? null;
$trendDir = $mc === null ? 'flat' : ($mc >= 0 ? 'up' : 'down');
$trendIcon = $mc === null ? 'bi-dash' : ($mc >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right');
$trendLabel = $mc === null ? '— vs last month' : abs($mc) . '% vs last month';

// Build amounts for sparkline (use trend data)
$trendAmounts = array_map(fn($t) => round($t['amount']), $trend);
$trendLabels  = array_map(fn($t) => $t['label'], $trend);
$trendCounts  = array_map(fn($t) => $t['cnt'], $trend);

// Category amounts max for progress bars
$catMax = 0;
foreach ($topCategories as $c) $catMax = max($catMax, (float) $c['amount']);

// Status distribution map
$statusColors = [
    'draft'              => '#94a3b8',
    'submitted'          => '#5b6cff',
    'manager_approved'   => '#06b6d4',
    'accounting_checked' => '#06b6d4',
    'director_approved'  => '#06b6d4',
    'approved'           => '#10b981',
    'paid'               => '#10b981',
    'pending_payment'    => '#f59e0b',
    'partially_paid'     => '#f59e0b',
    'revision_required'  => '#f97316',
    'rejected'           => '#ef4444',
    'closed'             => '#1e293b',
    'cancelled'          => '#cbd5e1',
];
$statusLabelsJson = json_encode(array_map(fn($r) => strtoupper(str_replace('_',' ',$r['status'])), $statusDist));
$statusValuesJson = json_encode(array_map(fn($r) => (int) $r['cnt'], $statusDist));
$statusColorsJson = json_encode(array_map(fn($r) => $statusColors[$r['status']] ?? '#94a3b8', $statusDist));
?>

<div class="page-title">
    <div>
        <h3>Dashboard</h3>
        <div class="meta">ภาพรวมระบบ Memo ค่าใช้จ่าย — <?= date('d M Y') ?></div>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <div class="period-pills">
            <a href="?period=week">Week</a>
            <a href="?period=month" class="active">Month</a>
            <a href="?period=quarter">Quarter</a>
            <a href="?period=year">Year</a>
        </div>
        <a href="<?= url('/memos/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Create Memo
        </a>
    </div>
</div>

<!-- Hero KPI grid -->
<div class="hero-grid mb-4">
    <div class="hero-kpi gradient-violet">
        <div class="head">
            <div class="ico" style="background: rgba(139,92,246,.15); color: var(--emm-violet);"><i class="bi bi-cash-coin"></i></div>
            <span class="trend <?= $trendDir ?>"><i class="bi <?= $trendIcon ?>"></i> <?= e($trendLabel) ?></span>
        </div>
        <div class="lab">This Month (THB)</div>
        <div class="num"><?= format_money($kpi['this_month']) ?></div>
        <div class="sub">Net amount · <?= date('M Y') ?></div>
        <svg class="spark" width="160" height="48" viewBox="0 0 160 48"><polyline id="spark1" fill="none" stroke="#8b5cf6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></polyline></svg>
    </div>
    <div class="hero-kpi gradient-orange">
        <div class="head">
            <div class="ico" style="background: rgba(249,115,22,.15); color: var(--emm-orange);"><i class="bi bi-clock-history"></i></div>
            <span class="trend flat"><i class="bi bi-arrow-right"></i> awaiting</span>
        </div>
        <div class="lab">Pending Approval</div>
        <div class="num"><?= number_format($kpi['submitted']) ?></div>
        <div class="sub">Memo รออนุมัติ</div>
    </div>
    <div class="hero-kpi gradient-teal">
        <div class="head">
            <div class="ico" style="background: rgba(20,184,166,.15); color: var(--emm-teal);"><i class="bi bi-check2-circle"></i></div>
            <span class="trend up"><i class="bi bi-arrow-up-right"></i> done</span>
        </div>
        <div class="lab">Paid Memos</div>
        <div class="num"><?= number_format($kpi['paid']) ?></div>
        <div class="sub">Payments completed</div>
    </div>
    <div class="hero-kpi gradient-pink">
        <div class="head">
            <div class="ico" style="background: rgba(236,72,153,.15); color: var(--emm-pink);"><i class="bi bi-graph-up-arrow"></i></div>
            <span class="trend up"><i class="bi bi-arrow-up-right"></i> YTD</span>
        </div>
        <div class="lab">Year-to-Date</div>
        <div class="num"><?= format_money($kpi['ytd']) ?></div>
        <div class="sub">Approved spend in <?= date('Y') ?></div>
    </div>
</div>

<!-- Charts row -->
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="chart-card">
            <div class="chart-head">
                <div class="ttl">Monthly Trend
                    <small>ยอดค่าใช้จ่าย Net · ย้อนหลัง 6 เดือน</small>
                </div>
                <div class="d-flex gap-3 align-items-center" style="font-size: 11.5px;">
                    <span style="display: inline-flex; align-items: center; gap: 5px;"><span style="width: 10px; height: 10px; background: linear-gradient(135deg, #5b6cff, #8b5cf6); border-radius: 3px;"></span> Net Amount</span>
                    <span style="display: inline-flex; align-items: center; gap: 5px;"><span style="width: 10px; height: 10px; background: var(--emm-orange); border-radius: 3px; opacity: .8;"></span> Memo Count</span>
                </div>
            </div>
            <div class="chart-body" style="position: relative; height: 280px;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-card">
            <div class="chart-head">
                <div class="ttl">Status Distribution
                    <small>สัดส่วน Memo ตามสถานะ</small>
                </div>
            </div>
            <div class="chart-body" style="position: relative; height: 280px;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top categories + Activity feed -->
<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <div class="emm-card">
            <div class="emm-card-header">
                <strong>Top Spending Categories</strong>
                <span class="text-soft small">ปี <?= date('Y') ?></span>
            </div>
            <div class="emm-card-body">
                <?php if (!$topCategories): ?>
                    <p class="text-center text-muted my-3 mb-0">ยังไม่มีรายการในปีนี้</p>
                <?php endif; ?>
                <?php foreach ($topCategories as $cat): $pct = $catMax > 0 ? round(($cat['amount'] / $catMax) * 100) : 0; ?>
                    <div class="cat-row">
                        <div class="cat-name">
                            <?= e($cat['category_name']) ?>
                            <small><?= number_format($cat['items']) ?> items · <?= e($cat['category_code']) ?></small>
                        </div>
                        <div class="bar"><span style="width: <?= $pct ?>%;"></span></div>
                        <div class="cat-amt"><?= format_money($cat['amount']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="emm-card">
            <div class="emm-card-header">
                <strong>Recent Activity</strong>
                <span class="text-soft small">8 รายการล่าสุด</span>
            </div>
            <div>
                <?php if (!$activity): ?>
                    <p class="text-center text-muted my-3 mb-0" style="padding: 20px;">ยังไม่มี activity</p>
                <?php endif; ?>
                <?php foreach ($activity as $a):
                    $iconMap = [
                        'submitted' => 'bi-send', 'approved' => 'bi-check-lg',
                        'rejected' => 'bi-x-lg', 'revision_required' => 'bi-arrow-counterclockwise',
                        'cancelled' => 'bi-x-circle',
                    ];
                ?>
                    <div class="activity-item">
                        <div class="dot <?= e($a['action']) ?>">
                            <i class="bi <?= e($iconMap[$a['action']] ?? 'bi-circle') ?>"></i>
                        </div>
                        <div class="ttext">
                            <strong><?= e(ucwords(str_replace('_',' ',$a['action']))) ?>
                                <?php if ($a['memo_no']): ?>
                                    <a href="<?= url('/memos/' . $a['memo_id']) ?>" style="font-weight: 600;"><?= e($a['memo_no']) ?></a>
                                <?php endif; ?>
                            </strong>
                            <small><?= e($a['approver_name']) ?> · <?= format_datetime($a['action_at']) ?></small>
                            <?php if ($a['comment']): ?><div class="text-muted" style="font-size: 12px; margin-top: 4px; font-style: italic;">"<?= e($a['comment']) ?>"</div><?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent memos -->
<div class="emm-card">
    <div class="emm-card-header">
        <strong>Recent Memos</strong>
        <a href="<?= url('/memos') ?>" class="btn btn-sm btn-light">View all <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="table-responsive">
        <table class="emm-table">
            <thead>
                <tr><th>Memo No.</th><th>Date</th><th>Company / Dept</th><th>Subject</th><th>Requester</th><th class="text-end">Net Amount</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php if (!$recent): ?>
                    <tr><td colspan="7" class="text-center" style="padding: 40px 14px; color: var(--emm-text-soft);">
                        <i class="bi bi-inbox" style="font-size: 32px; display: block; margin-bottom: 8px; opacity: .5;"></i>
                        ยังไม่มี Memo
                    </td></tr>
                <?php endif; ?>
                <?php foreach ($recent as $m): ?>
                    <tr>
                        <td><a href="<?= url('/memos/' . $m['id']) ?>" style="font-weight: 600;"><?= e($m['memo_no'] ?? 'DRAFT') ?></a></td>
                        <td class="text-muted small"><?= format_date($m['memo_date']) ?></td>
                        <td><span class="role-tag" style="background: var(--emm-bg); color: var(--emm-text-muted);"><?= e($m['company_code']) ?></span> <small class="text-muted"><?= e($m['department_code']) ?></small></td>
                        <td><?= e($m['subject']) ?></td>
                        <td><span class="avatar-sm"><?= strtoupper(substr($m['requester_name'] ?? 'U', 0, 1)) ?></span> <span class="text-muted small"><?= e($m['requester_name']) ?></span></td>
                        <td class="text-end money"><?= format_money($m['net_amount']) ?></td>
                        <td><span class="status <?= e($m['status']) ?>"><?= strtoupper(str_replace('_', ' ', $m['status'])) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Sparkline for hero card 1 (this month)
(function() {
    const data = <?= json_encode($trendAmounts) ?>;
    const max = Math.max(...data, 1);
    const w = 160, h = 48, pad = 4;
    const points = data.map((v, i) => {
        const x = pad + (i * (w - pad*2) / Math.max(data.length - 1, 1));
        const y = h - pad - ((v / max) * (h - pad*2));
        return `${x.toFixed(1)},${y.toFixed(1)}`;
    });
    const el = document.getElementById('spark1');
    if (el) el.setAttribute('points', points.join(' '));
})();

// Trend chart (mixed bar + line)
(function() {
    const ctx = document.getElementById('trendChart');
    if (!ctx || typeof Chart === 'undefined') return;
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($trendLabels) ?>,
            datasets: [{
                type: 'line',
                label: 'Net Amount',
                data: <?= json_encode($trendAmounts) ?>,
                borderColor: '#5b6cff',
                backgroundColor: 'rgba(91,108,255,.12)',
                fill: true, tension: .35,
                yAxisID: 'y', borderWidth: 2,
                pointBackgroundColor: '#5b6cff', pointRadius: 4,
            }, {
                type: 'bar',
                label: 'Memo Count',
                data: <?= json_encode($trendCounts) ?>,
                backgroundColor: 'rgba(249,115,22,.6)',
                borderRadius: 6,
                yAxisID: 'y1',
                barThickness: 18,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
            scales: {
                x: { grid: { display: false }, border: { display: false } },
                y: { position: 'left', grid: { color: '#eef0f5' }, border: { display: false }, ticks: { callback: v => (v/1000).toFixed(0) + 'k' } },
                y1: { position: 'right', grid: { display: false }, border: { display: false }, ticks: { precision: 0 } }
            }
        }
    });
})();

// Status donut chart
(function() {
    const ctx = document.getElementById('statusChart');
    if (!ctx || typeof Chart === 'undefined') return;
    const labels = <?= $statusLabelsJson ?>;
    const values = <?= $statusValuesJson ?>;
    const colors = <?= $statusColorsJson ?>;
    if (!labels.length) return;
    new Chart(ctx, {
        type: 'doughnut',
        data: { labels, datasets: [{ data: values, backgroundColor: colors, borderWidth: 0, hoverOffset: 6 }] },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '68%',
            plugins: {
                legend: { position: 'right', labels: { boxWidth: 10, boxHeight: 10, font: { size: 11.5 }, padding: 10 } },
                tooltip: { callbacks: { label: c => `${c.label}: ${c.parsed}` } }
            }
        }
    });
})();
</script>

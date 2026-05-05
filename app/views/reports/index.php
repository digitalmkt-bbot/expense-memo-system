<div class="page-title">
    <div>
        <h3>Reports</h3>
        <div class="meta">วิเคราะห์ค่าใช้จ่ายและติดตามสถานะ Memo</div>
    </div>
</div>

<div class="row g-3">
    <?php
    $reports = [
        ['by-company',       'bi-building',     'primary', 'Expense by Company',    'รวมค่าใช้จ่ายต่อบริษัท'],
        ['by-department',    'bi-diagram-3',    'info',    'Expense by Department', 'รวมค่าใช้จ่ายต่อแผนก'],
        ['by-category',      'bi-tags',         'success', 'Expense by Category',   'แยกตามหมวดค่าใช้จ่าย'],
        ['monthly',          'bi-calendar3',    'primary', 'Monthly Summary',       'สรุปยอดรายเดือน'],
        ['pending-approval', 'bi-hourglass',    'warning', 'Pending Approval',      'Memo ที่รออนุมัติ'],
        ['pending-payment',  'bi-clock-history','danger',  'Pending Payment',       'Memo ที่รอจ่ายเงิน'],
    ];
    foreach ($reports as $r): ?>
        <div class="col-md-6 col-lg-4">
            <a href="<?= url('/reports/' . $r[0]) ?>" class="emm-card" style="display: block; text-decoration: none; color: inherit; transition: all .15s ease;">
                <div class="emm-card-body">
                    <div class="d-flex align-items-start gap-3">
                        <div class="kpi-icon" style="width: 44px; height: 44px; border-radius: 12px;
                            display: grid; place-items: center; font-size: 18px; flex-shrink: 0;
                            background: var(--emm-<?= $r[2] ?>-50); color: var(--emm-<?= $r[2] ?>);">
                            <i class="bi <?= $r[1] ?>"></i>
                        </div>
                        <div>
                            <h5 style="margin: 0 0 4px; font-size: 15px;"><?= e($r[3]) ?></h5>
                            <small class="text-muted"><?= e($r[4]) ?></small>
                        </div>
                        <i class="bi bi-arrow-right text-soft" style="margin-left: auto;"></i>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-3">
    <?php foreach ([
        ['by-company',       'bi-building',     'Expense by Company',    'รวมค่าใช้จ่ายต่อบริษัท'],
        ['by-department',    'bi-diagram-3',    'Expense by Department', 'รวมค่าใช้จ่ายต่อแผนก'],
        ['by-category',      'bi-tags',         'Expense by Category',   'แยกตามหมวดค่าใช้จ่าย'],
        ['monthly',          'bi-calendar3',    'Monthly Summary',       'สรุปยอดรายเดือน'],
        ['pending-approval', 'bi-hourglass',    'Pending Approval',      'Memo ที่รออนุมัติ'],
        ['pending-payment',  'bi-clock-history','Pending Payment',       'Memo ที่รอจ่ายเงิน'],
    ] as $r): ?>
        <div class="col-md-4">
            <a href="<?= url('/reports/' . $r[0]) ?>" class="card h-100 text-decoration-none text-reset">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="display-6 me-3 text-primary"><i class="bi <?= $r[1] ?>"></i></div>
                        <div>
                            <h5 class="mb-1"><?= e($r[2]) ?></h5>
                            <small class="text-muted"><?= e($r[3]) ?></small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

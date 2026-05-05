<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>Memo <?= e($memo['memo_no'] ?? 'DRAFT') ?></title>
    <style>
        @page { size: A4; margin: 18mm 16mm; }
        body { font-family: "Sarabun", "TH SarabunPSK", Arial, sans-serif; font-size: 14px; color: #222; }
        .header { display: flex; justify-content: space-between; align-items: start; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header h3 { margin: 4px 0 0; color: #555; font-weight: 400; }
        .header .right { text-align: right; }
        .header .right h2 { margin: 0; }
        .meta { margin-top: 14px; }
        .meta table { width: 100%; border-collapse: collapse; }
        .meta td { padding: 4px 0; vertical-align: top; }
        .meta .label { color: #666; width: 22%; }
        .meta .value { font-weight: 600; width: 28%; }
        h4.section { margin: 18px 0 6px; padding-bottom: 4px; border-bottom: 1px solid #999; font-size: 15px; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 12px; }
        table.items th, table.items td { border: 1px solid #ccc; padding: 5px 6px; }
        table.items th { background: #eef0f5; }
        .text-end { text-align: right; }
        .summary { margin-top: 12px; width: 50%; margin-left: auto; }
        .summary td { padding: 4px 8px; }
        .summary .total { border-top: 2px solid #000; font-size: 16px; font-weight: 700; }
        .signatures { margin-top: 50px; display: flex; justify-content: space-between; }
        .sign { text-align: center; width: 30%; }
        .sign .line { border-top: 1px dotted #000; margin-bottom: 4px; padding-top: 50px; }
        .small { font-size: 11px; color: #666; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align:right; margin-bottom: 10px;">
    <button onclick="window.print()" style="padding: 6px 12px;">🖨 Print / Save as PDF</button>
</div>

<div class="header">
    <div>
        <h1><?= e($memo['company_name']) ?></h1>
        <h3><?= e($memo['department_name']) ?></h3>
    </div>
    <div class="right">
        <h2>EXPENSE MEMO</h2>
        <div><strong><?= e($memo['memo_no'] ?? 'DRAFT') ?></strong></div>
        <div class="small">Date: <?= format_date($memo['memo_date']) ?></div>
    </div>
</div>

<div class="meta">
    <table>
        <tr>
            <td class="label">Subject:</td>
            <td colspan="3" class="value"><?= e($memo['subject']) ?></td>
        </tr>
        <tr>
            <td class="label">Memo Type:</td>
            <td class="value"><?= e(memo_type_label($memo['memo_type'])) ?></td>
            <td class="label">Project:</td>
            <td class="value"><?= e($memo['project_code'] ?? '-') ?></td>
        </tr>
        <tr>
            <td class="label">Requester:</td>
            <td class="value"><?= e($memo['requester_name']) ?></td>
            <td class="label">Required Pay Date:</td>
            <td class="value"><?= format_date($memo['required_payment_date']) ?></td>
        </tr>
        <tr>
            <td class="label">Status:</td>
            <td class="value" colspan="3"><?= strtoupper(str_replace('_',' ',$memo['status'])) ?></td>
        </tr>
    </table>
</div>

<?php if ($memo['objective']): ?>
    <h4 class="section">Objective / วัตถุประสงค์</h4>
    <div><?= nl2br(e($memo['objective'])) ?></div>
<?php endif; ?>
<?php if ($memo['description']): ?>
    <h4 class="section">Description / รายละเอียด</h4>
    <div><?= nl2br(e($memo['description'])) ?></div>
<?php endif; ?>

<h4 class="section">Expense Items</h4>
<table class="items">
    <thead>
        <tr>
            <th>#</th><th>Date</th><th>Category</th><th>Item</th><th>Supplier</th>
            <th class="text-end">Qty</th><th class="text-end">Unit Price</th>
            <th class="text-end">Amount</th><th class="text-end">VAT</th><th class="text-end">WHT</th><th class="text-end">Net</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $idx => $i): ?>
        <tr>
            <td><?= $idx+1 ?></td>
            <td><?= format_date($i['expense_date']) ?></td>
            <td><?= e($i['category_name'] ?? '-') ?></td>
            <td><?= e($i['item_name']) ?>
                <?php if ($i['description']): ?><br><span class="small"><?= e($i['description']) ?></span><?php endif; ?>
            </td>
            <td><?= e($i['supplier_name'] ?? '-') ?></td>
            <td class="text-end"><?= format_money($i['quantity']) ?></td>
            <td class="text-end"><?= format_money($i['unit_price']) ?></td>
            <td class="text-end"><?= format_money($i['amount']) ?></td>
            <td class="text-end"><?= format_money($i['vat_amount']) ?></td>
            <td class="text-end"><?= format_money($i['wht_amount']) ?></td>
            <td class="text-end"><?= format_money($i['net_amount']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<table class="summary">
    <tr><td class="label">Total Amount:</td><td class="text-end"><?= format_money($memo['total_amount']) ?> THB</td></tr>
    <tr><td class="label">VAT:</td><td class="text-end">+ <?= format_money($memo['vat_amount']) ?> THB</td></tr>
    <tr><td class="label">WHT:</td><td class="text-end">- <?= format_money($memo['wht_amount']) ?> THB</td></tr>
    <tr class="total"><td>NET AMOUNT:</td><td class="text-end"><?= format_money($memo['net_amount']) ?> THB</td></tr>
</table>

<div class="signatures">
    <div class="sign">
        <div class="line"></div>
        <strong>Requester</strong><br>
        <span class="small"><?= e($memo['requester_name']) ?><br>Date: ____________</span>
    </div>
    <div class="sign">
        <div class="line"></div>
        <strong>Manager / Approver</strong><br>
        <span class="small">Date: ____________</span>
    </div>
    <div class="sign">
        <div class="line"></div>
        <strong>Director</strong><br>
        <span class="small">Date: ____________</span>
    </div>
</div>

<?php if ($logs): ?>
<h4 class="section" style="margin-top: 30px;">Approval History</h4>
<table class="items">
    <thead><tr><th>Date</th><th>Action</th><th>By</th><th>Role</th><th>Comment</th></tr></thead>
    <tbody>
    <?php foreach ($logs as $l): ?>
        <tr>
            <td><?= format_datetime($l['action_at']) ?></td>
            <td><?= strtoupper(str_replace('_',' ',$l['action'])) ?></td>
            <td><?= e($l['approver_name']) ?></td>
            <td><?= e($l['approver_role']) ?></td>
            <td><?= e($l['comment']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

</body>
</html>

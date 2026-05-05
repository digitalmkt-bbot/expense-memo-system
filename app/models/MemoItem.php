<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class MemoItem extends Model
{
    protected static string $table = 'memo_items';

    public static function byMemo(int $memoId): array
    {
        return Database::select(
            "SELECT i.*, c.category_name, c.category_code, s.supplier_name
             FROM memo_items i
             LEFT JOIN expense_categories c ON i.category_id = c.id
             LEFT JOIN suppliers          s ON i.supplier_id = s.id
             WHERE i.memo_id = ?
             ORDER BY i.id ASC",
            [$memoId]
        );
    }

    /**
     * คำนวณ amount/net_amount/vat/wht อัตโนมัติ
     */
    public static function calculate(array $data): array
    {
        $qty       = (float) ($data['quantity']    ?? 1);
        $unitPrice = (float) ($data['unit_price']  ?? 0);
        $vatType   = $data['vat_type'] ?? 'none';
        $vatRate   = (float) ($data['vat_rate']    ?? 0);
        $whtType   = $data['wht_type'] ?? 'none';
        $whtRate   = (float) ($data['wht_rate']    ?? 0);

        $amount = round($qty * $unitPrice, 2);

        $vatAmount = 0.0;
        if ($vatType === 'exclude_vat') {
            $vatAmount = round($amount * ($vatRate / 100), 2);
        } elseif ($vatType === 'include_vat') {
            // amount มี VAT รวมแล้ว → แยกออก
            $vatAmount = round($amount - ($amount / (1 + $vatRate / 100)), 2);
        }

        $whtAmount = 0.0;
        if ($whtType !== 'none' && $whtRate > 0) {
            $base = ($vatType === 'include_vat') ? $amount - $vatAmount : $amount;
            $whtAmount = round($base * ($whtRate / 100), 2);
        }

        // Net = amount + (VAT ถ้า exclude) - WHT
        $net = $amount;
        if ($vatType === 'exclude_vat') $net += $vatAmount;
        $net -= $whtAmount;

        $data['amount']     = $amount;
        $data['vat_amount'] = $vatAmount;
        $data['wht_amount'] = $whtAmount;
        $data['net_amount'] = round($net, 2);
        $data['vat_rate']   = $vatRate;
        $data['wht_rate']   = $whtRate;
        return $data;
    }
}

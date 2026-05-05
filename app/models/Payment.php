<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Payment extends Model
{
    protected static string $table = 'payments';

    public static function byMemo(int $memoId): array
    {
        return Database::select(
            "SELECT p.*, u.full_name AS paid_by_name
             FROM payments p
             LEFT JOIN users u ON p.paid_by = u.id
             WHERE p.memo_id = ?
             ORDER BY p.payment_date DESC, p.id DESC",
            [$memoId]
        );
    }

    public static function totalPaid(int $memoId): float
    {
        $row = Database::selectOne(
            "SELECT COALESCE(SUM(paid_amount),0) AS total FROM payments WHERE memo_id = ?",
            [$memoId]
        );
        return (float) ($row['total'] ?? 0);
    }
}

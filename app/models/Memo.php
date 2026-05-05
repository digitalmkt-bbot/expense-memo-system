<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Memo extends Model
{
    protected static string $table = 'memos';

    /**
     * Memo with joined company/department/requester
     */
    public static function findFull(int $id): ?array
    {
        return Database::selectOne(
            "SELECT m.*,
                    c.company_code, c.company_name,
                    d.department_code, d.department_name,
                    p.project_code, p.project_name,
                    u.full_name AS requester_name, u.email AS requester_email
             FROM memos m
             LEFT JOIN companies   c ON m.company_id    = c.id
             LEFT JOIN departments d ON m.department_id = d.id
             LEFT JOIN projects    p ON m.project_id    = p.id
             LEFT JOIN users       u ON m.requester_id  = u.id
             WHERE m.id = ?",
            [$id]
        );
    }

    /**
     * รายการ Memo สำหรับ List Page โดยรองรับ Filter
     */
    public static function search(array $f = []): array
    {
        $sql = "SELECT m.*, c.company_code, d.department_code, u.full_name AS requester_name
                FROM memos m
                LEFT JOIN companies   c ON m.company_id    = c.id
                LEFT JOIN departments d ON m.department_id = d.id
                LEFT JOIN users       u ON m.requester_id  = u.id
                WHERE 1=1";
        $params = [];
        if (!empty($f['requester_id']))    { $sql .= " AND m.requester_id = ?";   $params[] = $f['requester_id']; }
        if (!empty($f['company_id']))      { $sql .= " AND m.company_id = ?";     $params[] = $f['company_id']; }
        if (!empty($f['department_id']))   { $sql .= " AND m.department_id = ?";  $params[] = $f['department_id']; }
        if (!empty($f['status']))          { $sql .= " AND m.status = ?";         $params[] = $f['status']; }
        if (!empty($f['payment_status']))  { $sql .= " AND m.payment_status = ?"; $params[] = $f['payment_status']; }
        if (!empty($f['date_from']))       { $sql .= " AND m.memo_date >= ?";     $params[] = $f['date_from']; }
        if (!empty($f['date_to']))         { $sql .= " AND m.memo_date <= ?";     $params[] = $f['date_to']; }
        if (!empty($f['memo_type']))       { $sql .= " AND m.memo_type = ?";      $params[] = $f['memo_type']; }
        if (!empty($f['keyword'])) {
            $sql .= " AND (m.memo_no LIKE ? OR m.subject LIKE ?)";
            $params[] = '%' . $f['keyword'] . '%';
            $params[] = '%' . $f['keyword'] . '%';
        }
        $sql .= " ORDER BY m.created_at DESC";
        return Database::select($sql, $params);
    }

    /**
     * Recompute totals จาก memo_items
     */
    public static function recomputeTotals(int $memoId): void
    {
        $row = Database::selectOne(
            "SELECT
                COALESCE(SUM(amount),0)     AS total,
                COALESCE(SUM(vat_amount),0) AS vat,
                COALESCE(SUM(wht_amount),0) AS wht,
                COALESCE(SUM(net_amount),0) AS net
             FROM memo_items WHERE memo_id = ?",
            [$memoId]
        );
        Database::execute(
            "UPDATE memos
             SET total_amount = ?, vat_amount = ?, wht_amount = ?, net_amount = ?
             WHERE id = ?",
            [$row['total'], $row['vat'], $row['wht'], $row['net'], $memoId]
        );
    }

    /**
     * Status Counts (สำหรับ Dashboard)
     */
    public static function statusCounts(?int $userId = null): array
    {
        $sql = "SELECT status, COUNT(*) AS c FROM memos";
        $params = [];
        if ($userId) { $sql .= " WHERE requester_id = ?"; $params[] = $userId; }
        $sql .= " GROUP BY status";
        $rows = Database::select($sql, $params);
        $out = [];
        foreach ($rows as $r) $out[$r['status']] = (int) $r['c'];
        return $out;
    }
}

<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class ApprovalRule extends Model
{
    protected static string $table = 'approval_rules';

    /**
     * คืน list approver_role ที่ต้องผ่านสำหรับ amount นี้ (เรียงตาม approval_level)
     */
    public static function rulesFor(int $companyId, float $amount): array
    {
        return Database::select(
            "SELECT * FROM approval_rules
             WHERE company_id = ? AND is_active = 1
               AND ? BETWEEN min_amount AND COALESCE(max_amount, 99999999.99)
             ORDER BY approval_level ASC",
            [$companyId, $amount]
        );
    }

    /**
     * ตรวจว่า amount นี้ต้องผ่าน Director หรือไม่
     */
    public static function requiresDirector(int $companyId, float $amount): bool
    {
        $rules = self::rulesFor($companyId, $amount);
        foreach ($rules as $r) {
            if ($r['approver_role'] === 'director') return true;
        }
        return false;
    }
}

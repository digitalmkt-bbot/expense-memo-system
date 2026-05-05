<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

/**
 * Running Number Generator
 *  Format: [DEPARTMENT_CODE]-[YYYY]-[MM]-[RUNNING 3 DIGITS]
 *  - แยกต่อ Company + Department + Year + Month
 *  - เปลี่ยนเดือน → reset = 001
 */
class RunningNumber extends Model
{
    protected static string $table = 'memo_running_numbers';

    /**
     * Allocate (เพิ่ม +1 และ Return memo_no)
     * ใช้ใน Transaction เพื่อกัน Race-condition
     */
    public static function generate(int $companyId, int $departmentId, string $deptCode, string $memoDate): string
    {
        $year  = (int) date('Y', strtotime($memoDate));
        $month = (int) date('m', strtotime($memoDate));

        $existing = Database::selectOne(
            "SELECT * FROM memo_running_numbers
             WHERE department_id = ? AND year = ? AND month = ? FOR UPDATE",
            [$departmentId, $year, $month]
        );

        if (!$existing) {
            Database::insert(
                "INSERT INTO memo_running_numbers
                 (company_id, department_id, department_code, year, month, current_number)
                 VALUES (?, ?, ?, ?, ?, 1)",
                [$companyId, $departmentId, $deptCode, $year, $month]
            );
            $running = 1;
        } else {
            $running = (int) $existing['current_number'] + 1;
            Database::execute(
                "UPDATE memo_running_numbers SET current_number = ?
                 WHERE id = ?",
                [$running, $existing['id']]
            );
        }

        return sprintf('%s-%04d-%02d-%03d', $deptCode, $year, $month, $running);
    }
}

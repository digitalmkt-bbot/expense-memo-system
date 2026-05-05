<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Department extends Model
{
    protected static string $table = 'departments';

    public static function byCompany(int $companyId): array
    {
        return Database::select(
            "SELECT * FROM departments WHERE company_id = ? AND is_active = 1 ORDER BY department_code",
            [$companyId]
        );
    }

    public static function code(int $departmentId): ?string
    {
        $row = Database::selectOne("SELECT department_code FROM departments WHERE id = ?", [$departmentId]);
        return $row['department_code'] ?? null;
    }
}

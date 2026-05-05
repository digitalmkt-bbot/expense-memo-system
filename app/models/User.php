<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class User extends Model
{
    protected static string $table = 'users';

    public static function findByEmail(string $email): ?array
    {
        return Database::selectOne("SELECT * FROM users WHERE email = ? AND is_active = 1 LIMIT 1", [$email]);
    }

    public static function withDeptCompany(int $id): ?array
    {
        return Database::selectOne(
            "SELECT u.*, c.company_code, c.company_name, d.department_code, d.department_name
             FROM users u
             LEFT JOIN companies   c ON u.company_id    = c.id
             LEFT JOIN departments d ON u.department_id = d.id
             WHERE u.id = ?", [$id]
        );
    }

    public static function listAll(): array
    {
        return Database::select(
            "SELECT u.*, c.company_code, d.department_code
             FROM users u
             LEFT JOIN companies   c ON u.company_id    = c.id
             LEFT JOIN departments d ON u.department_id = d.id
             ORDER BY u.full_name"
        );
    }

    public static function byRole(string $role, ?int $companyId = null, ?int $departmentId = null): array
    {
        $sql = "SELECT * FROM users WHERE role = ? AND is_active = 1";
        $params = [$role];
        if ($companyId)    { $sql .= " AND company_id = ?";    $params[] = $companyId; }
        if ($departmentId) { $sql .= " AND department_id = ?"; $params[] = $departmentId; }
        return Database::select($sql, $params);
    }
}

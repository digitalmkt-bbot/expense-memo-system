<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Project extends Model
{
    protected static string $table = 'projects';

    public static function active(?int $companyId = null): array
    {
        $sql = "SELECT * FROM projects WHERE status = 'active'";
        $params = [];
        if ($companyId) { $sql .= " AND (company_id = ? OR company_id IS NULL)"; $params[] = $companyId; }
        $sql .= " ORDER BY project_name";
        return Database::select($sql, $params);
    }
}

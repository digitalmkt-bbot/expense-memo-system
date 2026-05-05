<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Company extends Model
{
    protected static string $table = 'companies';

    public static function active(): array
    {
        return Database::select("SELECT * FROM companies WHERE is_active = 1 ORDER BY company_code");
    }
}

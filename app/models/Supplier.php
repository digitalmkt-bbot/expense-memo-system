<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Supplier extends Model
{
    protected static string $table = 'suppliers';

    public static function active(): array
    {
        return Database::select("SELECT * FROM suppliers WHERE is_active = 1 ORDER BY supplier_name");
    }
}

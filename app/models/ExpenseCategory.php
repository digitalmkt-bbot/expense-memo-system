<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class ExpenseCategory extends Model
{
    protected static string $table = 'expense_categories';

    public static function active(): array
    {
        return Database::select("SELECT * FROM expense_categories WHERE is_active = 1 ORDER BY category_name");
    }
}

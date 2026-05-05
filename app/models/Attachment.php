<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Attachment extends Model
{
    protected static string $table = 'attachments';

    public static function forMemo(int $memoId): array
    {
        return Database::select(
            "SELECT * FROM attachments WHERE related_type = 'memo' AND related_id = ? ORDER BY uploaded_at DESC",
            [$memoId]
        );
    }
}

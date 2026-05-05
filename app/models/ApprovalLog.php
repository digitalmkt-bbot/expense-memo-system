<?php
namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class ApprovalLog extends Model
{
    protected static string $table = 'approval_logs';

    public static function add(int $memoId, int $level, int $approverId, string $role, string $action, ?string $comment = null): void
    {
        Database::insert(
            "INSERT INTO approval_logs (memo_id, approval_level, approver_id, approver_role, action, comment)
             VALUES (?, ?, ?, ?, ?, ?)",
            [$memoId, $level, $approverId, $role, $action, $comment]
        );
    }

    public static function byMemo(int $memoId): array
    {
        return Database::select(
            "SELECT l.*, u.full_name AS approver_name
             FROM approval_logs l
             LEFT JOIN users u ON l.approver_id = u.id
             WHERE l.memo_id = ?
             ORDER BY l.action_at ASC",
            [$memoId]
        );
    }
}

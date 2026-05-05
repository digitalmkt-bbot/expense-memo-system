<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;
use App\Models\Memo;
use App\Models\ApprovalLog;
use App\Models\ApprovalRule;

class ApprovalController extends Controller
{
    public function index(): void
    {
        Auth::requireRole(['manager','accounting','director','admin']);
        $u = Auth::user();

        // เลือก memo ที่ตรงกับ stage ของ role ปัจจุบัน
        $whereByRole = match ($u['role']) {
            'manager'    => "m.status = 'submitted'",
            'accounting' => "m.status = 'manager_approved'",
            'director'   => "m.status = 'accounting_checked'",
            default      => "m.status IN ('submitted','manager_approved','accounting_checked')",
        };

        // Manager อนุมัติเฉพาะของแผนกตัวเอง
        $params = [];
        if ($u['role'] === 'manager') {
            $whereByRole .= " AND m.department_id = ?";
            $params[] = $u['department_id'];
        }

        $memos = Database::select(
            "SELECT m.*, c.company_code, d.department_code, u.full_name AS requester_name
             FROM memos m
             LEFT JOIN companies   c ON m.company_id    = c.id
             LEFT JOIN departments d ON m.department_id = d.id
             LEFT JOIN users       u ON m.requester_id  = u.id
             WHERE $whereByRole
             ORDER BY m.submitted_at ASC",
            $params
        );

        $this->view('approvals/index', [
            'pageTitle' => 'Pending Approval (' . count($memos) . ')',
            'memos'     => $memos,
            'role'      => $u['role'],
        ]);
    }

    public function approve(int $id): void
    {
        Auth::requireRole(['manager','accounting','director','admin']);
        if (!verify_csrf()) $this->abort(419);

        $memo = Memo::find($id);
        if (!$memo) $this->abort(404);

        $u = Auth::user();
        $comment = $this->input('comment', '');

        $expectedStatus = $this->expectedStatusForRole($u['role']);
        if (!in_array($memo['status'], $expectedStatus, true)) {
            flash('error', 'สถานะ Memo ไม่ตรงกับขั้นที่คุณอนุมัติได้ ('. $memo['status'] .')');
            $this->redirect('/memos/' . $id);
        }

        // กำหนด next status
        $next = match ($memo['status']) {
            'submitted'          => 'manager_approved',
            'manager_approved'   => 'accounting_checked',
            'accounting_checked' => 'director_approved',
            default              => 'approved',
        };

        // หาก amount ไม่ต้องผ่าน Director ให้ขยับเป็น approved/pending_payment เลย
        $needDirector = ApprovalRule::requiresDirector((int) $memo['company_id'], (float) $memo['net_amount']);
        if ($next === 'accounting_checked' && !$needDirector) {
            $next = 'approved';
        }
        if ($next === 'director_approved') $next = 'approved';

        $update = ['status' => $next];
        if ($next === 'approved') {
            $update['payment_status'] = 'pending_payment';
            $update['approved_at']    = date('Y-m-d H:i:s');
        }

        Memo::update($id, $update);
        ApprovalLog::add($id, 0, $u['id'], $u['role'], 'approved', $comment);

        flash('success', 'Approved → สถานะ: ' . strtoupper(str_replace('_',' ',$next)));
        $this->redirect('/approvals');
    }

    public function reject(int $id): void
    {
        Auth::requireRole(['manager','accounting','director','admin']);
        if (!verify_csrf()) $this->abort(419);
        $memo = Memo::find($id);
        if (!$memo) $this->abort(404);

        Memo::update($id, [
            'status'      => 'rejected',
            'rejected_at' => date('Y-m-d H:i:s'),
        ]);
        ApprovalLog::add($id, 0, Auth::id(), Auth::role(), 'rejected', $this->input('comment'));
        flash('success', 'Memo ถูก Reject แล้ว');
        $this->redirect('/approvals');
    }

    public function revision(int $id): void
    {
        Auth::requireRole(['manager','accounting','director','admin']);
        if (!verify_csrf()) $this->abort(419);
        $memo = Memo::find($id);
        if (!$memo) $this->abort(404);

        Memo::update($id, ['status' => 'revision_required']);
        ApprovalLog::add($id, 0, Auth::id(), Auth::role(), 'revision_required', $this->input('comment'));
        flash('success', 'ส่งกลับให้ Requester แก้ไขแล้ว');
        $this->redirect('/approvals');
    }

    private function expectedStatusForRole(string $role): array
    {
        return match ($role) {
            'manager'    => ['submitted'],
            'accounting' => ['manager_approved'],
            'director'   => ['accounting_checked'],
            'admin'      => ['submitted','manager_approved','accounting_checked'],
            default      => [],
        };
    }
}

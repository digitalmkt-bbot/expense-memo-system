<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;

class DashboardController extends Controller
{
    public function index(): void
    {
        Auth::require();
        $u = Auth::user();

        // KPIs
        $isStaff = in_array($u['role'], ['requester'], true);

        $where = $isStaff ? "WHERE m.requester_id = ?" : "";
        $params = $isStaff ? [$u['id']] : [];

        $kpi = [
            'total'     => Database::selectOne("SELECT COUNT(*) c FROM memos m $where", $params)['c'],
            'submitted' => Database::selectOne("SELECT COUNT(*) c FROM memos m $where " . ($where ? 'AND' : 'WHERE') . " status='submitted'", $params)['c'],
            'pending_pay' => Database::selectOne("SELECT COUNT(*) c FROM memos m $where " . ($where ? 'AND' : 'WHERE') . " status='approved' AND payment_status='pending_payment'", $params)['c'],
            'paid'      => Database::selectOne("SELECT COUNT(*) c FROM memos m $where " . ($where ? 'AND' : 'WHERE') . " payment_status='paid'", $params)['c'],
            'rejected'  => Database::selectOne("SELECT COUNT(*) c FROM memos m $where " . ($where ? 'AND' : 'WHERE') . " status='rejected'", $params)['c'],
        ];

        // Total amount this month
        $monthSql = "SELECT COALESCE(SUM(net_amount),0) total FROM memos m
                     WHERE YEAR(memo_date)=YEAR(CURDATE()) AND MONTH(memo_date)=MONTH(CURDATE())";
        if ($isStaff) {
            $monthSql .= " AND requester_id = ?";
            $kpi['this_month'] = Database::selectOne($monthSql, [$u['id']])['total'];
        } else {
            $kpi['this_month'] = Database::selectOne($monthSql)['total'];
        }

        // Recent memos
        $recentSql = "SELECT m.*, c.company_code, d.department_code, u.full_name AS requester_name
                      FROM memos m
                      LEFT JOIN companies   c ON m.company_id    = c.id
                      LEFT JOIN departments d ON m.department_id = d.id
                      LEFT JOIN users       u ON m.requester_id  = u.id";
        if ($isStaff) $recentSql .= " WHERE m.requester_id = ?";
        $recentSql .= " ORDER BY m.created_at DESC LIMIT 8";
        $recent = Database::select($recentSql, $isStaff ? [$u['id']] : []);

        $this->view('dashboard/index', [
            'pageTitle' => 'Dashboard',
            'kpi'       => $kpi,
            'recent'    => $recent,
        ]);
    }
}

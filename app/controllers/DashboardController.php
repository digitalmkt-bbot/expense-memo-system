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

        $isStaff = $u['role'] === 'requester';
        $userFilter = $isStaff ? "AND m.requester_id = " . (int) $u['id'] : "";

        // ─── KPIs ───
        $kpi = [
            'total'       => (int) Database::selectOne("SELECT COUNT(*) c FROM memos m WHERE 1=1 $userFilter")['c'],
            'submitted'   => (int) Database::selectOne("SELECT COUNT(*) c FROM memos m WHERE status='submitted' $userFilter")['c'],
            'pending_pay' => (int) Database::selectOne("SELECT COUNT(*) c FROM memos m WHERE status='approved' AND payment_status='pending_payment' $userFilter")['c'],
            'paid'        => (int) Database::selectOne("SELECT COUNT(*) c FROM memos m WHERE payment_status='paid' $userFilter")['c'],
            'rejected'    => (int) Database::selectOne("SELECT COUNT(*) c FROM memos m WHERE status='rejected' $userFilter")['c'],
        ];

        $thisMonth = Database::selectOne(
            "SELECT COALESCE(SUM(net_amount),0) total FROM memos m
             WHERE YEAR(memo_date)=YEAR(CURDATE()) AND MONTH(memo_date)=MONTH(CURDATE()) $userFilter"
        );
        $kpi['this_month'] = (float) $thisMonth['total'];

        $lastMonth = Database::selectOne(
            "SELECT COALESCE(SUM(net_amount),0) total FROM memos m
             WHERE memo_date >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
             AND memo_date < DATE_SUB(CURDATE(), INTERVAL 1 MONTH) $userFilter"
        );
        $kpi['last_month']    = (float) $lastMonth['total'];
        $kpi['month_change']  = $kpi['last_month'] > 0
            ? round((($kpi['this_month'] - $kpi['last_month']) / $kpi['last_month']) * 100, 1)
            : null;

        // ─── Total approved spend YTD ───
        $ytd = Database::selectOne(
            "SELECT COALESCE(SUM(net_amount),0) total FROM memos m
             WHERE YEAR(memo_date)=YEAR(CURDATE()) AND status IN ('approved','paid','closed','partially_paid') $userFilter"
        );
        $kpi['ytd'] = (float) $ytd['total'];

        // ─── Monthly trend (last 6 months) ───
        $trendRows = Database::select(
            "SELECT DATE_FORMAT(memo_date,'%Y-%m') AS ym,
                    COUNT(*) AS cnt,
                    COALESCE(SUM(net_amount),0) AS amount
             FROM memos m
             WHERE memo_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) $userFilter
             GROUP BY ym ORDER BY ym ASC"
        );

        // Build last 6-month series with zero-fill
        $trend = [];
        for ($i = 5; $i >= 0; $i--) {
            $key = date('Y-m', strtotime("-$i month"));
            $trend[$key] = ['label' => date('M', strtotime($key . '-01')), 'cnt' => 0, 'amount' => 0];
        }
        foreach ($trendRows as $r) {
            if (isset($trend[$r['ym']])) {
                $trend[$r['ym']]['cnt']    = (int) $r['cnt'];
                $trend[$r['ym']]['amount'] = (float) $r['amount'];
            }
        }

        // ─── Status distribution (donut) ───
        $statusDist = Database::select(
            "SELECT status, COUNT(*) AS cnt FROM memos m WHERE 1=1 $userFilter GROUP BY status"
        );

        // ─── Top categories (this year) ───
        $topCategories = Database::select(
            "SELECT c.category_name, c.category_code,
                    COALESCE(SUM(i.net_amount),0) AS amount,
                    COUNT(DISTINCT i.id) AS items
             FROM memo_items i
             LEFT JOIN expense_categories c ON i.category_id = c.id
             LEFT JOIN memos m ON i.memo_id = m.id
             WHERE c.id IS NOT NULL AND YEAR(m.memo_date) = YEAR(CURDATE()) $userFilter
             GROUP BY c.id, c.category_name, c.category_code
             ORDER BY amount DESC
             LIMIT 6"
        );

        // ─── Recent memos ───
        $recentSql = "SELECT m.*, c.company_code, d.department_code, u.full_name AS requester_name
                      FROM memos m
                      LEFT JOIN companies   c ON m.company_id    = c.id
                      LEFT JOIN departments d ON m.department_id = d.id
                      LEFT JOIN users       u ON m.requester_id  = u.id
                      WHERE 1=1 $userFilter
                      ORDER BY m.created_at DESC LIMIT 6";
        $recent = Database::select($recentSql);

        // ─── Recent activity (approval logs) ───
        $activity = Database::select(
            "SELECT al.*, m.memo_no, m.subject, u.full_name AS approver_name
             FROM approval_logs al
             LEFT JOIN memos m ON al.memo_id = m.id
             LEFT JOIN users u ON al.approver_id = u.id
             ORDER BY al.action_at DESC LIMIT 8"
        );

        $this->view('dashboard/index', [
            'pageTitle'     => 'Dashboard',
            'kpi'           => $kpi,
            'recent'        => $recent,
            'trend'         => array_values($trend),
            'statusDist'    => $statusDist,
            'topCategories' => $topCategories,
            'activity'      => $activity,
        ]);
    }
}

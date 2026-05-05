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

        // ─── Period selector ───
        $period = $_GET['period'] ?? 'month';
        $from   = $_GET['from']   ?? null;
        $to     = $_GET['to']     ?? null;

        $range = $this->resolveRange($period, $from, $to);
        // $range = ['from'=>YYYY-MM-DD, 'to'=>YYYY-MM-DD, 'label'=>..., 'trend'=>'monthly|daily|weekly', 'trend_count'=>N]

        $rangeFilter = "AND memo_date BETWEEN '{$range['from']}' AND '{$range['to']}'";

        // ─── KPIs ───
        $kpi = [
            'total'       => (int) Database::selectOne("SELECT COUNT(*) c FROM memos m WHERE 1=1 $userFilter $rangeFilter")['c'],
            'submitted'   => (int) Database::selectOne("SELECT COUNT(*) c FROM memos m WHERE status='submitted' $userFilter $rangeFilter")['c'],
            'pending_pay' => (int) Database::selectOne("SELECT COUNT(*) c FROM memos m WHERE status='approved' AND payment_status='pending_payment' $userFilter $rangeFilter")['c'],
            'paid'        => (int) Database::selectOne("SELECT COUNT(*) c FROM memos m WHERE payment_status='paid' $userFilter $rangeFilter")['c'],
            'rejected'    => (int) Database::selectOne("SELECT COUNT(*) c FROM memos m WHERE status='rejected' $userFilter $rangeFilter")['c'],
        ];

        // Spend within selected range
        $spendRow = Database::selectOne(
            "SELECT COALESCE(SUM(net_amount),0) total FROM memos m WHERE 1=1 $userFilter $rangeFilter"
        );
        $kpi['period_total'] = (float) $spendRow['total'];

        // Previous period for trend %
        $prevSpendRow = Database::selectOne(
            "SELECT COALESCE(SUM(net_amount),0) total FROM memos m
             WHERE memo_date BETWEEN '{$range['prev_from']}' AND '{$range['prev_to']}' $userFilter"
        );
        $kpi['prev_total']    = (float) $prevSpendRow['total'];
        $kpi['period_change'] = $kpi['prev_total'] > 0
            ? round((($kpi['period_total'] - $kpi['prev_total']) / $kpi['prev_total']) * 100, 1)
            : null;

        // ─── Total approved spend YTD (always year-to-date regardless of filter) ───
        $ytd = Database::selectOne(
            "SELECT COALESCE(SUM(net_amount),0) total FROM memos m
             WHERE YEAR(memo_date)=YEAR(CURDATE()) AND status IN ('approved','paid','closed','partially_paid') $userFilter"
        );
        $kpi['ytd'] = (float) $ytd['total'];

        // ─── Trend ───
        $trend = $this->buildTrend($range, $userFilter);

        // ─── Status distribution (within range) ───
        $statusDist = Database::select(
            "SELECT status, COUNT(*) AS cnt FROM memos m WHERE 1=1 $userFilter $rangeFilter GROUP BY status"
        );

        // ─── Top categories (within range) ───
        $topCategories = Database::select(
            "SELECT c.category_name, c.category_code,
                    COALESCE(SUM(i.net_amount),0) AS amount,
                    COUNT(DISTINCT i.id) AS items
             FROM memo_items i
             LEFT JOIN expense_categories c ON i.category_id = c.id
             LEFT JOIN memos m ON i.memo_id = m.id
             WHERE c.id IS NOT NULL AND m.memo_date BETWEEN '{$range['from']}' AND '{$range['to']}' $userFilter
             GROUP BY c.id, c.category_name, c.category_code
             ORDER BY amount DESC LIMIT 6"
        );

        // ─── Recent memos (always latest 6, regardless of filter) ───
        $recentSql = "SELECT m.*, c.company_code, d.department_code, u.full_name AS requester_name
                      FROM memos m
                      LEFT JOIN companies   c ON m.company_id    = c.id
                      LEFT JOIN departments d ON m.department_id = d.id
                      LEFT JOIN users       u ON m.requester_id  = u.id
                      WHERE 1=1 $userFilter
                      ORDER BY m.created_at DESC LIMIT 6";
        $recent = Database::select($recentSql);

        // ─── Recent activity (always latest 8) ───
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
            'trend'         => $trend,
            'statusDist'    => $statusDist,
            'topCategories' => $topCategories,
            'activity'      => $activity,
            'period'        => $period,
            'range'         => $range,
        ]);
    }

    /**
     * Resolve period preset → {from, to, prev_from, prev_to, trend granularity}
     */
    private function resolveRange(string $period, ?string $from, ?string $to): array
    {
        $today = date('Y-m-d');

        if ($period === 'custom' && $from && $to) {
            $diff = max(1, (int) ((strtotime($to) - strtotime($from)) / 86400));
            $prevFrom = date('Y-m-d', strtotime($from . " -{$diff} days"));
            $prevTo   = date('Y-m-d', strtotime($from . " -1 days"));
            return [
                'from' => $from, 'to' => $to,
                'prev_from' => $prevFrom, 'prev_to' => $prevTo,
                'label' => date('d M Y', strtotime($from)) . ' – ' . date('d M Y', strtotime($to)),
                'trend' => $diff <= 31 ? 'daily' : ($diff <= 120 ? 'weekly' : 'monthly'),
                'trend_count' => $diff <= 31 ? min(31, $diff+1) : ($diff <= 120 ? 12 : 12),
            ];
        }

        switch ($period) {
            case 'week':
                $f = date('Y-m-d', strtotime('-6 days'));
                return [
                    'from' => $f, 'to' => $today,
                    'prev_from' => date('Y-m-d', strtotime('-13 days')),
                    'prev_to'   => date('Y-m-d', strtotime('-7 days')),
                    'label' => 'Last 7 days',
                    'trend' => 'daily', 'trend_count' => 7,
                ];

            case 'quarter':
                $f = date('Y-m-d', strtotime('first day of -2 months'));
                return [
                    'from' => $f, 'to' => $today,
                    'prev_from' => date('Y-m-d', strtotime('first day of -5 months')),
                    'prev_to'   => date('Y-m-d', strtotime('last day of -3 months')),
                    'label' => 'Last 3 months',
                    'trend' => 'weekly', 'trend_count' => 12,
                ];

            case 'year':
                $f = date('Y-01-01');
                return [
                    'from' => $f, 'to' => $today,
                    'prev_from' => date('Y-01-01', strtotime('-1 year')),
                    'prev_to'   => date('Y-12-31', strtotime('-1 year')),
                    'label' => 'Year ' . date('Y'),
                    'trend' => 'monthly', 'trend_count' => 12,
                ];

            case 'month':
            default:
                $f = date('Y-m-01');
                return [
                    'from' => $f, 'to' => $today,
                    'prev_from' => date('Y-m-01', strtotime('-1 month')),
                    'prev_to'   => date('Y-m-d', strtotime(date('Y-m-01') . ' -1 day')),
                    'label' => date('M Y'),
                    'trend' => 'monthly', 'trend_count' => 6,
                ];
        }
    }

    /**
     * Build trend series with zero-fill based on granularity
     */
    private function buildTrend(array $range, string $userFilter): array
    {
        $count = $range['trend_count'];
        $granularity = $range['trend'];

        // Build query + zero-fill index
        if ($granularity === 'daily') {
            $rows = Database::select(
                "SELECT DATE_FORMAT(memo_date, '%Y-%m-%d') AS k,
                        COUNT(*) AS cnt, COALESCE(SUM(net_amount),0) AS amount
                 FROM memos m
                 WHERE memo_date >= DATE_SUB(CURDATE(), INTERVAL " . ($count - 1) . " DAY) $userFilter
                 GROUP BY k ORDER BY k ASC"
            );
            $idx = [];
            for ($i = $count - 1; $i >= 0; $i--) {
                $key = date('Y-m-d', strtotime("-$i days"));
                $idx[$key] = ['label' => date('d M', strtotime($key)), 'cnt' => 0, 'amount' => 0];
            }
        } elseif ($granularity === 'weekly') {
            $rows = Database::select(
                "SELECT DATE_FORMAT(memo_date, '%x-W%v') AS k,
                        COUNT(*) AS cnt, COALESCE(SUM(net_amount),0) AS amount,
                        MIN(memo_date) AS first_date
                 FROM memos m
                 WHERE memo_date >= DATE_SUB(CURDATE(), INTERVAL " . ($count * 7) . " DAY) $userFilter
                 GROUP BY k ORDER BY first_date ASC"
            );
            $idx = [];
            for ($i = $count - 1; $i >= 0; $i--) {
                $weekStart = strtotime("-$i weeks Monday");
                $key = date('o-\WW', $weekStart);
                $idx[$key] = ['label' => 'W' . date('W', $weekStart), 'cnt' => 0, 'amount' => 0];
            }
        } else {
            // monthly
            $rows = Database::select(
                "SELECT DATE_FORMAT(memo_date, '%Y-%m') AS k,
                        COUNT(*) AS cnt, COALESCE(SUM(net_amount),0) AS amount
                 FROM memos m
                 WHERE memo_date >= DATE_SUB(CURDATE(), INTERVAL " . ($count - 1) . " MONTH) $userFilter
                 GROUP BY k ORDER BY k ASC"
            );
            $idx = [];
            for ($i = $count - 1; $i >= 0; $i--) {
                $key = date('Y-m', strtotime("-$i month"));
                $idx[$key] = ['label' => date('M', strtotime($key . '-01')), 'cnt' => 0, 'amount' => 0];
            }
        }

        foreach ($rows as $r) {
            if (isset($idx[$r['k']])) {
                $idx[$r['k']]['cnt']    = (int) $r['cnt'];
                $idx[$r['k']]['amount'] = (float) $r['amount'];
            }
        }

        return array_values($idx);
    }
}

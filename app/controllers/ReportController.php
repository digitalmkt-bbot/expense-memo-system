<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;

class ReportController extends Controller
{
    public function index(): void
    {
        Auth::require();
        $this->view('reports/index', ['pageTitle' => 'Reports']);
    }

    public function byCompany(): void
    {
        Auth::require();
        $rows = Database::select(
            "SELECT c.company_code, c.company_name,
                    COUNT(m.id) AS memo_count,
                    COALESCE(SUM(m.total_amount),0) AS total,
                    COALESCE(SUM(m.net_amount),0)   AS net
             FROM companies c
             LEFT JOIN memos m ON m.company_id = c.id
             GROUP BY c.id ORDER BY net DESC"
        );
        $this->view('reports/by_company', [
            'pageTitle' => 'Expense by Company',
            'rows'      => $rows,
        ]);
    }

    public function byDepartment(): void
    {
        Auth::require();
        $rows = Database::select(
            "SELECT d.department_code, d.department_name, c.company_code,
                    COUNT(m.id) AS memo_count,
                    COALESCE(SUM(m.net_amount),0) AS net
             FROM departments d
             LEFT JOIN memos m ON m.department_id = d.id
             LEFT JOIN companies c ON d.company_id = c.id
             GROUP BY d.id ORDER BY c.company_code, net DESC"
        );
        $this->view('reports/by_department', [
            'pageTitle' => 'Expense by Department',
            'rows'      => $rows,
        ]);
    }

    public function byCategory(): void
    {
        Auth::require();
        $rows = Database::select(
            "SELECT cat.category_code, cat.category_name,
                    COUNT(i.id) AS item_count,
                    COALESCE(SUM(i.amount),0)     AS total,
                    COALESCE(SUM(i.net_amount),0) AS net
             FROM expense_categories cat
             LEFT JOIN memo_items i ON i.category_id = cat.id
             GROUP BY cat.id ORDER BY net DESC"
        );
        $this->view('reports/by_category', [
            'pageTitle' => 'Expense by Category',
            'rows'      => $rows,
        ]);
    }

    public function monthly(): void
    {
        Auth::require();
        $year = (int) ($_GET['year'] ?? date('Y'));
        $rows = Database::select(
            "SELECT MONTH(memo_date) AS m, c.company_code,
                    COUNT(*) AS cnt, COALESCE(SUM(net_amount),0) AS net
             FROM memos
             LEFT JOIN companies c ON memos.company_id = c.id
             WHERE YEAR(memo_date) = ?
             GROUP BY c.company_code, m
             ORDER BY m, c.company_code",
            [$year]
        );
        $this->view('reports/monthly', [
            'pageTitle' => 'Monthly Summary',
            'rows'      => $rows,
            'year'      => $year,
        ]);
    }

    public function pendingApproval(): void
    {
        Auth::require();
        $rows = Database::select(
            "SELECT m.*, c.company_code, d.department_code, u.full_name AS requester_name
             FROM memos m
             LEFT JOIN companies   c ON m.company_id    = c.id
             LEFT JOIN departments d ON m.department_id = d.id
             LEFT JOIN users       u ON m.requester_id  = u.id
             WHERE m.status IN ('submitted','manager_approved','accounting_checked')
             ORDER BY m.submitted_at ASC"
        );
        $this->view('reports/pending_approval', [
            'pageTitle' => 'Pending Approval',
            'rows'      => $rows,
        ]);
    }

    public function pendingPayment(): void
    {
        Auth::require();
        $rows = Database::select(
            "SELECT m.*, c.company_code, d.department_code, u.full_name AS requester_name
             FROM memos m
             LEFT JOIN companies   c ON m.company_id    = c.id
             LEFT JOIN departments d ON m.department_id = d.id
             LEFT JOIN users       u ON m.requester_id  = u.id
             WHERE m.payment_status IN ('pending_payment','partially_paid')
             ORDER BY m.required_payment_date ASC"
        );
        $this->view('reports/pending_payment', [
            'pageTitle' => 'Pending Payment',
            'rows'      => $rows,
        ]);
    }
}

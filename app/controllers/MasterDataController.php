<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use App\Models\ExpenseCategory;
use App\Models\Supplier;
use App\Models\Project;
use App\Models\ApprovalRule;

class MasterDataController extends Controller
{
    private function adminOnly(): void
    {
        Auth::requireRole(['admin']);
    }

    // ----- Users -----
    public function users(): void
    {
        $this->adminOnly();
        $this->view('master/users', [
            'pageTitle' => 'Master Data — Users',
            'users'     => User::listAll(),
            'companies' => Company::active(),
            'depts'     => Database::select("SELECT * FROM departments ORDER BY company_id, department_code"),
        ]);
    }

    public function saveUser(): void
    {
        $this->adminOnly();
        if (!verify_csrf()) $this->abort(419);

        $id = $this->input('id');
        $data = [
            'full_name'     => $this->input('full_name', ''),
            'email'         => $this->input('email', ''),
            'phone'         => $this->input('phone'),
            'position'      => $this->input('position'),
            'role'          => $this->input('role', 'requester'),
            'company_id'    => $this->input('company_id') ?: null,
            'department_id' => $this->input('department_id') ?: null,
            'is_active'     => $this->input('is_active') ? 1 : 0,
        ];

        if ($id) {
            User::update((int) $id, $data);
            if ($pw = $this->input('password')) {
                Database::execute("UPDATE users SET password_hash = ? WHERE id = ?",
                    [password_hash($pw, PASSWORD_BCRYPT), $id]);
            }
        } else {
            $pw = $this->input('password', 'password123');
            $data['password_hash'] = password_hash($pw, PASSWORD_BCRYPT);
            User::create($data);
        }
        flash('success', 'บันทึก User เรียบร้อย');
        $this->redirect('/master/users');
    }

    // ----- Companies / Departments (read-only display) -----
    public function companies(): void
    {
        $this->adminOnly();
        $this->view('master/companies', [
            'pageTitle' => 'Master Data — Companies',
            'companies' => Company::all(),
        ]);
    }

    public function departments(): void
    {
        $this->adminOnly();
        $rows = Database::select(
            "SELECT d.*, c.company_code, c.company_name
             FROM departments d
             LEFT JOIN companies c ON d.company_id = c.id
             ORDER BY c.company_code, d.department_code"
        );
        $this->view('master/departments', [
            'pageTitle'   => 'Master Data — Departments',
            'departments' => $rows,
        ]);
    }

    // ----- Categories -----
    public function categories(): void
    {
        $this->adminOnly();
        $this->view('master/categories', [
            'pageTitle'  => 'Master Data — Categories',
            'categories' => ExpenseCategory::all(),
        ]);
    }

    public function saveCategory(): void
    {
        $this->adminOnly();
        if (!verify_csrf()) $this->abort(419);
        $id = $this->input('id');
        $data = [
            'category_code'   => strtoupper($this->input('category_code', '')),
            'category_name'   => $this->input('category_name', ''),
            'accounting_code' => $this->input('accounting_code'),
            'is_active'       => $this->input('is_active') ? 1 : 0,
        ];
        $id ? ExpenseCategory::update((int)$id, $data) : ExpenseCategory::create($data);
        flash('success', 'Saved');
        $this->redirect('/master/categories');
    }

    // ----- Suppliers -----
    public function suppliers(): void
    {
        $this->adminOnly();
        $this->view('master/suppliers', [
            'pageTitle' => 'Master Data — Suppliers',
            'suppliers' => Supplier::all('supplier_name ASC'),
        ]);
    }

    public function saveSupplier(): void
    {
        $this->adminOnly();
        if (!verify_csrf()) $this->abort(419);
        $id = $this->input('id');
        $data = [
            'supplier_code'       => $this->input('supplier_code'),
            'supplier_name'       => $this->input('supplier_name', ''),
            'contact_name'        => $this->input('contact_name'),
            'phone'               => $this->input('phone'),
            'email'               => $this->input('email'),
            'tax_id'              => $this->input('tax_id'),
            'bank_name'           => $this->input('bank_name'),
            'bank_account_name'   => $this->input('bank_account_name'),
            'bank_account_number' => $this->input('bank_account_number'),
            'is_active'           => $this->input('is_active') ? 1 : 0,
        ];
        $id ? Supplier::update((int)$id, $data) : Supplier::create($data);
        flash('success', 'Saved');
        $this->redirect('/master/suppliers');
    }

    // ----- Projects -----
    public function projects(): void
    {
        $this->adminOnly();
        $this->view('master/projects', [
            'pageTitle' => 'Master Data — Projects',
            'projects'  => Database::select(
                "SELECT p.*, c.company_code FROM projects p
                 LEFT JOIN companies c ON p.company_id = c.id
                 ORDER BY p.project_code"
            ),
            'companies' => Company::active(),
        ]);
    }

    public function saveProject(): void
    {
        $this->adminOnly();
        if (!verify_csrf()) $this->abort(419);
        $id = $this->input('id');
        $data = [
            'project_code' => $this->input('project_code', ''),
            'project_name' => $this->input('project_name', ''),
            'company_id'   => $this->input('company_id') ?: null,
            'start_date'   => $this->input('start_date') ?: null,
            'end_date'     => $this->input('end_date') ?: null,
            'status'       => $this->input('status', 'active'),
        ];
        $id ? Project::update((int)$id, $data) : Project::create($data);
        flash('success', 'Saved');
        $this->redirect('/master/projects');
    }

    // ----- Approval Rules -----
    public function approvalRules(): void
    {
        $this->adminOnly();
        $rules = Database::select(
            "SELECT r.*, c.company_code FROM approval_rules r
             LEFT JOIN companies c ON r.company_id = c.id
             ORDER BY c.company_code, r.min_amount, r.approval_level"
        );
        $this->view('master/approval_rules', [
            'pageTitle' => 'Approval Rules',
            'rules'     => $rules,
        ]);
    }

    // ----- API: cascading departments -----
    public function apiDepartments(int $id): void
    {
        $this->json(Department::byCompany($id));
    }
}

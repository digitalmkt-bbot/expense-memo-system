<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;
use App\Models\Memo;
use App\Models\MemoItem;
use App\Models\Company;
use App\Models\Department;
use App\Models\Project;
use App\Models\ExpenseCategory;
use App\Models\Supplier;
use App\Models\RunningNumber;
use App\Models\ApprovalLog;
use App\Models\ApprovalRule;
use App\Models\Attachment;
use App\Models\Payment;

class MemoController extends Controller
{
    public function index(): void
    {
        Auth::require();
        $u = Auth::user();

        $filter = [
            'keyword'        => $_GET['q']        ?? null,
            'status'         => $_GET['status']   ?? null,
            'company_id'     => $_GET['company']  ?? null,
            'department_id'  => $_GET['dept']     ?? null,
            'date_from'      => $_GET['from']     ?? null,
            'date_to'        => $_GET['to']       ?? null,
        ];

        // Requester เห็นเฉพาะของตัวเอง
        if ($u['role'] === 'requester') $filter['requester_id'] = $u['id'];

        $memos = Memo::search($filter);
        $companies = Company::active();

        $this->view('memos/index', [
            'pageTitle' => 'My Memos',
            'memos'     => $memos,
            'filter'    => $filter,
            'companies' => $companies,
        ]);
    }

    public function create(): void
    {
        Auth::require();
        $u = Auth::user();

        $this->view('memos/create', [
            'pageTitle'  => 'Create Memo',
            'companies'  => Company::active(),
            'depts'      => Department::byCompany((int) $u['company_id']),
            'projects'   => Project::active((int) $u['company_id']),
            'categories' => ExpenseCategory::active(),
            'suppliers'  => Supplier::active(),
        ]);
    }

    public function store(): void
    {
        Auth::require();
        if (!verify_csrf()) $this->abort(419, 'CSRF mismatch');

        $u = Auth::user();
        $companyId = (int) $this->input('company_id', $u['company_id']);
        $deptId    = (int) $this->input('department_id', $u['department_id']);

        Database::beginTransaction();
        try {
            $memoId = Memo::create([
                'company_id'            => $companyId,
                'department_id'         => $deptId,
                'project_id'            => $this->input('project_id') ?: null,
                'project_name_text'     => $this->input('project_name_text') ?: null,
                'requester_id'          => $u['id'],
                'memo_date'             => $this->input('memo_date', date('Y-m-d')),
                'required_payment_date' => $this->input('required_payment_date') ?: null,
                'memo_type'             => $this->input('memo_type', 'general_expense'),
                'subject'               => $this->input('subject', ''),
                'objective'             => $this->input('objective'),
                'description'           => $this->input('description'),
                'currency'              => 'THB',
                'status'                => 'draft',
                'payment_status'        => 'unpaid',
                'created_by'            => $u['id'],
                'updated_by'            => $u['id'],
            ]);

            Database::commit();
            flash('success', 'สร้าง Memo Draft แล้ว — เพิ่มรายการค่าใช้จ่ายและไฟล์แนบก่อน Submit ได้เลย');
            $this->redirect('/memos/' . $memoId . '/edit');
        } catch (\Throwable $e) {
            Database::rollBack();
            flash('error', 'บันทึกไม่สำเร็จ: ' . $e->getMessage());
            $this->redirect('/memos/create');
        }
    }

    public function show(int $id): void
    {
        Auth::require();
        $memo = Memo::findFull($id);
        if (!$memo) $this->abort(404, 'Memo not found');
        $this->authorizeView($memo);

        $this->view('memos/show', [
            'pageTitle' => 'Memo: ' . ($memo['memo_no'] ?? 'DRAFT'),
            'memo'      => $memo,
            'items'     => MemoItem::byMemo($id),
            'attachments'=> Attachment::forMemo($id),
            'logs'      => ApprovalLog::byMemo($id),
            'payments'  => Payment::byMemo($id),
            'totalPaid' => Payment::totalPaid($id),
        ]);
    }

    public function edit(int $id): void
    {
        Auth::require();
        $memo = Memo::findFull($id);
        if (!$memo) $this->abort(404);
        $this->authorizeEdit($memo);

        $u = Auth::user();
        $this->view('memos/edit', [
            'pageTitle'  => 'Edit Memo: ' . ($memo['memo_no'] ?? 'DRAFT'),
            'memo'       => $memo,
            'items'      => MemoItem::byMemo($id),
            'attachments'=> Attachment::forMemo($id),
            'companies'  => Company::active(),
            'depts'      => Department::byCompany((int) $memo['company_id']),
            'projects'   => Project::active((int) $memo['company_id']),
            'categories' => ExpenseCategory::active(),
            'suppliers'  => Supplier::active(),
        ]);
    }

    public function update(int $id): void
    {
        Auth::require();
        if (!verify_csrf()) $this->abort(419);

        $memo = Memo::find($id);
        if (!$memo) $this->abort(404);
        $this->authorizeEdit($memo);

        $u = Auth::user();
        Memo::update($id, [
            'company_id'            => (int) $this->input('company_id', $memo['company_id']),
            'department_id'         => (int) $this->input('department_id', $memo['department_id']),
            'project_id'            => $this->input('project_id') ?: null,
            'project_name_text'     => $this->input('project_name_text') ?: null,
            'memo_date'             => $this->input('memo_date', $memo['memo_date']),
            'required_payment_date' => $this->input('required_payment_date') ?: null,
            'memo_type'             => $this->input('memo_type', $memo['memo_type']),
            'subject'               => $this->input('subject', $memo['subject']),
            'objective'             => $this->input('objective'),
            'description'           => $this->input('description'),
            'updated_by'            => $u['id'],
        ]);

        flash('success', 'บันทึกข้อมูลแล้ว');
        $this->redirect('/memos/' . $id . '/edit');
    }

    public function addItem(int $id): void
    {
        Auth::require();
        if (!verify_csrf()) $this->abort(419);

        $memo = Memo::find($id);
        if (!$memo) $this->abort(404);
        $this->authorizeEdit($memo);

        $data = MemoItem::calculate([
            'memo_id'        => $id,
            'expense_date'   => $this->input('expense_date') ?: null,
            'category_id'    => $this->input('category_id') ?: null,
            'supplier_id'    => $this->input('supplier_id') ?: null,
            'supplier_name_text' => $this->input('supplier_name_text') ?: null,
            'item_name'      => $this->input('item_name', ''),
            'description'    => $this->input('description'),
            'quantity'       => $this->input('quantity', 1),
            'unit'           => $this->input('unit'),
            'unit_price'     => $this->input('unit_price', 0),
            'vat_type'       => $this->input('vat_type', 'none'),
            'vat_rate'       => $this->input('vat_rate', 0),
            'wht_type'       => $this->input('wht_type', 'none'),
            'wht_rate'       => $this->input('wht_rate', 0),
            'payment_method' => $this->input('payment_method', 'bank_transfer'),
            'note'           => $this->input('note'),
        ]);

        MemoItem::create($data);
        Memo::recomputeTotals($id);

        flash('success', 'เพิ่มรายการเรียบร้อย');
        $this->redirect('/memos/' . $id . '/edit');
    }

    public function deleteItem(int $id, int $itemId): void
    {
        Auth::require();
        if (!verify_csrf()) $this->abort(419);
        $memo = Memo::find($id);
        if (!$memo) $this->abort(404);
        $this->authorizeEdit($memo);

        MemoItem::delete($itemId);
        Memo::recomputeTotals($id);

        flash('success', 'ลบรายการแล้ว');
        $this->redirect('/memos/' . $id . '/edit');
    }

    public function uploadAttachment(int $id): void
    {
        Auth::require();
        if (!verify_csrf()) $this->abort(419);

        $memo = Memo::find($id);
        if (!$memo) $this->abort(404);
        $this->authorizeEdit($memo);

        if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            flash('error', 'อัพโหลดไม่สำเร็จ');
            $this->redirect('/memos/' . $id . '/edit');
        }

        $file = $_FILES['file'];
        $cfg  = config('upload');
        if ($file['size'] > $cfg['max_size']) {
            flash('error', 'ไฟล์ใหญ่เกินกว่า ' . round($cfg['max_size']/1024/1024) . ' MB');
            $this->redirect('/memos/' . $id . '/edit');
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $cfg['allowed_ext'], true)) {
            flash('error', 'นามสกุลไฟล์ไม่อนุญาต (ใช้ได้: ' . implode(', ', $cfg['allowed_ext']) . ')');
            $this->redirect('/memos/' . $id . '/edit');
        }

        $folder = $cfg['path'] . '/memos/' . $id;
        if (!is_dir($folder)) @mkdir($folder, 0775, true);
        $newName = uniqid('att_') . '.' . $ext;
        $dest = $folder . '/' . $newName;
        move_uploaded_file($file['tmp_name'], $dest);

        Attachment::create([
            'related_type' => 'memo',
            'related_id'   => $id,
            'file_type'    => $this->input('file_type', 'other'),
            'file_name'    => $file['name'],
            'file_url'     => $cfg['public_path'] . '/memos/' . $id . '/' . $newName,
            'mime_type'    => $file['type'],
            'file_size'    => $file['size'],
            'uploaded_by'  => Auth::id(),
        ]);

        flash('success', 'อัพโหลดไฟล์แล้ว');
        $this->redirect('/memos/' . $id . '/edit');
    }

    public function deleteAttachment(int $id): void
    {
        Auth::require();
        if (!verify_csrf()) $this->abort(419);
        Attachment::delete($id);
        flash('success', 'ลบไฟล์แล้ว');
        $back = $_SERVER['HTTP_REFERER'] ?? url('/dashboard');
        header('Location: ' . $back);
    }

    /**
     * Submit Memo → Generate Memo No.
     */
    public function submit(int $id): void
    {
        Auth::require();
        if (!verify_csrf()) $this->abort(419);

        $memo = Memo::find($id);
        if (!$memo) $this->abort(404);
        $this->authorizeEdit($memo);

        if ($memo['status'] !== 'draft' && $memo['status'] !== 'revision_required') {
            flash('error', 'สถานะปัจจุบันไม่อนุญาตให้ Submit');
            $this->redirect('/memos/' . $id);
        }

        $items = MemoItem::byMemo($id);
        if (!$items) {
            flash('error', 'ต้องมีรายการค่าใช้จ่ายอย่างน้อย 1 รายการ');
            $this->redirect('/memos/' . $id . '/edit');
        }

        Database::beginTransaction();
        try {
            // Recompute totals
            Memo::recomputeTotals($id);
            $memo = Memo::find($id);

            // Generate memo_no (ถ้ายังไม่มี)
            $memoNo = $memo['memo_no'];
            if (empty($memoNo)) {
                $deptCode = Department::code((int) $memo['department_id']);
                $memoNo = RunningNumber::generate(
                    (int) $memo['company_id'],
                    (int) $memo['department_id'],
                    $deptCode,
                    $memo['memo_date']
                );
            }

            Memo::update($id, [
                'memo_no'              => $memoNo,
                'status'               => 'submitted',
                'submitted_at'         => date('Y-m-d H:i:s'),
                'current_approval_step'=> 1,
            ]);

            ApprovalLog::add($id, 0, Auth::id(), Auth::role(), 'submitted', 'Memo submitted for approval');

            Database::commit();
            flash('success', 'Submit Memo สำเร็จ — เลขที่ Memo: ' . $memoNo);
            $this->redirect('/memos/' . $id);
        } catch (\Throwable $e) {
            Database::rollBack();
            flash('error', 'Submit ไม่สำเร็จ: ' . $e->getMessage());
            $this->redirect('/memos/' . $id);
        }
    }

    public function cancel(int $id): void
    {
        Auth::require();
        if (!verify_csrf()) $this->abort(419);
        $memo = Memo::find($id);
        if (!$memo) $this->abort(404);
        $this->authorizeEdit($memo);

        Memo::update($id, ['status' => 'cancelled']);
        ApprovalLog::add($id, 0, Auth::id(), Auth::role(), 'cancelled');
        flash('success', 'ยกเลิก Memo แล้ว');
        $this->redirect('/memos/' . $id);
    }

    public function pdf(int $id): void
    {
        Auth::require();
        $memo = Memo::findFull($id);
        if (!$memo) $this->abort(404);
        $this->authorizeView($memo);

        $items = MemoItem::byMemo($id);
        $logs  = ApprovalLog::byMemo($id);

        // ใช้ HTML view โดย browser print เป็น PDF (Ctrl+P → Save as PDF)
        // หรือเชื่อม Dompdf/MPDF ผ่าน Composer ภายหลัง
        require __DIR__ . '/../views/memos/pdf.php';
    }

    // ----- Authorization helpers -----
    private function authorizeView(array $memo): void
    {
        $u = Auth::user();
        if ($u['role'] === 'admin' || $u['role'] === 'director' || $u['role'] === 'accounting') return;
        if ($u['role'] === 'manager' && (int) $u['department_id'] === (int) $memo['department_id']) return;
        if ((int) $memo['requester_id'] === (int) $u['id']) return;
        $this->abort(403, 'No permission to view this memo');
    }

    private function authorizeEdit(array $memo): void
    {
        $u = Auth::user();
        if ($u['role'] === 'admin') return;
        if ((int) $memo['requester_id'] !== (int) $u['id']) {
            $this->abort(403, 'Only requester can edit this memo');
        }
        $editable = ['draft','revision_required'];
        if (!in_array($memo['status'], $editable, true)) {
            $this->abort(403, 'Memo อยู่ในสถานะ ' . $memo['status'] . ' จึงไม่สามารถแก้ไขได้');
        }
    }
}

<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Database;
use App\Models\Memo;
use App\Models\Payment;
use App\Models\Attachment;

class PaymentController extends Controller
{
    public function index(): void
    {
        Auth::requireRole(['accounting','admin']);

        // Tabs: pending payment / partially paid / paid
        $tab = $_GET['tab'] ?? 'pending';
        $where = match ($tab) {
            'pending'   => "m.status = 'approved' AND m.payment_status IN ('unpaid','pending_payment')",
            'partial'   => "m.payment_status = 'partially_paid'",
            'paid'      => "m.payment_status = 'paid'",
            default     => "1",
        };

        $memos = Database::select(
            "SELECT m.*, c.company_code, d.department_code, u.full_name AS requester_name
             FROM memos m
             LEFT JOIN companies   c ON m.company_id    = c.id
             LEFT JOIN departments d ON m.department_id = d.id
             LEFT JOIN users       u ON m.requester_id  = u.id
             WHERE $where
             ORDER BY m.required_payment_date ASC, m.approved_at DESC"
        );

        $this->view('payments/index', [
            'pageTitle' => 'Payments',
            'memos'     => $memos,
            'tab'       => $tab,
        ]);
    }

    public function create(int $id): void
    {
        Auth::requireRole(['accounting','admin']);
        $memo = Memo::findFull($id);
        if (!$memo) $this->abort(404);

        $this->view('payments/create', [
            'pageTitle' => 'Record Payment — ' . ($memo['memo_no'] ?? 'DRAFT'),
            'memo'      => $memo,
            'totalPaid' => Payment::totalPaid($id),
        ]);
    }

    public function store(int $id): void
    {
        Auth::requireRole(['accounting','admin']);
        if (!verify_csrf()) $this->abort(419);

        $memo = Memo::find($id);
        if (!$memo) $this->abort(404);
        if ($memo['status'] !== 'approved' && $memo['payment_status'] !== 'partially_paid') {
            flash('error', 'Memo ยังไม่ได้รับอนุมัติ');
            $this->redirect('/memos/' . $id);
        }

        $paid = (float) $this->input('paid_amount', 0);
        if ($paid <= 0) {
            flash('error', 'ระบุจำนวนเงินที่จ่าย');
            $this->redirect('/memos/' . $id . '/payment');
        }

        Database::beginTransaction();
        try {
            $paymentNo = sprintf('PAY-%s-%04d', date('Ymd'), random_int(1, 9999));
            $paymentId = Payment::create([
                'memo_id'             => $id,
                'payment_no'          => $paymentNo,
                'payment_date'        => $this->input('payment_date', date('Y-m-d')),
                'paid_amount'         => $paid,
                'payment_method'      => $this->input('payment_method', 'bank_transfer'),
                'paid_by'             => Auth::id(),
                'paid_to_name'        => $this->input('paid_to_name'),
                'bank_name'           => $this->input('bank_name'),
                'bank_account_name'   => $this->input('bank_account_name'),
                'bank_account_number' => $this->input('bank_account_number'),
                'payment_note'        => $this->input('payment_note'),
            ]);

            // Upload slip ถ้ามี
            if (!empty($_FILES['slip']) && $_FILES['slip']['error'] === UPLOAD_ERR_OK) {
                $cfg = config('upload');
                $folder = $cfg['path'] . '/payments/' . $id;
                if (!is_dir($folder)) @mkdir($folder, 0775, true);
                $ext = strtolower(pathinfo($_FILES['slip']['name'], PATHINFO_EXTENSION));
                $newName = uniqid('slip_') . '.' . $ext;
                move_uploaded_file($_FILES['slip']['tmp_name'], $folder . '/' . $newName);
                Attachment::create([
                    'related_type' => 'payment',
                    'related_id'   => $paymentId,
                    'file_type'    => 'payment_slip',
                    'file_name'    => $_FILES['slip']['name'],
                    'file_url'     => $cfg['public_path'] . '/payments/' . $id . '/' . $newName,
                    'mime_type'    => $_FILES['slip']['type'],
                    'file_size'    => $_FILES['slip']['size'],
                    'uploaded_by'  => Auth::id(),
                ]);
            }

            // อัพเดท Payment Status
            $totalPaid = Payment::totalPaid($id);
            $net = (float) $memo['net_amount'];

            if ($totalPaid >= $net) {
                Memo::update($id, [
                    'payment_status' => 'paid',
                    'status'         => 'paid',
                    'paid_at'        => date('Y-m-d H:i:s'),
                ]);
            } else {
                Memo::update($id, [
                    'payment_status' => 'partially_paid',
                    'status'         => 'partially_paid',
                ]);
            }

            Database::commit();
            flash('success', 'บันทึกการจ่ายเงินแล้ว — ' . $paymentNo);
            $this->redirect('/memos/' . $id);
        } catch (\Throwable $e) {
            Database::rollBack();
            flash('error', 'ไม่สำเร็จ: ' . $e->getMessage());
            $this->redirect('/memos/' . $id . '/payment');
        }
    }

    public function close(int $id): void
    {
        Auth::requireRole(['accounting','admin']);
        if (!verify_csrf()) $this->abort(419);
        $memo = Memo::find($id);
        if (!$memo) $this->abort(404);
        if ($memo['payment_status'] !== 'paid') {
            flash('error', 'ยังจ่ายเงินไม่ครบ จึงปิด Memo ไม่ได้');
            $this->redirect('/memos/' . $id);
        }
        Memo::update($id, ['status' => 'closed', 'closed_at' => date('Y-m-d H:i:s')]);
        flash('success', 'ปิด Memo เรียบร้อย');
        $this->redirect('/memos/' . $id);
    }
}

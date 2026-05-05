<?php
/**
 * Global helpers
 */
use App\Core\Auth;

if (!function_exists('config')) {
    function config(string $key = null) {
        static $cfg = null;
        if ($cfg === null) $cfg = require __DIR__ . '/../../config/config.php';
        if ($key === null) return $cfg;
        $parts = explode('.', $key);
        $val = $cfg;
        foreach ($parts as $p) $val = $val[$p] ?? null;
        return $val;
    }
}

if (!function_exists('url')) {
    function url(string $path = '/') : string {
        $base = rtrim(config('app.base_url'), '/');
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset(string $path) : string {
        return url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('e')) {
    function e($val) : string {
        return htmlspecialchars((string) $val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token() : string {
        if (empty($_SESSION['_csrf'])) $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        return $_SESSION['_csrf'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field() : string {
        return '<input type="hidden" name="_csrf" value="' . csrf_token() . '">';
    }
}

if (!function_exists('verify_csrf')) {
    function verify_csrf() : bool {
        $token = $_POST['_csrf'] ?? '';
        return $token && hash_equals($_SESSION['_csrf'] ?? '', $token);
    }
}

if (!function_exists('flash')) {
    function flash(string $type = null, string $message = null) {
        if ($type !== null && $message !== null) {
            $_SESSION['_flash'][$type] = $message;
            return null;
        }
        if ($type !== null) {
            $msg = $_SESSION['_flash'][$type] ?? null;
            unset($_SESSION['_flash'][$type]);
            return $msg;
        }
        return $_SESSION['_flash'] ?? [];
    }
}

if (!function_exists('old')) {
    function old(string $key, $default = '') {
        return $_SESSION['_old'][$key] ?? $default;
    }
}

if (!function_exists('format_money')) {
    function format_money($val, int $dec = 2) : string {
        return number_format((float) $val, $dec, '.', ',');
    }
}

if (!function_exists('format_date')) {
    function format_date(?string $date) : string {
        if (!$date) return '-';
        return date('d/m/Y', strtotime($date));
    }
}

if (!function_exists('format_datetime')) {
    function format_datetime(?string $dt) : string {
        if (!$dt) return '-';
        return date('d/m/Y H:i', strtotime($dt));
    }
}

if (!function_exists('status_badge')) {
    function status_badge(string $status) : string {
        $map = [
            'draft'              => 'bg-secondary',
            'submitted'          => 'bg-primary',
            'manager_approved'   => 'bg-info',
            'accounting_checked' => 'bg-info',
            'director_approved'  => 'bg-info',
            'approved'           => 'bg-success',
            'revision_required'  => 'bg-warning text-dark',
            'rejected'           => 'bg-danger',
            'pending_payment'    => 'bg-warning text-dark',
            'partially_paid'     => 'bg-warning text-dark',
            'paid'               => 'bg-success',
            'closed'             => 'bg-dark',
            'cancelled'          => 'bg-secondary',
        ];
        $class = $map[$status] ?? 'bg-secondary';
        $label = strtoupper(str_replace('_', ' ', $status));
        return '<span class="badge ' . $class . '">' . $label . '</span>';
    }
}

if (!function_exists('memo_type_label')) {
    function memo_type_label(string $type) : string {
        return [
            'advance_payment'  => 'Advance Payment / ขอเงินสำรองจ่าย',
            'reimbursement'    => 'Reimbursement / เบิกคืน',
            'supplier_payment' => 'Supplier Payment / จ่าย Supplier',
            'general_expense'  => 'General Expense / ค่าใช้จ่ายทั่วไป',
            'petty_cash'       => 'Petty Cash / เงินสดย่อย',
            'other'            => 'Other / อื่น ๆ',
        ][$type] ?? $type;
    }
}

if (!function_exists('user')) {
    function user() { return Auth::user(); }
}

if (!function_exists('autoload')) {
    /** PSR-4-ish autoloader */
    function emm_autoload(string $class) : void {
        if (!str_starts_with($class, 'App\\')) return;
        $relative = str_replace('App\\', '', $class);
        $relative = str_replace('\\', '/', $relative);
        // Map App\Controllers\Foo -> app/controllers/Foo.php  (ใช้ตัวพิมพ์เล็กสำหรับ folder)
        $segments = explode('/', $relative);
        $file = array_pop($segments);
        $folder = strtolower(implode('/', $segments));
        $path = __DIR__ . '/../' . ($folder ? $folder . '/' : '') . $file . '.php';
        if (file_exists($path)) require_once $path;
    }
}

spl_autoload_register('emm_autoload');

<?php
/**
 * FLAT-LAYOUT FRONT CONTROLLER (สำหรับ cPanel/DirectAdmin shared hosting)
 *
 * วาง index.php นี้ที่ root ของ public_html เมื่อ:
 *   - คุณ upload ทั้ง project ไปไว้ใน public_html ทั้งก้อน
 *   - และไม่สามารถเปลี่ยน document root ได้
 *
 * Folder layout (ใน public_html):
 *   public_html/
 *   ├── index.php          ← ไฟล์นี้
 *   ├── .htaccess          ← จาก deploy/htaccess.root
 *   ├── setup.php
 *   ├── app/               (denied via .htaccess)
 *   ├── config/            (denied via .htaccess)
 *   ├── database/          (denied via .htaccess)
 *   └── storage/uploads/   (writable)
 */
declare(strict_types=1);

// Production safety
ini_set('display_errors', '0');
error_reporting(E_ALL);

require __DIR__ . '/app/core/helpers.php';

date_default_timezone_set(config('app.timezone'));

// Session config (production-safe cookies)
session_name(config('session.name'));
$cookieParams = [
    'lifetime' => config('session.lifetime'),
    'path'     => '/',
    'httponly' => true,
    'samesite' => config('session.samesite') ?? 'Lax',
];
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    $cookieParams['secure'] = true;
}
session_set_cookie_params($cookieParams);
session_start();

// Security headers
foreach ((array) config('security.headers') as $h => $v) header("$h: $v");

if (config('app.debug')) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

use App\Core\Router;

$router = new Router();

// ----- Auth -----
$router->get('/login',  'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

// ----- Dashboard -----
$router->get('/',          'DashboardController@index');
$router->get('/dashboard', 'DashboardController@index');

// ----- Memos -----
$router->get('/memos',                   'MemoController@index');
$router->get('/memos/create',            'MemoController@create');
$router->post('/memos',                  'MemoController@store');
$router->get('/memos/{id}',              'MemoController@show');
$router->get('/memos/{id}/edit',         'MemoController@edit');
$router->post('/memos/{id}',             'MemoController@update');
$router->post('/memos/{id}/submit',      'MemoController@submit');
$router->post('/memos/{id}/cancel',      'MemoController@cancel');
$router->get('/memos/{id}/pdf',          'MemoController@pdf');
$router->post('/memos/{id}/items',                 'MemoController@addItem');
$router->post('/memos/{id}/items/{itemId}/delete', 'MemoController@deleteItem');
$router->post('/memos/{id}/attachments',           'MemoController@uploadAttachment');
$router->post('/attachments/{id}/delete',          'MemoController@deleteAttachment');

// ----- Approval -----
$router->get('/approvals',                      'ApprovalController@index');
$router->post('/memos/{id}/approve',            'ApprovalController@approve');
$router->post('/memos/{id}/reject',             'ApprovalController@reject');
$router->post('/memos/{id}/revision',           'ApprovalController@revision');

// ----- Payment -----
$router->get('/payments',                       'PaymentController@index');
$router->get('/memos/{id}/payment',             'PaymentController@create');
$router->post('/memos/{id}/payment',            'PaymentController@store');
$router->post('/memos/{id}/close',              'PaymentController@close');

// ----- Reports -----
$router->get('/reports',                        'ReportController@index');
$router->get('/reports/by-company',             'ReportController@byCompany');
$router->get('/reports/by-department',          'ReportController@byDepartment');
$router->get('/reports/by-category',            'ReportController@byCategory');
$router->get('/reports/monthly',                'ReportController@monthly');
$router->get('/reports/pending-approval',       'ReportController@pendingApproval');
$router->get('/reports/pending-payment',        'ReportController@pendingPayment');

// ----- Master Data -----
$router->get('/master/users',                   'MasterDataController@users');
$router->post('/master/users',                  'MasterDataController@saveUser');
$router->get('/master/companies',               'MasterDataController@companies');
$router->get('/master/departments',             'MasterDataController@departments');
$router->get('/master/categories',              'MasterDataController@categories');
$router->post('/master/categories',             'MasterDataController@saveCategory');
$router->get('/master/suppliers',               'MasterDataController@suppliers');
$router->post('/master/suppliers',              'MasterDataController@saveSupplier');
$router->get('/master/projects',                'MasterDataController@projects');
$router->post('/master/projects',               'MasterDataController@saveProject');
$router->get('/master/approval-rules',          'MasterDataController@approvalRules');
$router->get('/api/companies/{id}/departments', 'MasterDataController@apiDepartments');

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

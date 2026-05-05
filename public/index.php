<?php
/**
 * Front Controller — ทุก Request เข้ามาผ่านไฟล์นี้
 */
declare(strict_types=1);

// Bootstrap
require __DIR__ . '/../app/core/helpers.php';

date_default_timezone_set(config('app.timezone'));

// Session
session_name(config('session.name'));
session_set_cookie_params([
    'lifetime' => config('session.lifetime'),
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

if (config('app.debug')) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
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

// ----- Memos (Requester) -----
$router->get('/memos',                   'MemoController@index');
$router->get('/memos/create',            'MemoController@create');
$router->post('/memos',                  'MemoController@store');
$router->get('/memos/{id}',              'MemoController@show');
$router->get('/memos/{id}/edit',         'MemoController@edit');
$router->post('/memos/{id}',             'MemoController@update');
$router->post('/memos/{id}/submit',      'MemoController@submit');
$router->post('/memos/{id}/cancel',      'MemoController@cancel');
$router->get('/memos/{id}/pdf',          'MemoController@pdf');

// ----- Memo Items -----
$router->post('/memos/{id}/items',           'MemoController@addItem');
$router->post('/memos/{id}/items/{itemId}/delete', 'MemoController@deleteItem');

// ----- Attachments -----
$router->post('/memos/{id}/attachments',          'MemoController@uploadAttachment');
$router->post('/attachments/{id}/delete',         'MemoController@deleteAttachment');

// ----- Approval -----
$router->get('/approvals',                      'ApprovalController@index');
$router->post('/memos/{id}/approve',            'ApprovalController@approve');
$router->post('/memos/{id}/reject',             'ApprovalController@reject');
$router->post('/memos/{id}/revision',           'ApprovalController@revision');

// ----- Accounting / Payment -----
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

// ----- Master Data (Admin) -----
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

// AJAX helper — get departments by company
$router->get('/api/companies/{id}/departments', 'MasterDataController@apiDepartments');

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

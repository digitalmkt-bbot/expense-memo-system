<?php
/**
 * App Config — Environment-aware
 * Production deployments (Railway, etc.) override values via ENV vars.
 */

$env = static function (string $key, $default = null) {
    $val = getenv($key);
    return ($val === false || $val === '') ? $default : $val;
};

// Detect if running on Railway/production
$isProd = $env('APP_ENV') === 'production'
       || (bool) $env('RAILWAY_ENVIRONMENT')
       || (bool) $env('RAILWAY_PROJECT_ID');

return [
    'app' => [
        'name'      => $env('APP_NAME', 'Expense Memo Management System'),
        'company'   => 'LOVE ISLAND CO., LTD. / ANDAMAN SUNDAY CO., LTD.',
        'base_url'  => $env('APP_BASE_URL', $isProd ? '' : '/expense-memo-system/public'),
        'timezone'  => $env('APP_TIMEZONE', 'Asia/Bangkok'),
        'locale'    => 'th',
        'debug'     => filter_var($env('APP_DEBUG', $isProd ? 'false' : 'true'), FILTER_VALIDATE_BOOLEAN),
        'currency'  => 'THB',
        'env'       => $isProd ? 'production' : 'local',
    ],

    'session' => [
        'name'     => 'EMM_SESSION',
        'lifetime' => (int) $env('SESSION_LIFETIME', 60 * 60 * 8),
        'secure'   => (bool) $isProd,
        'samesite' => $isProd ? 'Strict' : 'Lax',
    ],

    'upload' => [
        'path'        => $env('UPLOAD_PATH', __DIR__ . '/../storage/uploads'),
        'public_path' => '/storage/uploads',
        'max_size'    => 10 * 1024 * 1024,
        'allowed_ext' => ['pdf','jpg','jpeg','png','gif','xlsx','xls','docx','doc'],
    ],

    'pagination' => ['per_page' => 20],

    'approval' => [
        'tiers' => [
            ['min' => 0,         'max' => 10000,    'requires_director' => false],
            ['min' => 10000.01,  'max' => 50000,    'requires_director' => true],
            ['min' => 50000.01,  'max' => 99999999, 'requires_director' => true],
        ],
    ],

    'security' => [
        'headers' => [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options'        => 'SAMEORIGIN',
            'Referrer-Policy'        => 'same-origin',
            'Permissions-Policy'     => 'geolocation=(), camera=(), microphone=()',
        ],
    ],

    /**
     * Auto-install on first boot if no admin user exists yet.
     * ใช้ใน Railway deployment ครั้งแรกเพื่อสร้าง schema + admin อัตโนมัติ
     * ปิดได้ด้วย ENV: AUTO_INSTALL=false
     */
    'auto_install' => filter_var($env('AUTO_INSTALL', 'true'), FILTER_VALIDATE_BOOLEAN),

    /**
     * Default admin (สร้างครั้งแรกใน auto-install)
     * ใน Railway: ตั้ง ADMIN_EMAIL + ADMIN_PASSWORD ใน Variables
     */
    'admin_seed' => [
        'email'    => $env('ADMIN_EMAIL',    'admin@loveandaman.com'),
        'password' => $env('ADMIN_PASSWORD', null),  // ถ้า null = random + log
        'name'     => $env('ADMIN_NAME',     'System Administrator'),
    ],
];

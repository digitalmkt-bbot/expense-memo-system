<?php
/**
 * Production Config — สำหรับ Live Server
 *
 * วิธีใช้: เปลี่ยนชื่อไฟล์นี้เป็น config.php บน server
 *          (cp config.production.php config.php)
 *
 * หรือใส่ environment-specific ใน .env แล้ว load
 */
return [
    'app' => [
        'name'      => 'Expense Memo Management System',
        'company'   => 'LOVE ISLAND CO., LTD. / ANDAMAN SUNDAY CO., LTD.',
        // ───────────── ปรับให้ตรงกับ Live Domain ─────────────
        // ตัวอย่าง:
        //   https://memo.loveandaman.com/         → 'base_url' => ''
        //   https://www.loveandaman.com/memo/     → 'base_url' => '/memo'
        //   subdomain ที่ document root ตรงไปแล้ว  → 'base_url' => ''
        'base_url'  => '',
        'timezone'  => 'Asia/Bangkok',
        'locale'    => 'th',
        'debug'     => false,            // production = false เสมอ
        'currency'  => 'THB',
    ],

    'session' => [
        'name'     => 'EMM_SESSION',
        'lifetime' => 60 * 60 * 8,
        'secure'   => true,              // ส่งผ่าน HTTPS เท่านั้น (เปิด SSL ก่อนใช้)
        'samesite' => 'Strict',
    ],

    'upload' => [
        'path'        => __DIR__ . '/../storage/uploads',
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

    /**
     * Security Headers (จะถูก inject โดย index.php ใน production)
     */
    'security' => [
        'headers' => [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options'        => 'SAMEORIGIN',
            'Referrer-Policy'        => 'same-origin',
            'Permissions-Policy'     => 'geolocation=(), camera=(), microphone=()',
        ],
    ],
];

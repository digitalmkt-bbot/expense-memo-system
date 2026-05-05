<?php
/**
 * Production Database Config
 * แก้ค่าจาก cPanel → MySQL Databases หลังสร้าง user/database แล้ว
 */
return [
    'driver'    => 'mysql',
    'host'      => 'localhost',                      // cPanel ส่วนใหญ่ใช้ localhost
    'port'      => 3306,
    'database'  => 'YOURUSER_expense_memo',          // ส่วนใหญ่ cPanel จะเติม prefix ให้
    'username'  => 'YOURUSER_emm_app',
    'password'  => 'CHANGE_THIS_STRONG_PASSWORD',
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'options'   => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ],
];

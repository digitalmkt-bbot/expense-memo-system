<?php
/**
 * Database Config — Environment-aware
 *
 * รองรับ:
 *   - Local dev:        ค่า default ด้านล่าง
 *   - Railway:          อ่านจาก MYSQLHOST, MYSQLUSER, MYSQLPASSWORD, MYSQLDATABASE, MYSQLPORT
 *                       (Railway MySQL plugin inject ให้อัตโนมัติ)
 *   - Generic:          อ่านจาก DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT
 *   - DATABASE_URL:     mysql://user:pass@host:port/dbname (Heroku-style)
 */

// Helper: getenv กับ fallback
$env = static function (string $key, $default = null) {
    $val = getenv($key);
    return ($val === false || $val === '') ? $default : $val;
};

// Helper: parse DATABASE_URL ถ้ามี
$parseUrl = static function (?string $url): ?array {
    if (!$url) return null;
    $p = parse_url($url);
    if (!$p) return null;
    return [
        'host'     => $p['host']     ?? 'localhost',
        'port'     => $p['port']     ?? 3306,
        'database' => isset($p['path']) ? ltrim($p['path'], '/') : '',
        'username' => $p['user']     ?? '',
        'password' => $p['pass']     ?? '',
    ];
};

// Order of preference: DATABASE_URL > MYSQL* > DB_* > defaults
$conn = $parseUrl($env('DATABASE_URL') ?? $env('MYSQL_URL'));

if (!$conn) {
    $conn = [
        'host'     => $env('MYSQLHOST',     $env('DB_HOST', '127.0.0.1')),
        'port'     => (int) $env('MYSQLPORT', $env('DB_PORT', 3306)),
        'database' => $env('MYSQLDATABASE', $env('DB_NAME', 'expense_memo')),
        'username' => $env('MYSQLUSER',     $env('DB_USER', 'root')),
        'password' => $env('MYSQLPASSWORD', $env('DB_PASSWORD', '')),
    ];
}

return [
    'driver'    => 'mysql',
    'host'      => $conn['host'],
    'port'      => (int) $conn['port'],
    'database'  => $conn['database'],
    'username'  => $conn['username'],
    'password'  => $conn['password'],
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'options'   => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ],
];

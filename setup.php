<?php
/**
 * ===================================================================
 * Web Installer for Expense Memo Management System
 * ===================================================================
 *
 *   วางไฟล์นี้ที่ document root (ข้าง index.php)
 *   เปิด browser ไปที่ https://yourdomain.com/setup.php
 *
 *   ⚠️  ลบไฟล์นี้ทันทีหลังติดตั้งเสร็จ! (ระบบจะแนะนำให้ลบ)
 * ===================================================================
 */
declare(strict_types=1);
session_start();

// =============== Bootstrap ===============
$ROOT = __DIR__;
$step = (int) ($_GET['step'] ?? 1);
$err  = '';
$msg  = '';

// =============== Helpers ===============
function h($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }
function isInstalled(): bool {
    return file_exists(__DIR__ . '/.installed');
}

// =============== Logic ===============
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isInstalled() && empty($_POST['force'])) {
        $err = 'ระบบถูกติดตั้งแล้ว — ลบไฟล์ .installed ก่อนหากต้องการติดตั้งใหม่';
    }
    elseif ($step === 2) {
        // Save DB config
        $_SESSION['db'] = [
            'host'     => trim($_POST['db_host'] ?? 'localhost'),
            'port'     => (int) ($_POST['db_port'] ?? 3306),
            'database' => trim($_POST['db_name'] ?? ''),
            'username' => trim($_POST['db_user'] ?? ''),
            'password' => $_POST['db_pass'] ?? '',
        ];

        try {
            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                $_SESSION['db']['host'], $_SESSION['db']['port'], $_SESSION['db']['database']);
            $pdo = new PDO($dsn, $_SESSION['db']['username'], $_SESSION['db']['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            header('Location: setup.php?step=3');
            exit;
        } catch (Throwable $e) {
            $err = 'เชื่อมต่อ DB ไม่ได้: ' . $e->getMessage();
        }
    }
    elseif ($step === 3 && !empty($_SESSION['db'])) {
        // Run schema + seed
        try {
            $db = $_SESSION['db'];
            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                $db['host'], $db['port'], $db['database']);
            $pdo = new PDO($dsn, $db['username'], $db['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);

            $schemaSql = file_get_contents($ROOT . '/database/01_schema.sql');
            // ตัด CREATE DATABASE / USE ... ออก เพราะ user ส่วนใหญ่บน shared hosting สร้าง DB ผ่าน cPanel แล้ว
            $schemaSql = preg_replace('/CREATE\s+DATABASE.*?;/is',  '', $schemaSql);
            $schemaSql = preg_replace('/USE\s+\w+\s*;/is',           '', $schemaSql);
            $pdo->exec($schemaSql);

            $seedSql = file_get_contents($ROOT . '/database/02_seed.sql');
            $seedSql = preg_replace('/USE\s+\w+\s*;/is', '', $seedSql);
            $pdo->exec($seedSql);

            // Re-hash demo passwords (กรณี seed hash เก่าใช้ไม่ได้)
            $newHash = password_hash('password123', PASSWORD_BCRYPT);
            $pdo->exec("UPDATE users SET password_hash = " . $pdo->quote($newHash));

            $msg = '✅ Schema + Seed ถูก import แล้ว';
            header('Location: setup.php?step=4');
            exit;
        } catch (Throwable $e) {
            $err = 'Migrate ไม่สำเร็จ: ' . $e->getMessage();
        }
    }
    elseif ($step === 4 && !empty($_SESSION['db'])) {
        // Create / update admin user + write config files
        $adminEmail = trim($_POST['admin_email'] ?? '');
        $adminName  = trim($_POST['admin_name'] ?? '');
        $adminPass  = $_POST['admin_pass'] ?? '';
        $baseUrl    = trim($_POST['base_url'] ?? '');

        if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL) || strlen($adminPass) < 6) {
            $err = 'กรุณากรอก email ให้ถูก และ password อย่างน้อย 6 ตัวอักษร';
        } else {
            try {
                $db = $_SESSION['db'];
                $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                    $db['host'], $db['port'], $db['database']);
                $pdo = new PDO($dsn, $db['username'], $db['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);

                $hash = password_hash($adminPass, PASSWORD_BCRYPT);

                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$adminEmail]);
                $row = $stmt->fetch();
                if ($row) {
                    $u = $pdo->prepare("UPDATE users SET full_name=?, password_hash=?, role='admin', is_active=1 WHERE id=?");
                    $u->execute([$adminName, $hash, $row['id']]);
                } else {
                    $u = $pdo->prepare("INSERT INTO users (full_name,email,password_hash,role,company_id,department_id,is_active)
                                        VALUES (?,?,?,?,?,?,1)");
                    $u->execute([$adminName, $adminEmail, $hash, 'admin', 1, 3]);
                }

                // Write config/database.php
                $cfg = "<?php\nreturn " . var_export([
                    'driver'    => 'mysql',
                    'host'      => $db['host'],
                    'port'      => (int) $db['port'],
                    'database'  => $db['database'],
                    'username'  => $db['username'],
                    'password'  => $db['password'],
                    'charset'   => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'options'   => '__OPTIONS_PLACEHOLDER__',
                ], true) . ";\n";
                $cfg = str_replace("'__OPTIONS_PLACEHOLDER__'", '[
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]', $cfg);
                file_put_contents($ROOT . '/config/database.php', $cfg);

                // Update base_url ใน config.php
                $appCfg = file_get_contents($ROOT . '/config/config.php');
                $appCfg = preg_replace(
                    "/'base_url'\s*=>\s*'[^']*'/",
                    "'base_url'  => '" . addslashes($baseUrl) . "'",
                    $appCfg
                );
                $appCfg = preg_replace("/'debug'\s*=>\s*true/", "'debug'     => false", $appCfg);
                file_put_contents($ROOT . '/config/config.php', $appCfg);

                // Mark installed
                file_put_contents($ROOT . '/.installed', date('c') . " — installed by " . $adminEmail);

                unset($_SESSION['db']);
                header('Location: setup.php?step=5');
                exit;
            } catch (Throwable $e) {
                $err = 'สร้าง Admin ไม่สำเร็จ: ' . $e->getMessage();
            }
        }
    }
}

// =============== UI ===============
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Setup — Expense Memo System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f5f6f8; min-height: 100vh; padding: 30px 0; font-family: "Segoe UI", "Sarabun", Arial, sans-serif; }
        .wrap { max-width: 700px; margin: 0 auto; }
        .step-bar { display: flex; gap: 8px; margin-bottom: 25px; }
        .step-bar div { flex: 1; height: 6px; background: #d8dbe2; border-radius: 3px; }
        .step-bar div.done { background: #4f7cff; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="text-center mb-4">
        <div class="display-5"><i class="bi bi-receipt-cutoff text-primary"></i></div>
        <h3 class="mb-0">Expense Memo Setup</h3>
        <small class="text-muted">LOVE ISLAND / ANDAMAN SUNDAY</small>
    </div>

    <div class="step-bar">
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <div class="<?= $i <= $step ? 'done' : '' ?>"></div>
        <?php endfor; ?>
    </div>

    <?php if ($err): ?><div class="alert alert-danger"><?= h($err) ?></div><?php endif; ?>
    <?php if ($msg): ?><div class="alert alert-success"><?= h($msg) ?></div><?php endif; ?>

    <div class="card">
        <div class="card-body p-4">

        <?php if (isInstalled() && $step !== 5): ?>
            <div class="alert alert-warning">
                <strong>ระบบติดตั้งแล้ว</strong> — โปรดลบไฟล์ <code>setup.php</code> และ <code>.installed</code> หากต้องการ reset
            </div>
            <a href="<?= h($_SERVER['PHP_SELF']) ?>?step=5" class="btn btn-primary">ไปยังขั้นสุดท้าย</a>

        <?php elseif ($step === 1): ?>
            <h5>Step 1 — Pre-flight Check</h5>
            <p class="text-muted">ตรวจ environment ก่อนติดตั้ง</p>
            <table class="table">
                <?php
                $checks = [
                    ['PHP version ≥ 8.0',        version_compare(PHP_VERSION, '8.0', '>='), PHP_VERSION],
                    ['PDO MySQL extension',       extension_loaded('pdo_mysql'),             extension_loaded('pdo_mysql') ? 'yes' : 'no'],
                    ['mbstring extension',        extension_loaded('mbstring'),              extension_loaded('mbstring') ? 'yes' : 'no'],
                    ['fileinfo extension',        extension_loaded('fileinfo'),              extension_loaded('fileinfo') ? 'yes' : 'no'],
                    ['config/ writable',          is_writable($ROOT . '/config'),            is_writable($ROOT . '/config') ? 'yes' : 'no'],
                    ['storage/uploads writable',  is_writable($ROOT . '/storage/uploads') || is_writable($ROOT . '/storage'), 'check'],
                    ['database/01_schema.sql',    file_exists($ROOT . '/database/01_schema.sql'), file_exists($ROOT . '/database/01_schema.sql') ? 'found' : 'missing'],
                    ['database/02_seed.sql',      file_exists($ROOT . '/database/02_seed.sql'), file_exists($ROOT . '/database/02_seed.sql') ? 'found' : 'missing'],
                ];
                $allOk = true;
                foreach ($checks as $c) {
                    if (!$c[1]) $allOk = false;
                    echo '<tr><td>' . h($c[0]) . '</td><td class="text-end">';
                    echo $c[1] ? '<span class="text-success">✓ ' . h($c[2]) . '</span>'
                              : '<span class="text-danger">✗ ' . h($c[2]) . '</span>';
                    echo '</td></tr>';
                }
                ?>
            </table>
            <?php if ($allOk): ?>
                <a href="?step=2" class="btn btn-primary w-100">Next — Database Connection</a>
            <?php else: ?>
                <div class="alert alert-warning">โปรดแก้ปัญหาด้านบนก่อน (chmod 775 / ติดตั้ง php extension / re-upload ไฟล์)</div>
                <a href="?step=1" class="btn btn-light">Re-check</a>
                <a href="?step=2" class="btn btn-warning ms-2">ข้ามไป (ไม่แนะนำ)</a>
            <?php endif; ?>

        <?php elseif ($step === 2): ?>
            <h5>Step 2 — Database Connection</h5>
            <p class="text-muted">หา DB credentials จาก cPanel → MySQL Databases</p>
            <form method="post">
                <div class="mb-2"><label class="form-label">Host</label>
                    <input type="text" name="db_host" class="form-control" value="<?= h($_SESSION['db']['host'] ?? 'localhost') ?>" required></div>
                <div class="mb-2"><label class="form-label">Port</label>
                    <input type="number" name="db_port" class="form-control" value="<?= h($_SESSION['db']['port'] ?? 3306) ?>" required></div>
                <div class="mb-2"><label class="form-label">Database Name *</label>
                    <input type="text" name="db_name" class="form-control" placeholder="cpaneluser_expense_memo" value="<?= h($_SESSION['db']['database'] ?? '') ?>" required></div>
                <div class="mb-2"><label class="form-label">Database User *</label>
                    <input type="text" name="db_user" class="form-control" placeholder="cpaneluser_emm_app" value="<?= h($_SESSION['db']['username'] ?? '') ?>" required></div>
                <div class="mb-3"><label class="form-label">Database Password *</label>
                    <input type="password" name="db_pass" class="form-control" required></div>
                <button class="btn btn-primary w-100">Test Connection & Continue</button>
            </form>

        <?php elseif ($step === 3): ?>
            <h5>Step 3 — Import Schema & Seed Data</h5>
            <p class="text-muted">จะสร้าง 16 tables พร้อม seed Companies/Departments/Categories/Users</p>
            <div class="alert alert-warning">
                ⚠️ ขั้นนี้จะ <strong>DROP</strong> tables เดิม (ถ้ามี) และ import ใหม่
            </div>
            <form method="post">
                <button class="btn btn-warning w-100">Run Migration</button>
            </form>

        <?php elseif ($step === 4): ?>
            <h5>Step 4 — Create Admin User & Save Config</h5>
            <form method="post">
                <div class="mb-2"><label class="form-label">Base URL</label>
                    <input type="text" name="base_url" class="form-control" value="" placeholder="ปกติว่าง — ใส่เช่น /memo เมื่ออยู่ใน subfolder">
                    <small class="text-muted">ถ้า domain ชี้ไปที่ document root ของระบบ ให้เว้นว่าง</small></div>
                <div class="mb-2"><label class="form-label">Admin Name *</label>
                    <input type="text" name="admin_name" class="form-control" value="System Administrator" required></div>
                <div class="mb-2"><label class="form-label">Admin Email *</label>
                    <input type="email" name="admin_email" class="form-control" placeholder="admin@yourdomain.com" required></div>
                <div class="mb-3"><label class="form-label">Admin Password *</label>
                    <input type="password" name="admin_pass" class="form-control" minlength="6" required></div>
                <button class="btn btn-primary w-100">Create Admin & Finish</button>
            </form>

        <?php elseif ($step === 5): ?>
            <h2 class="text-success"><i class="bi bi-check-circle"></i> ติดตั้งสำเร็จ</h2>
            <p class="lead">ระบบพร้อมใช้งาน</p>
            <div class="alert alert-danger">
                <strong>⚠️ สำคัญมาก —</strong> โปรดลบไฟล์เหล่านี้ออกจาก server ทันที:
                <ul class="mb-0">
                    <li><code>setup.php</code> (ไฟล์นี้)</li>
                    <li><code>database/01_schema.sql</code></li>
                    <li><code>database/02_seed.sql</code></li>
                    <li><code>deploy/</code> folder (ทั้ง folder)</li>
                </ul>
            </div>
            <a href="login" class="btn btn-primary btn-lg w-100">เข้าสู่ระบบ</a>

        <?php endif; ?>

        </div>
    </div>

    <p class="text-center text-muted small mt-3">
        Expense Memo System — Setup wizard
    </p>
</div>
</body>
</html>

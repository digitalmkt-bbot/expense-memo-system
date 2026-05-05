<?php
/**
 * Auto-installer — รันบน startup ของ Railway/Container
 *
 * Logic:
 *   1. ทดสอบ DB connection
 *   2. ถ้าไม่มี table → run schema + seed
 *   3. ถ้าไม่มี admin user (ตาม ADMIN_EMAIL) → สร้างใหม่จาก env
 *   4. ปิดทำงานได้ด้วย AUTO_INSTALL=false
 *
 * ใช้กับ Dockerfile entrypoint หรือ Railway start command
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') exit("Auto-installer must run from CLI\n");

$root = dirname(__DIR__);
require $root . '/app/core/helpers.php';

if (!config('auto_install')) {
    echo "ℹ️  AUTO_INSTALL=false — skipping\n";
    exit(0);
}

$dbCfg = require $root . '/config/database.php';

echo "==========================================================\n";
echo "  Expense Memo — Auto-installer\n";
echo "  DB: {$dbCfg['database']} @ {$dbCfg['host']}:{$dbCfg['port']}\n";
echo "==========================================================\n\n";

// Wait for DB to be ready (Railway MySQL provisioning can take a moment)
$maxRetries = 30;
$pdo = null;
for ($i = 1; $i <= $maxRetries; $i++) {
    try {
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $dbCfg['host'], $dbCfg['port'], $dbCfg['database']);
        $pdo = new PDO($dsn, $dbCfg['username'], $dbCfg['password'], $dbCfg['options']);
        echo "✅ DB connection established (attempt $i)\n";
        break;
    } catch (Throwable $e) {
        echo "⏳ Waiting for DB... (attempt $i/$maxRetries): " . $e->getMessage() . "\n";
        if ($i === $maxRetries) {
            echo "❌ Could not connect to DB after $maxRetries attempts\n";
            exit(1);
        }
        sleep(2);
    }
}

// Check if schema exists
$hasSchema = false;
try {
    $pdo->query("SELECT 1 FROM companies LIMIT 1");
    $hasSchema = true;
} catch (Throwable $e) {
    $hasSchema = false;
}

if (!$hasSchema) {
    echo "\n🔵 Schema not found — installing...\n";

    $schemaSql = file_get_contents($root . '/database/01_schema.sql');
    $schemaSql = preg_replace('/CREATE\s+DATABASE.*?;/is', '', $schemaSql);
    $schemaSql = preg_replace('/USE\s+\w+\s*;/is', '', $schemaSql);
    $pdo->exec($schemaSql);
    echo "✅ Schema loaded\n";

    $seedSql = file_get_contents($root . '/database/02_seed.sql');
    $seedSql = preg_replace('/USE\s+\w+\s*;/is', '', $seedSql);
    $pdo->exec($seedSql);
    echo "✅ Seed data loaded (Companies, Departments, Categories, Demo users)\n";
} else {
    echo "\n✅ Schema exists — skipping migration\n";
}

// Setup admin user from ENV
$adminCfg = config('admin_seed');
$adminEmail = $adminCfg['email'];
$adminName  = $adminCfg['name'];
$adminPass  = $adminCfg['password'];

// Re-hash all demo users with bcrypt + 'password123' so login works
// (ใน production ลบ demo users ออกหลังทดสอบเสร็จ)
$demoHash = password_hash('password123', PASSWORD_BCRYPT);
$pdo->prepare("UPDATE users SET password_hash = ?
               WHERE email LIKE '%@loveandaman.com' AND email != ?")
    ->execute([$demoHash, $adminEmail]);

// Create / update admin
if (!$adminPass) {
    // generate random password
    $adminPass = bin2hex(random_bytes(8));   // 16 chars
    echo "\n⚠️  ADMIN_PASSWORD not set — generated random:\n";
    echo "    📧 Email:    $adminEmail\n";
    echo "    🔑 Password: $adminPass\n";
    echo "    💡 Set ADMIN_PASSWORD env var to use your own\n";
}

$hash = password_hash($adminPass, PASSWORD_BCRYPT);

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$adminEmail]);
$existing = $stmt->fetch();

if ($existing) {
    $pdo->prepare("UPDATE users SET full_name=?, password_hash=?, role='admin', is_active=1 WHERE id=?")
        ->execute([$adminName, $hash, $existing['id']]);
    echo "\n✅ Admin user updated: $adminEmail\n";
} else {
    $pdo->prepare("INSERT INTO users (full_name,email,password_hash,role,company_id,department_id,is_active)
                   VALUES (?,?,?,?,?,?,1)")
        ->execute([$adminName, $adminEmail, $hash, 'admin', 1, 3]);
    echo "\n✅ Admin user created: $adminEmail\n";
}

// Mark installed
file_put_contents($root . '/.installed', date('c') . " — auto-installed\n");

echo "\n==========================================================\n";
echo "  ✅ Auto-install complete\n";
echo "==========================================================\n\n";

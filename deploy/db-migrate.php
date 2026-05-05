<?php
/**
 * CLI Migration Script — รัน schema + seed บน server ที่มี SSH/CLI
 *   php deploy/db-migrate.php           # รัน schema + seed (ครั้งแรก)
 *   php deploy/db-migrate.php --schema  # รัน schema เท่านั้น
 *   php deploy/db-migrate.php --seed    # รัน seed เท่านั้น
 *   php deploy/db-migrate.php --check   # ตรวจ connection อย่างเดียว
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit("This script must be run from CLI\n");
}

$root = dirname(__DIR__);
$cfgPath = $root . '/config/database.php';
if (!file_exists($cfgPath)) {
    exit("❌ Database config not found: $cfgPath\n");
}
$cfg = require $cfgPath;

$args = array_slice($argv, 1);
$mode = 'all';
foreach ($args as $a) {
    if ($a === '--schema') $mode = 'schema';
    if ($a === '--seed')   $mode = 'seed';
    if ($a === '--check')  $mode = 'check';
}

echo "==================================================\n";
echo "  Migration: $mode\n";
echo "  DB: {$cfg['database']} @ {$cfg['host']}\n";
echo "==================================================\n\n";

try {
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        $cfg['host'], $cfg['port'], $cfg['database']);
    $pdo = new PDO($dsn, $cfg['username'], $cfg['password'], $cfg['options'] ?? [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "✅ Connection OK\n";
} catch (Throwable $e) {
    exit("❌ Connection failed: " . $e->getMessage() . "\n");
}

if ($mode === 'check') exit("\n✅ Check complete\n");

if ($mode === 'all' || $mode === 'schema') {
    echo "\n🔵 Loading schema...\n";
    $sql = file_get_contents($root . '/database/01_schema.sql');
    $sql = preg_replace('/CREATE\s+DATABASE.*?;/is', '', $sql);
    $sql = preg_replace('/USE\s+\w+\s*;/is', '', $sql);
    $pdo->exec($sql);
    echo "✅ Schema loaded\n";
}

if ($mode === 'all' || $mode === 'seed') {
    echo "\n🔵 Loading seed...\n";
    $sql = file_get_contents($root . '/database/02_seed.sql');
    $sql = preg_replace('/USE\s+\w+\s*;/is', '', $sql);
    $pdo->exec($sql);
    // Re-hash demo passwords
    $hash = password_hash('password123', PASSWORD_BCRYPT);
    $pdo->exec("UPDATE users SET password_hash = " . $pdo->quote($hash));
    echo "✅ Seed loaded — demo password = password123\n";
}

echo "\n==================================================\n";
echo "  ✅ Migration complete\n";
echo "==================================================\n";

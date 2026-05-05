<?php
namespace App\Core;

use PDO;
use PDOException;

/**
 * Singleton PDO wrapper — บางทีใช้เป็น helper หลักของระบบ
 */
class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $cfg = require __DIR__ . '/../../config/database.php';
        $dsn = sprintf(
            '%s:host=%s;port=%d;dbname=%s;charset=%s',
            $cfg['driver'], $cfg['host'], $cfg['port'], $cfg['database'], $cfg['charset']
        );

        try {
            self::$pdo = new PDO($dsn, $cfg['username'], $cfg['password'], $cfg['options']);
        } catch (PDOException $e) {
            http_response_code(500);
            die('Database connection failed: ' . $e->getMessage());
        }

        return self::$pdo;
    }

    /** SELECT รายการ */
    public static function select(string $sql, array $params = []): array
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** SELECT 1 record */
    public static function selectOne(string $sql, array $params = []): ?array
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** INSERT/UPDATE/DELETE */
    public static function execute(string $sql, array $params = []): int
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public static function insert(string $sql, array $params = []): string
    {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        return self::connection()->lastInsertId();
    }

    public static function beginTransaction(): void { self::connection()->beginTransaction(); }
    public static function commit(): void          { self::connection()->commit(); }
    public static function rollBack(): void        { self::connection()->rollBack(); }
}

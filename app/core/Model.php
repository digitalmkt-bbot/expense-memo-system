<?php
namespace App\Core;

abstract class Model
{
    protected static string $table = '';
    protected static string $primary = 'id';

    public static function all(string $orderBy = 'id ASC'): array
    {
        return Database::select("SELECT * FROM " . static::$table . " ORDER BY $orderBy");
    }

    public static function find($id): ?array
    {
        return Database::selectOne(
            "SELECT * FROM " . static::$table . " WHERE " . static::$primary . " = ? LIMIT 1",
            [$id]
        );
    }

    public static function where(string $col, $value): array
    {
        return Database::select(
            "SELECT * FROM " . static::$table . " WHERE $col = ?",
            [$value]
        );
    }

    public static function create(array $data): string
    {
        $cols = array_keys($data);
        $placeholders = array_fill(0, count($cols), '?');
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            static::$table,
            implode(',', $cols),
            implode(',', $placeholders)
        );
        return Database::insert($sql, array_values($data));
    }

    public static function update($id, array $data): int
    {
        $set = implode(',', array_map(fn($c) => "$c = ?", array_keys($data)));
        $sql = sprintf('UPDATE %s SET %s WHERE %s = ?',
            static::$table, $set, static::$primary);
        $params = array_values($data);
        $params[] = $id;
        return Database::execute($sql, $params);
    }

    public static function delete($id): int
    {
        return Database::execute(
            "DELETE FROM " . static::$table . " WHERE " . static::$primary . " = ?",
            [$id]
        );
    }
}

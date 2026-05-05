<?php
namespace App\Core;

class Auth
{
    public static function login(array $user): void
    {
        $_SESSION['user'] = [
            'id'            => $user['id'],
            'full_name'     => $user['full_name'],
            'email'         => $user['email'],
            'role'          => $user['role'],
            'company_id'    => $user['company_id'],
            'department_id' => $user['department_id'],
        ];
        $_SESSION['logged_in_at'] = time();
    }

    public static function logout(): void
    {
        unset($_SESSION['user'], $_SESSION['logged_in_at']);
        session_destroy();
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int
    {
        return $_SESSION['user']['id'] ?? null;
    }

    public static function role(): ?string
    {
        return $_SESSION['user']['role'] ?? null;
    }

    public static function hasRole(string|array $roles): bool
    {
        $roles = (array) $roles;
        return in_array(self::role(), $roles, true);
    }

    public static function require(): void
    {
        if (!self::check()) {
            $cfg = require __DIR__ . '/../../config/config.php';
            header('Location: ' . rtrim($cfg['app']['base_url'], '/') . '/login');
            exit;
        }
    }

    public static function requireRole(string|array $roles): void
    {
        self::require();
        if (!self::hasRole($roles)) {
            http_response_code(403);
            echo '<h1>403 Forbidden</h1><p>You do not have permission to access this resource.</p>';
            exit;
        }
    }
}

<?php
namespace App\Core;

class Controller
{
    /**
     * Render view (ใช้ PHP template ปกติ)
     */
    protected function view(string $view, array $data = [], string $layout = 'layouts/main'): void
    {
        extract($data);
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        $layoutPath = __DIR__ . '/../views/' . $layout . '.php';

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        if (file_exists($layoutPath)) {
            require $layoutPath;
        } else {
            echo $content;
        }
    }

    protected function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function redirect(string $url): void
    {
        $cfg = require __DIR__ . '/../../config/config.php';
        $base = rtrim($cfg['app']['base_url'], '/');
        if (!str_starts_with($url, 'http')) {
            $url = $base . $url;
        }
        header('Location: ' . $url);
        exit;
    }

    protected function input(string $key, $default = null)
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function flash(string $type, string $message): void
    {
        $_SESSION['_flash'][$type] = $message;
    }

    protected function abort(int $code, string $message = ''): void
    {
        http_response_code($code);
        echo '<h1>' . $code . '</h1><p>' . htmlspecialchars($message) . '</p>';
        exit;
    }
}

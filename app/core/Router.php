<?php
namespace App\Core;

/**
 * Tiny Router — รองรับ Method + Pattern + named param เช่น /memos/{id}
 */
class Router
{
    protected array $routes = [];

    public function get(string $path, $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    public function any(array $methods, string $path, $handler, array $middleware = []): void
    {
        foreach ($methods as $m) $this->add(strtoupper($m), $path, $handler, $middleware);
    }

    private function add(string $method, string $path, $handler, array $middleware): void
    {
        $this->routes[] = [
            'method'     => $method,
            'path'       => $path,
            'handler'    => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = parse_url($uri, PHP_URL_PATH) ?? '/';
        // strip base_url prefix
        $cfg = require __DIR__ . '/../../config/config.php';
        $base = rtrim($cfg['app']['base_url'], '/');
        if ($base && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }
        if ($uri === '' || $uri === false) $uri = '/';

        foreach ($this->routes as $r) {
            if ($r['method'] !== $method) continue;
            $pattern = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $r['path']);
            $pattern = '#^' . $pattern . '$#';
            if (preg_match($pattern, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                // Run middleware
                foreach ($r['middleware'] as $mw) {
                    if (is_callable($mw)) $mw();
                }
                $this->call($r['handler'], $params);
                return;
            }
        }

        http_response_code(404);
        echo '<h1>404 Not Found</h1><p>URI: ' . htmlspecialchars($uri) . '</p>';
    }

    private function call($handler, array $params): void
    {
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }
        if (is_string($handler) && str_contains($handler, '@')) {
            [$class, $method] = explode('@', $handler);
            $fqcn = 'App\\Controllers\\' . $class;
            $obj = new $fqcn();
            call_user_func_array([$obj, $method], $params);
            return;
        }
        throw new \RuntimeException('Invalid route handler');
    }
}

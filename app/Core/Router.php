<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function add(string $method, string $route, array $handler): void
    {
        $route = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route);
        $route = '#^' . $route . '$#s';
        $this->routes[strtoupper($method)][$route] = $handler;
    }

    public function dispatch(string $url, string $method): void
    {
        $url = parse_url($url, PHP_URL_PATH);

        if (defined('BASE_PATH') && BASE_PATH !== '') {
            if (strpos($url, BASE_PATH) === 0) {
                $url = substr($url, strlen(BASE_PATH));
            }
        }

        $url = trim($url, '/');
        $method = strtoupper($method);

        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            if (preg_match($route, $url, $matches)) {
                [$controllerClass, $action] = $handler;

                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass();

                    if (method_exists($controller, $action)) {
                        $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                        call_user_func_array([$controller, $action], $params);
                        return;
                    }
                }
            }
        }

        $this->abort();
    }

    private function abort(): void
    {
        if (ob_get_length()) {
            ob_clean();
        }

        $response = new \App\Core\Response();

        ob_start();
        require __DIR__ . '/../../views/errors/404.php';
        $html = ob_get_clean();

        $response->setStatus(404)
            ->addHeader('Content-Type: text/html; charset=utf-8')
            ->send($html);
    }
}
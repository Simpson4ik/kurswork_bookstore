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

        $basePath = defined('BASE_PATH') ? BASE_PATH : '';
        if ($basePath !== '') {
            if ($url === $basePath) {
                $url = '';
            } elseif (strpos($url, $basePath . '/') === 0) {
                $url = substr($url, strlen($basePath));
            }
        }

        $url = trim($url, '/');
        $method = strtoupper($method);

        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            if (preg_match($route, $url, $matches)) {
                [$controllerClass, $action] = $handler;

                if (!class_exists($controllerClass)) {
                    throw new \RuntimeException("Target controller class '{$controllerClass}' not found for route '{$url}'");
                }

                $controller = new $controllerClass();

                if (!method_exists($controller, $action)) {
                    throw new \RuntimeException("Action method '{$action}' not found in controller '{$controllerClass}'");
                }

                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                call_user_func_array([$controller, $action], $params);
                return;
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
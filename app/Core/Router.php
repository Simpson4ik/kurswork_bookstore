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
        $url = trim(parse_url($url, PHP_URL_PATH), '/');
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
        http_response_code(404);
        echo "404 - Page Not Found";
        exit;
    }
}
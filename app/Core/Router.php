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
        $response = new \App\Core\Response();

        $html = "
            <body style='background:#0b0f19; color:#f8fafc; font-family:sans-serif; text-align:center; padding-top:100px;'>
                <h1 style='color:#38bdf8; font-size:48px;'>404</h1>
                <h2>Сторінку не знайдено</h2>
                <p style='color:#94a3b8;'>На жаль, такої сторінки або книги не існує.</p>
                <a href='/coursework/' style='color:#2563eb; text-decoration:none; font-weight:bold;'>&larr; На головну сторінку</a>
            body>
        ";

        $response->setStatus(404)
            ->addHeader('Content-Type: text/html; charset=utf-8')
            ->send($html);
    }
}
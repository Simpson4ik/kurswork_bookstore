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
        $base = defined('BASE_PATH') ? BASE_PATH : '';

        $html = "<!DOCTYPE html><html lang='uk'><head><meta charset='UTF-8'><title>404 Сторінку не знайдено</title></head><body style='background:#0b0f19; color:#f8fafc; font-family:system-ui, sans-serif; text-align:center; padding-top:100px;'><h1 style='color:#38bdf8; font-size:64px; margin-bottom:10px;'>404</h1><h2 style='font-size:24px; margin-bottom:15px;'>Сторінку не знайдено</h2><p style='color:#94a3b8; max-width:500px; margin:0 auto 30px auto; line-height:1.6;'>На жаль, запитуваної книги або космічної станції не існує в нашій системі координат.</p><a href='{$base}/' style='display:inline-block; background:#2563eb; color:#fff; text-decoration:none; padding:12px 24px; font-weight:600; border-radius:8px;'>&larr; На головну сторінку</a></body></html>";

        $response->setStatus(404)
            ->addHeader('Content-Type: text/html; charset=utf-8')
            ->send($html);
    }
}
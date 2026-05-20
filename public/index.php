<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';
    $len = strlen($prefix);

    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

$router = new \App\Core\Router();

$router->add('GET', '', [\App\Controllers\HomeController::class, 'index']);
$router->add('GET', 'books', [\App\Controllers\BookController::class, 'index']);
$router->add('GET', 'book/{id}', [\App\Controllers\BookController::class, 'show']);

$router->add('GET', 'register', [\App\Controllers\AuthController::class, 'register']);
$router->add('POST', 'register/store', [\App\Controllers\AuthController::class, 'storeRegister']);
$router->add('GET', 'login', [\App\Controllers\AuthController::class, 'login']);
$router->add('POST', 'login/authenticate', [\App\Controllers\AuthController::class, 'authenticate']);
$router->add('GET', 'logout', [\App\Controllers\AuthController::class, 'logout']);

$router->add('GET', 'admin/dashboard', [\App\Controllers\Admin\DashboardController::class, 'index']);
$router->add('GET', 'admin/book/add', [\App\Controllers\Admin\BookController::class, 'create']);
$router->add('POST', 'admin/book/store', [\App\Controllers\Admin\BookController::class, 'store']);

$router->add('GET', 'cart', [\App\Controllers\CartController::class, 'index']);
$router->add('GET', 'cart/add/{id}', [\App\Controllers\CartController::class, 'add']);
$router->add('GET', 'cart/remove/{id}', [\App\Controllers\CartController::class, 'remove']);
$router->add('POST', 'cart/update', [\App\Controllers\CartController::class, 'update']);
$router->add('GET', 'cart/checkout', [\App\Controllers\CartController::class, 'checkout']);

$url = $_GET['url'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($url, $method);
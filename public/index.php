<?php

ob_start();

$config = require __DIR__ . '/../config/app.php';

if ($config['env'] === 'dev') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);

    $logDir = __DIR__ . '/../storage/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }

    ini_set('log_errors', 1);
    ini_set('error_log', $logDir . '/error.log');
}

define('BASE_PATH', $config['base_path']);

$isProd = ($config['env'] === 'prod');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => BASE_PATH ?: '/',
    'domain' => $_SERVER['SERVER_NAME'] ?? '',
    'secure' => $isProd,
    'httponly' => true,
    'samesite' => 'Strict'
]);

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

if (!isset($_SESSION['user']) && isset($_COOKIE['remember_me'])) {
    $customerModel = new \App\Models\Customer();
    $user = $customerModel->getByRememberToken($_COOKIE['remember_me']);
    if ($user) {
        $_SESSION['user'] = [
            'id' => $user['customer_id'],
            'name' => $user['first_name'],
            'role' => $user['role']
        ];
    } else {
        setcookie('remember_me', '', time() - 3600, '/');
    }
}

if (isset($_SESSION['user']) && !isset($_SESSION['cart_loaded_from_db'])) {
    $cartModel = new \App\Models\Cart();
    $dbCart = $cartModel->getByCustomerId((int)$_SESSION['user']['id']);

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    foreach ($_SESSION['cart'] as $bookId => $quantity) {
        $cartModel->saveItem((int)$_SESSION['user']['id'], (int)$bookId, (int)$quantity);
    }

    foreach ($dbCart as $bookId => $quantity) {
        if (!isset($_SESSION['cart'][$bookId])) {
            $_SESSION['cart'][$bookId] = $quantity;
        }
    }

    $_SESSION['cart_loaded_from_db'] = true;
}

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
$router->add('GET', 'admin/book/edit/{id}', [\App\Controllers\Admin\BookController::class, 'edit']);
$router->add('POST', 'admin/book/update/{id}', [\App\Controllers\Admin\BookController::class, 'update']);
$router->add('GET', 'admin/book/delete/{id}', [\App\Controllers\Admin\BookController::class, 'delete']);
$router->add('GET', 'admin/publishers', [\App\Controllers\Admin\PublisherController::class, 'index']);
$router->add('POST', 'admin/publishers/store', [\App\Controllers\Admin\PublisherController::class, 'store']);
$router->add('GET', 'admin/authors', [\App\Controllers\Admin\AuthorController::class, 'index']);
$router->add('POST', 'register/check-email', [\App\Controllers\AuthController::class, 'checkEmailAjax']);
$router->add('POST', 'admin/authors/store', [\App\Controllers\Admin\AuthorController::class, 'store']);
$router->add('GET', 'admin/genres', [\App\Controllers\Admin\GenreController::class, 'index']);
$router->add('POST', 'admin/genres/store', [\App\Controllers\Admin\GenreController::class, 'store']);

$router->add('GET', 'cart', [\App\Controllers\CartController::class, 'index']);
$router->add('GET', 'cart/add/{id}', [\App\Controllers\CartController::class, 'add']);
$router->add('GET', 'cart/remove/{id}', [\App\Controllers\CartController::class, 'remove']);
$router->add('POST', 'cart/update', [\App\Controllers\CartController::class, 'update']);
$router->add('POST', 'cart/checkout', [\App\Controllers\CartController::class, 'checkout']);
$router->add('GET', 'books/search', [\App\Controllers\BookController::class, 'searchAjax']);

$router->add('POST', 'cart/add-ajax', [\App\Controllers\CartController::class, 'addAjax']);
$router->add('POST', 'cart/update-ajax', [\App\Controllers\CartController::class, 'updateAjax']);
$router->add('POST', 'cart/remove-ajax', [\App\Controllers\CartController::class, 'removeAjax']);

$router->add('GET', 'orders', [\App\Controllers\ProfileController::class, 'orders']);
$router->add('GET', 'profile', [\App\Controllers\ProfileController::class, 'index']);
$router->add('POST', 'profile/update-ajax', [\App\Controllers\ProfileController::class, 'updateAjax']);

$router->add('GET', 'admin/orders', [\App\Controllers\Admin\OrderController::class, 'index']);
$router->add('GET', 'admin/orders/view/{id}', [\App\Controllers\Admin\OrderController::class, 'show']);
$router->add('POST', 'admin/orders/update-status-ajax', [\App\Controllers\Admin\OrderController::class, 'updateStatusAjax']);

$url = $_GET['url'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

try {
    $router->dispatch($url, $method);
} catch (\Throwable $e) {
    if (ob_get_length()) {
        ob_clean();
    }

    $logDir = __DIR__ . '/../storage/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }
    error_log($e->getMessage() . "\n" . $e->getTraceAsString());

    $response = new \App\Core\Response();

    $html = "<!DOCTYPE html><html lang='uk'><head><meta charset='UTF-8'><title>500 Внутрішня помилка сервера</title></head><body style='background:#0b0f19; color:#f8fafc; font-family:system-ui, sans-serif; text-align:center; padding-top:100px;'><h1 style='color:#ef4444; font-size:64px; margin-bottom:10px;'>500</h1><h2 style='font-size:24px; margin-bottom:15px;'>Внутрішня помилка сервера</h2><p style='color:#94a3b8; max-width:500px; margin:0 auto 30px auto; line-height:1.6;'>Сталася непередбачувана аномалія в системі розрахунків. Наші інженери вже усувають несправність.</p><a href='/coursework/' style='display:inline-block; background:#2563eb; color:#fff; text-decoration:none; padding:12px 24px; font-weight:600; border-radius:8px;'>&larr; Повернутися на головну</a></body></html>";

    if ($config['env'] === 'dev') {
        $html .= "<pre style='text-align:left; max-width:800px; margin:20px auto; background:#161f33; padding:20px; border-radius:8px; color:#ef4444; overflow-x:auto;'>" . htmlspecialchars($e->getMessage()) . "\n" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }

    $response->setStatus(500)
        ->addHeader('Content-Type: text/html; charset=utf-8')
        ->send($html);
}
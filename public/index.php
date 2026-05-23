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
$router->add('GET', 'cart/checkout', [\App\Controllers\CartController::class, 'checkout']);
$router->add('GET', 'books/search', [\App\Controllers\BookController::class, 'searchAjax']);

$router->add('POST', 'cart/add-ajax', [\App\Controllers\CartController::class, 'addAjax']);
$router->add('POST', 'cart/update-ajax', [\App\Controllers\CartController::class, 'updateAjax']);
$router->add('POST', 'cart/remove-ajax', [\App\Controllers\CartController::class, 'removeAjax']);

$router->add('GET', 'orders', [\App\Controllers\ProfileController::class, 'orders']);
$router->add('GET', 'profile', [\App\Controllers\ProfileController::class, 'index']);
$router->add('POST', 'profile/update-ajax', [\App\Controllers\ProfileController::class, 'updateAjax']);


$url = $_GET['url'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($url, $method);
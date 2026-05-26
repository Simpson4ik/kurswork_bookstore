<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Book;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Order;
use App\Core\Response;

class CartController extends Controller
{
    public function index(): void
    {
        $cart = $_SESSION['cart'] ?? [];
        $cartItems = [];
        $totalPrice = 0;

        if (!empty($cart)) {
            $bookModel = new Book();
            $cartModel = new Cart();
            $customerId = isset($_SESSION['user']) ? (int)$_SESSION['user']['id'] : 0;

            foreach ($cart as $bookId => $quantity) {
                $book = $bookModel->getById((int)$bookId);

                if ($book) {
                    // FIX 1: Жорстке обмеження кількості (не більше, ніж зараз є на складі)
                    if ($quantity > $book['stock_quantity']) {
                        $quantity = $book['stock_quantity'];
                        $_SESSION['cart'][$bookId] = $quantity;
                        if ($customerId > 0) {
                            $cartModel->saveItem($customerId, $bookId, $quantity);
                        }
                    }

                    // Додаємо до кошика на вивід тільки якщо товару > 0
                    if ($quantity > 0) {
                        $book['quantity'] = $quantity;
                        $book['subtotal'] = $book['price'] * $quantity;
                        $totalPrice += $book['subtotal'];
                        $cartItems[] = $book;
                    } else {
                        // Товар закінчився - видаляємо з кошика
                        unset($_SESSION['cart'][$bookId]);
                        if ($customerId > 0) {
                            $cartModel->removeItem($customerId, $bookId);
                        }
                    }
                } else {
                    // FIX 2: Книгу було видалено з каталогу - повністю прибираємо її з кошика
                    unset($_SESSION['cart'][$bookId]);
                    if ($customerId > 0) {
                        $cartModel->removeItem($customerId, $bookId);
                    }
                }
            }
        }

        $userData = null;
        if (isset($_SESSION['user'])) {
            $customerModel = new Customer();
            $userData = $customerModel->getById((int)$_SESSION['user']['id']);
        }

        $this->view('cart', [
            'title' => 'Мій кошик',
            'cartItems' => $cartItems,
            'totalPrice' => $totalPrice,
            'user' => $userData
        ]);
    }

    public function checkout(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('cart');
        }

        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            $this->redirect('login');
        }

        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            $this->redirect('cart');
        }

        $firstName = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
        $lastName = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

        $customerId = (int)$_SESSION['user']['id'];
        $customerModel = new Customer();
        $userRow = $customerModel->getById($customerId);

        $bookModel = new Book();
        $cartModel = new Cart();
        $cartItems = [];
        $totalPrice = 0;

        foreach ($cart as $bookId => $quantity) {
            $book = $bookModel->getById((int)$bookId);

            if ($book) {
                if ($quantity > $book['stock_quantity']) {
                    $quantity = $book['stock_quantity'];
                }

                if ($quantity > 0) {
                    $book['quantity'] = $quantity;
                    $book['subtotal'] = $book['price'] * $quantity;
                    $totalPrice += $book['subtotal'];
                    $cartItems[] = $book;
                }
            } else {
                // Якщо книгу видалили прямо під час оформлення
                unset($_SESSION['cart'][$bookId]);
                $cartModel->removeItem($customerId, $bookId);
            }
        }

        if (empty($cartItems)) {
            $this->redirect('cart');
            return;
        }

        if (empty($firstName) || empty($lastName) || empty($phone)) {
            http_response_code(400);
            $this->view('cart', [
                'title' => 'Мій кошик',
                'cartItems' => $cartItems,
                'totalPrice' => $totalPrice,
                'user' => $userRow,
                'error' => 'Заповніть усі обов\'язкові поля форми доставки.'
            ]);
            exit;
        }

        if ($userRow) {
            $customerModel->updateProfile($customerId, [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone,
                'email' => $userRow['email']
            ]);
            $_SESSION['user']['name'] = $firstName;
        }

        $orderModel = new Order();

        if ($orderModel->saveOrder($customerId, $cartItems, $totalPrice)) {
            $cartModel->clear($customerId);
            unset($_SESSION['cart']);
            $this->view('order_success', ['title' => 'Замовлення оформлено!']);
        } else {
            http_response_code(500);
            $this->layout = '';
            $this->view('errors/500');
            exit;
        }
    }

    public function addAjax(): void
    {
        $response = new Response();
        $input = json_decode(file_get_contents('php://input'), true);
        $bookId = isset($input['book_id']) ? (int)$input['book_id'] : 0;

        if ($bookId <= 0) {
            $response->json(['success' => false, 'message' => 'Некоректний ID книги'], 400);
        }

        $bookModel = new Book();
        $book = $bookModel->getById($bookId);
        if (!$book) {
            $response->json(['success' => false, 'message' => 'Книгу не знайдено'], 404);
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $currentQty = $_SESSION['cart'][$bookId] ?? 0;
        if ($currentQty >= $book['stock_quantity']) {
            $response->json(['success' => false, 'message' => 'Вибачте, більше немає в наявності на складі'], 400);
        }

        $_SESSION['cart'][$bookId] = $currentQty + 1;

        if (isset($_SESSION['user'])) {
            $cartModel = new Cart();
            $cartModel->saveItem((int)$_SESSION['user']['id'], $bookId, $_SESSION['cart'][$bookId]);
        }

        $totalItems = array_sum($_SESSION['cart']);

        $response->json([
            'success' => true,
            'message' => 'Книгу додано до кошика!',
            'total_items' => $totalItems
        ]);
    }

    public function updateAjax(): void
    {
        $response = new Response();
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['book_id']) || !isset($input['quantity'])) {
            $response->json(['success' => false, 'message' => 'Некоректні дані запиту'], 400);
        }

        $bookId = (int)$input['book_id'];
        $quantity = (int)$input['quantity'];

        $bookModel = new Book();
        $book = $bookModel->getById($bookId);
        if (!$book) {
            $response->json(['success' => false, 'message' => 'Книгу не знайдено в базі'], 404);
        }

        if ($quantity <= 0) {
            unset($_SESSION['cart'][$bookId]);
            $quantity = 0;
            $subtotal = 0;

            if (isset($_SESSION['user'])) {
                $cartModel = new Cart();
                $cartModel->removeItem((int)$_SESSION['user']['id'], $bookId);
            }
        } else {
            if ($quantity > $book['stock_quantity']) {
                $quantity = $book['stock_quantity'];
            }
            $_SESSION['cart'][$bookId] = $quantity;
            $subtotal = $book['price'] * $quantity;

            if (isset($_SESSION['user'])) {
                $cartModel = new Cart();
                $cartModel->saveItem((int)$_SESSION['user']['id'], $bookId, $quantity);
            }
        }

        $totalPrice = $this->calculateTotalPrice($bookModel);

        $response->json([
            'success' => true,
            'quantity' => $quantity,
            'subtotal' => $subtotal . ' грн',
            'total_price' => $totalPrice . ' грн',
            'cart_empty' => empty($_SESSION['cart'])
        ]);
    }

    public function removeAjax(): void
    {
        $response = new Response();
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['book_id'])) {
            $response->json(['success' => false, 'message' => 'Некоректні дані запиту'], 400);
        }

        $bookId = (int)$input['book_id'];

        if (isset($_SESSION['cart'][$bookId])) {
            unset($_SESSION['cart'][$bookId]);
        }

        if (isset($_SESSION['user'])) {
            $cartModel = new Cart();
            $cartModel->removeItem((int)$_SESSION['user']['id'], $bookId);
        }

        $bookModel = new Book();
        $totalPrice = $this->calculateTotalPrice($bookModel);

        $response->json([
            'success' => true,
            'total_price' => $totalPrice . ' грн',
            'cart_empty' => empty($_SESSION['cart'])
        ]);
    }

    private function calculateTotalPrice(Book $bookModel): float
    {
        $totalPrice = 0;
        foreach (($_SESSION['cart'] ?? []) as $id => $qty) {
            $b = $bookModel->getById((int)$id);
            if ($b) {
                $totalPrice += $b['price'] * $qty;
            }
        }
        return $totalPrice;
    }
}
<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Book;

class CartController extends Controller
{
    public function index(): void
    {
        $cart = $_SESSION['cart'] ?? [];
        $cartItems = [];
        $totalPrice = 0;

        if (!empty($cart)) {
            $bookModel = new Book();
            foreach ($cart as $bookId => $quantity) {
                $book = $bookModel->getById((int)$bookId);
                if ($book) {
                    $book['quantity'] = $quantity;
                    $book['subtotal'] = $book['price'] * $quantity;
                    $totalPrice += $book['subtotal'];
                    $cartItems[] = $book;
                }
            }
        }

        $this->view('cart', [
            'title' => 'Мій кошик',
            'cartItems' => $cartItems,
            'totalPrice' => $totalPrice
        ]);
    }

    public function add(string $id): void
    {
        $bookId = (int)$id;

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$bookId])) {
            $_SESSION['cart'][$bookId]++;
        } else {
            $_SESSION['cart'][$bookId] = 1;
        }

        header('Location: /coursework/cart');
        exit;
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantities'])) {
            $bookModel = new Book();

            foreach ($_POST['quantities'] as $bookId => $quantity) {
                $bookId = (int)$bookId;
                $quantity = (int)$quantity;

                if ($quantity <= 0) {
                    unset($_SESSION['cart'][$bookId]);
                } else {
                    $book = $bookModel->getById($bookId);
                    if ($book) {
                        if ($quantity > $book['stock_quantity']) {
                            $_SESSION['cart'][$bookId] = $book['stock_quantity'];
                        } else {
                            $_SESSION['cart'][$bookId] = $quantity;
                        }
                    }
                }
            }
        }

        header('Location: /coursework/cart');
        exit;
    }

    public function remove(string $id): void
    {
        $bookId = (int)$id;

        if (isset($_SESSION['cart'][$bookId])) {
            unset($_SESSION['cart'][$bookId]);
        }

        header('Location: /coursework/cart');
        exit;
    }

    public function checkout(): void
    {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            header('Location: /coursework/login');
            exit;
        }

        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            header('Location: /coursework/cart');
            exit;
        }

        $bookModel = new Book();
        $cartItems = [];
        $totalPrice = 0;

        foreach ($cart as $bookId => $quantity) {
            $book = $bookModel->getById((int)$bookId);
            if ($book) {
                $book['quantity'] = $quantity;
                $totalPrice += $book['price'] * $quantity;
                $cartItems[] = $book;
            }
        }

        $orderModel = new \App\Models\Order();
        $customerId = $_SESSION['user']['id'];

        if ($orderModel->saveOrder($customerId, $cartItems, $totalPrice)) {
            unset($_SESSION['cart']);
            $this->view('order_success', ['title' => 'Замовлення оформлено!']);
        } else {
            $response = new \App\Core\Response();
            $response->setStatus(500)->send("<h2>Помилка сервера</h2><p>Не вдалося зберегти замовлення через критичну помилку бази даних. Спробуйте пізніше.</p>");
        }
    }

    public function addAjax(): void
    {
        $response = new \App\Core\Response();
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

        if (isset($_SESSION['cart'][$bookId])) {
            $_SESSION['cart'][$bookId]++;
        } else {
            $_SESSION['cart'][$bookId] = 1;
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
        $response = new \App\Core\Response();
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
        } else {
            if ($quantity > $book['stock_quantity']) {
                $quantity = $book['stock_quantity'];
            }
            $_SESSION['cart'][$bookId] = $quantity;
            $subtotal = $book['price'] * $quantity;
        }

        $totalPrice = 0;
        foreach (($_SESSION['cart'] ?? []) as $id => $qty) {
            $b = $bookModel->getById((int)$id);
            if ($b) {
                $totalPrice += $b['price'] * $qty;
            }
        }

        $response->json([
            'success' => true,
            'quantity' => $quantity,
            'subtotal' => $subtotal . ' грн',
            'total_price' => $totalPrice . ' грн',
            'cart_empty' => empty($_SESSION['cart'])
        ]);
    }
}
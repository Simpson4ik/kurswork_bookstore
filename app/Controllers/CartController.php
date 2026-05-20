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
            die("Критична помилка бази даних при оформленні замовлення.");
        }
    }
}
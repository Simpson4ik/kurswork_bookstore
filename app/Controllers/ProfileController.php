<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;

class ProfileController extends Controller
{
    public function orders(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /coursework/login');
            exit;
        }

        $orderModel = new Order();
        $orders = $orderModel->getByCustomerId((int)$_SESSION['user']['id']);

        $this->view('orders', [
            'title' => 'Мої замовлення',
            'orders' => $orders
        ]);
    }
}
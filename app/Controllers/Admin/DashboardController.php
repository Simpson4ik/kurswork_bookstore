<?php

namespace App\Controllers\Admin;

use App\Models\Book;
use App\Models\Order;
use App\Models\Customer;

class DashboardController extends AdminController
{
    public function index(): void
    {
        $bookModel = new Book();
        $orderModel = new Order();
        $customerModel = new Customer();

        $stats = [
            'total_books' => $bookModel->getTotalCount(),
            'total_orders' => $orderModel->getTotalCount(),
            'total_revenue' => $orderModel->getTotalRevenue(),
            'total_customers' => $customerModel->getTotalCount()
        ];

        $books = $bookModel->getAll();

        $this->view('admin/dashboard', [
            'title' => 'Панель адміністратора',
            'stats' => $stats, // Передаємо масив зі статистикою у шаблон
            'books' => $books
        ]);
    }
}
<?php

namespace App\Controllers\Admin;

use App\Models\Book;

class DashboardController extends AdminController
{
    public function index(): void
    {
        $bookModel = new Book();
        $books = $bookModel->getAll();

        $this->view('admin/dashboard', [
            'title' => 'Панель адміністратора',
            'books' => $books
        ]);
    }
}
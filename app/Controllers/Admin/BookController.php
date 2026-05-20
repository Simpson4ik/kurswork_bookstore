<?php

namespace App\Controllers\Admin;

use App\Models\Book;
use App\Models\Publisher;

class BookController extends AdminController
{
    public function create(): void
    {
        $publisherModel = new Publisher();
        $publishers = $publisherModel->getAll();

        $this->view('admin/add_book', [
            'title' => 'Додати нову книгу',
            'publishers' => $publishers
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookModel = new Book();
            $bookModel->create($_POST);

            header('Location: /coursework/');
            exit;
        }
    }
}
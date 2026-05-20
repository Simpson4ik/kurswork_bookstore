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

            header('Location: /coursework/admin/dashboard');
            exit;
        }
    }

    public function edit(string $id): void
    {
        $bookModel = new Book();
        $book = $bookModel->getById((int)$id);

        if (!$book) {
            http_response_code(404);
            die("Книгу не знайдено");
        }

        $publisherModel = new Publisher();
        $publishers = $publisherModel->getAll();

        $this->view('admin/edit_book', [
            'title' => 'Редагувати книгу',
            'book' => $book,
            'publishers' => $publishers
        ]);
    }

    public function update(string $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookModel = new Book();
            $bookModel->update((int)$id, $_POST);

            header('Location: /coursework/admin/dashboard');
            exit;
        }
    }

    public function delete(string $id): void
    {
        $bookModel = new Book();
        $bookModel->delete((int)$id);

        header('Location: /coursework/admin/dashboard');
        exit;
    }
}
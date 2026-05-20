<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Book;

class BookController extends Controller
{
    public function index(): void
    {
        $bookModel = new Book();
        $books = $bookModel->getAll();

        $this->view('home', [
            'title' => 'Каталог книг',
            'books' => $books
        ]);
    }

    public function show(string $id): void
    {
        $bookModel = new Book();
        $book = $bookModel->getById((int)$id);

        if (!$book) {
            http_response_code(404);
            $this->view('errors/404', [
                'title' => 'Сторінку не знайдено'
            ]);
            return;
        }

        $this->view('book', [
            'title' => $book['title'],
            'book' => $book
        ]);
    }
}
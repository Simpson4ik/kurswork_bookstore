<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Book;

class HomeController extends Controller
{
    public function index(): void
    {
        $bookModel = new Book();
        $books = $bookModel->getAll();

        $this->view('home', [
            'title' => 'Головна сторінка інтернет-магазину',
            'books' => $books
        ]);
    }
}
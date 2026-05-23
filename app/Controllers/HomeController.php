<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Book;
use App\Models\Genre;

class HomeController extends Controller
{
    public function index(): void
    {
        $bookModel = new Book();
        $genreModel = new Genre();

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) {
            $page = 1;
        }

        $perPage = 6;
        $totalBooks = $bookModel->getTotalCount();
        $totalPages = (int)ceil($totalBooks / $perPage);

        if ($page > $totalPages && $totalPages > 0) {
            $page = $totalPages;
        }

        $books = $bookModel->getPaginated($page, $perPage);

        $this->view('home', [
            'title' => 'Головна сторінка інтернет-магазину',
            'books' => $books,
            'genres' => $genreModel->getAll(),
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }
}
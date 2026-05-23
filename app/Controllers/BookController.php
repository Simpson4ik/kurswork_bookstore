<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Book;
use App\Models\Genre;
use App\Core\Response;

class BookController extends Controller
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
            'title' => 'Каталог книг',
            'books' => $books,
            'genres' => $genreModel->getAll(),
            'currentPage' => $page,
            'totalPages' => $totalPages
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

    public function searchAjax(): void
    {
        $response = new Response();
        $filters = $_GET;

        if (isset($filters['genres']) && !is_array($filters['genres']) && $filters['genres'] !== '') {
            $filters['genres'] = explode(',', $filters['genres']);
        }

        $bookModel = new Book();

        $hasActiveFilters = !empty($filters['q']) ||
            (isset($filters['min_price']) && $filters['min_price'] !== '') ||
            (isset($filters['max_price']) && $filters['max_price'] !== '') ||
            (!empty($filters['in_stock']) && $filters['in_stock'] === 'true') ||
            !empty($filters['genres']);

        if ($hasActiveFilters) {
            $books = $bookModel->filter($filters);
        } else {
            $books = $bookModel->getPaginated(1, 6);
        }

        $response->json([
            'success' => true,
            'books' => $books
        ]);
    }
}
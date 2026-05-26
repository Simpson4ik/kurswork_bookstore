<?php

namespace App\Controllers\Admin;

use App\Models\Genre;
use App\Core\Response;

class GenreController extends AdminController
{
    public function index(): void
    {
        $genreModel = new Genre();
        $this->view('admin/genres', [
            'title' => 'Керування жанрами',
            'genres' => $genreModel->getAll()
        ]);
    }

    public function store(): void
    {
        $genreModel = new Genre();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(trim($_POST['genre_name']))) {
            $name = trim($_POST['genre_name']);
            if ($genreModel->getByName($name)) {
                $this->view('admin/genres', [
                    'title' => 'Керування жанрами',
                    'genres' => $genreModel->getAll(),
                    'error' => "Жанр «{$name}» вже існує в базі!"
                ]);
                return;
            }

            $genreModel->create($name);
        }
        $this->redirect('admin/genres');
    }

    public function delete(string $id): void
    {
        $response = new Response();
        $genreId = (int)$id;
        $genreModel = new Genre();

        try {
            $genreModel->delete($genreId);
            $response->json(['success' => true, 'message' => 'Жанр успішно видалено з системи.']);
        } catch (\Exception $e) {
            $response->json(['success' => false, 'message' => 'Неможливо видалити жанр. Оскільки деякі книги використовують його.'], 400);
        }
    }
}
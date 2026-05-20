<?php

namespace App\Controllers\Admin;

use App\Models\Genre;

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
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(trim($_POST['genre_name']))) {
            $genreModel = new Genre();
            $genreModel->create(trim($_POST['genre_name']));
        }
        header('Location: /coursework/admin/genres');
        exit;
    }
}
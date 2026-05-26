<?php

namespace App\Controllers\Admin;

use App\Models\Author;
use App\Core\Response;

class AuthorController extends AdminController
{
    public function index(): void
    {
        $authorModel = new Author();
        $this->view('admin/authors', [
            'title' => 'Керування авторами',
            'authors' => $authorModel->getAll()
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(trim($_POST['first_name'])) && !empty(trim($_POST['last_name']))) {
            $authorModel = new Author();
            $authorModel->create($_POST);
        }

        $this->redirect('admin/authors');
    }

    public function delete(string $id): void
    {
        $response = new Response();
        $authorId = (int)$id;
        $authorModel = new Author();

        try {
            $authorModel->delete($authorId);
            $response->json(['success' => true, 'message' => 'Автора успішно видалено з бази даних.']);
        } catch (\Exception $e) {
            $response->json(['success' => false, 'message' => 'Неможливо видалити автора. Він прив’язаний до існуючих книг у каталозі.'], 400);
        }
    }
}
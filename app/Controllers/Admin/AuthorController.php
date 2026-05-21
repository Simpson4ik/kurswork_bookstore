<?php

namespace App\Controllers\Admin;

use App\Models\Author;

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

        header('Location: /coursework/admin/authors');
        exit;
    }

    public function checkEmailAjax(): void
    {
        $response = new \App\Core\Response();
        $input = json_decode(file_get_contents('php://input'), true);
        $email = isset($input['email']) ? trim($input['email']) : '';

        if (empty($email)) {
            $response->json(['success' => false, 'message' => 'Email не вказано'], 400);
        }

        $customerModel = new \App\Models\Customer();
        $user = $customerModel->getByEmail($email);

        $response->json([
            'success' => true,
            'exists' => (bool)$user
        ]);
    }
}
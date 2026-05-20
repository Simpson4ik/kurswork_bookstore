<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Customer;

class AuthController extends Controller
{
    public function register(): void
    {
        $this->view('auth/register', ['title' => 'Реєстрація']);
    }

    public function storeRegister(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customerModel = new Customer();

            if ($customerModel->getByEmail($_POST['email'])) {
                die("Користувач з такою поштою вже існує");
            }

            $customerModel->create($_POST);
            header('Location: /coursework/login');
            exit;
        }
    }

    public function login(): void
    {
        $this->view('auth/login', ['title' => 'Вхід']);
    }

    public function authenticate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customerModel = new Customer();
            $user = $customerModel->getByEmail($_POST['email']);

            if ($user && password_verify($_POST['password'], $user['password_hash'])) {
                $_SESSION['user'] = [
                    'id' => $user['customer_id'],
                    'name' => $user['first_name'],
                    'role' => $user['role']
                ];

                if ($user['role'] === 'admin') {
                    header('Location: /coursework/admin/dashboard');
                } else {
                    header('Location: /coursework/');
                }
                exit;
            }

            die("Неправильний email або пароль");
        }
    }

    public function logout(): void
    {
        unset($_SESSION['user']);
        session_destroy();
        header('Location: /coursework/');
        exit;
    }
}
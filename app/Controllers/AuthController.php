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
                $response = new \App\Core\Response();
                $response->setStatus(400)->send("<h2>Помилка реєстрації</h2><p>Користувач з такою поштою вже існує в системі.</p><p><a href='/coursework/register'>Назад</a></p>");
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

                if (isset($_POST['remember'])) {
                    $token = bin2hex(random_bytes(32));
                    $customerModel->updateRememberToken($user['customer_id'], $token);
                    setcookie('remember_me', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
                }

                if ($user['role'] === 'admin') {
                    header('Location: /coursework/admin/dashboard');
                } else {
                    header('Location: /coursework/');
                }
                exit;
            }
            $response = new \App\Core\Response();
            $response->setStatus(401)->send("<h2>Помилка входу</h2><p>Неправильний email або пароль.</p><p><a href='/coursework/login'>Спробувати знову</a></p>");
        }
    }

    public function logout(): void
    {
        if (isset($_SESSION['user'])) {
            $customerModel = new Customer();
            $customerModel->updateRememberToken((int)$_SESSION['user']['id'], null);
        }

        if (isset($_COOKIE['remember_me'])) {
            setcookie('remember_me', '', time() - 3600, '/');
        }

        unset($_SESSION['user']);
        session_destroy();
        header('Location: /coursework/');
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

        $customerModel = new Customer();
        $user = $customerModel->getByEmail($email);

        $response->json([
            'success' => true,
            'exists' => (bool)$user
        ]);
    }
}
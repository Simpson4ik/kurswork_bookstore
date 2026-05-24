<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Customer;
use App\Core\Response;

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
                http_response_code(400);
                $this->view('auth/register', [
                    'title' => 'Реєстрація',
                    'error' => 'Користувач з такою поштою вже існує в системі.'
                ]);
                exit;
            }

            $customerModel->create($_POST);
            $this->redirect('login');
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
                session_regenerate_id(true);

                $_SESSION['user'] = [
                    'id' => $user['customer_id'],
                    'name' => $user['first_name'],
                    'role' => $user['role']
                ];

                if (isset($_POST['remember'])) {
                    $token = bin2hex(random_bytes(32));
                    $customerModel->updateRememberToken($user['customer_id'], $token);

                    $cookiePath = defined('BASE_PATH') && BASE_PATH !== '' ? BASE_PATH : '/';
                    setcookie('remember_me', $token, time() + (30 * 24 * 60 * 60), $cookiePath, '', false, true);
                }

                if ($user['role'] === 'admin') {
                    $this->redirect('admin/dashboard');
                } else {
                    $this->redirect('');
                }
            }

            http_response_code(401);
            $this->view('auth/login', [
                'title' => 'Вхід',
                'error' => 'Неправильний email або пароль.'
            ]);
            exit;
        }
    }

    public function logout(): void
    {
        if (isset($_SESSION['user'])) {
            $customerModel = new Customer();
            $customerModel->updateRememberToken((int)$_SESSION['user']['id'], null);
        }

        if (isset($_COOKIE['remember_me'])) {
            $cookiePath = defined('BASE_PATH') && BASE_PATH !== '' ? BASE_PATH : '/';
            setcookie('remember_me', '', time() - 3600, $cookiePath);
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
        $this->redirect('login');
    }

    public function checkEmailAjax(): void
    {
        $response = new Response();
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
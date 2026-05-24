<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Core\Response;

class ProfileController extends Controller
{
    public function index(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /coursework/login');
            exit;
        }

        $customerModel = new Customer();
        $user = $customerModel->getById((int)$_SESSION['user']['id']);

        $this->view('profile', [
            'title' => 'Особистий кабінет',
            'user' => $user
        ]);
    }

    public function orders(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /coursework/login');
            exit;
        }

        $orderModel = new Order();
        $orders = $orderModel->getByCustomerId((int)$_SESSION['user']['id']);

        $this->view('orders', [
            'title' => 'Історія моїх замовлень',
            'orders' => $orders
        ]);
    }

    public function updateAjax(): void
    {
        $response = new Response();
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($_SESSION['user'])) {
            $response->json(['success' => false, 'message' => 'Неавторизований доступ'], 401);
        }

        $firstName = isset($input['first_name']) ? trim($input['first_name']) : '';
        $lastName = isset($input['last_name']) ? trim($input['last_name']) : '';
        $phone = isset($input['phone']) ? trim($input['phone']) : '';
        $email = isset($input['email']) ? trim($input['email']) : '';

        if (empty($firstName) || empty($lastName) || empty($email)) {
            $response->json(['success' => false, 'message' => "Заповніть обов'язкові поля"], 400);
        }

        $customerId = (int)$_SESSION['user']['id'];
        $customerModel = new Customer();

        if ($customerModel->updateProfile($customerId, [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone,
            'email' => $email
        ])) {
            $_SESSION['user']['name'] = $firstName;
            $response->json(['success' => true, 'message' => 'Профіль успешно оновлено!']);
        } else {
            $response->json(['success' => false, 'message' => 'Помилка оновлення даних в БД'], 500);
        }
    }
}
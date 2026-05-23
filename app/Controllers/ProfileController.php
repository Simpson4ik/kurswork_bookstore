<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Customer;
use App\Models\Order;

class ProfileController extends Controller
{
    public function index(): void
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /coursework/login');
            exit;
        }

        $customerModel = new Customer();
        $userData = $customerModel->getById((int)$_SESSION['user']['id']);

        if (!$userData) {
            header('Location: /coursework/logout');
            exit;
        }

        $this->view('profile', [
            'title' => 'Особистий кабінет',
            'user' => $userData
        ]);
    }

    public function updateAjax(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Несанкціонований доступ або невірний метод.']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        $firstName = isset($input['first_name']) ? trim($input['first_name']) : '';
        $lastName = isset($input['last_name']) ? trim($input['last_name']) : '';
        $phone = isset($input['phone']) ? trim($input['phone']) : '';
        $email = isset($input['email']) ? trim($input['email']) : '';

        if (empty($firstName) || empty($lastName) || empty($email)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Будь ласка, заповніть обов\'язкові поля (Ім\'я, Прізвище, Email).']);
            exit;
        }

        $customerModel = new Customer();
        $existingUser = $customerModel->getByEmail($email);
        if ($existingUser && (int)$existingUser['customer_id'] !== (int)$_SESSION['user']['id']) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Цей Email вже використовується іншим акаунтом.']);
            exit;
        }

        $success = $customerModel->updateProfile((int)$_SESSION['user']['id'], [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone,
            'email' => $email
        ]);

        if ($success) {
            $_SESSION['user']['name'] = $firstName;
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Дані вашого профілю успішно оновлено!']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Не вдалося зберегти зміни профілю в базі даних.']);
        }
        exit;
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
            'title' => 'Мої замовлення',
            'orders' => $orders
        ]);
    }
}
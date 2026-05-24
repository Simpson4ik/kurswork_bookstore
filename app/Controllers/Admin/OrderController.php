<?php

namespace App\Controllers\Admin;

use App\Models\Order;
use App\Core\Response;

class OrderController extends AdminController
{
    public function index(): void
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) {
            $page = 1;
        }
        $perPage = 10;

        $orderModel = new Order();
        $orders = $orderModel->getAllPaginated($page, $perPage);
        $totalOrders = $orderModel->getTotalCount();
        $totalPages = (int)ceil($totalOrders / $perPage);

        $this->view('admin/orders', [
            'title' => 'Керування замовленнями',
            'orders' => $orders,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function show(string $id): void
    {
        $orderId = (int)$id;
        $orderModel = new Order();
        $order = $orderModel->getDetailedById($orderId);

        if (!$order) {
            $response = new Response();
            $response->setStatus(404)->send("<h2>Замовлення не знайдено</h2>");
            return;
        }

        $this->view('admin/view_order', [
            'title' => "Замовлення #" . $order['order_id'],
            'order' => $order
        ]);
    }

    public function updateStatusAjax(): void
    {
        $response = new Response();
        $input = json_decode(file_get_contents('php://input'), true);

        $orderId = isset($input['order_id']) ? (int)$input['order_id'] : 0;
        $status = isset($input['status']) ? trim($input['status']) : '';

        if ($orderId <= 0 || empty($status)) {
            $response->json(['success' => false, 'message' => 'Некоректні вхідні дані'], 400);
        }

        $allowedStatuses = ['Нове', 'Підтверджено', 'Відправлено', 'Виконано', 'Скасовано'];
        if (!in_array($status, $allowedStatuses)) {
            $response->json(['success' => false, 'message' => 'Недопустимий статус замовлення'], 400);
        }

        $orderModel = new Order();
        if ($orderModel->updateStatus($orderId, $status)) {
            $response->json(['success' => true, 'message' => 'Статус замовлення успішно змінено. Залишки перераховано!']);
        } else {
            $response->json(['success' => false, 'message' => 'Критична помилка оновлення статусу'], 500);
        }
    }
}
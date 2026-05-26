<?php

namespace App\Models;

use App\Core\Model;
use Exception;

class Order extends Model
{
    public function saveOrder(int $customerId, array $cartItems, float $totalPrice): bool
    {
        try {
            $this->db->beginTransaction();

            $statement = $this->db->prepare("INSERT INTO orders (customer_id, total_amount) VALUES (?, ?)");
            $statement->execute([$customerId, $totalPrice]);

            $orderId = $this->db->lastInsertId();

            $itemStatement = $this->db->prepare("INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)");
            $updateStockStatement = $this->db->prepare("UPDATE books SET stock_quantity = stock_quantity - ? WHERE book_id = ?");

            foreach ($cartItems as $item) {
                $itemStatement->execute([$orderId, $item['book_id'], $item['quantity'], $item['price']]);
                $updateStockStatement->execute([$item['quantity'], $item['book_id']]);
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getByCustomerId(int $customerId): array
    {
        $statement = $this->db->prepare("
            SELECT o.order_id, o.total_amount, o.order_date, o.status,
                   oi.quantity, oi.price, b.title
            FROM orders o
            LEFT JOIN order_items oi ON o.order_id = oi.order_id
            LEFT JOIN books b ON oi.book_id = b.book_id
            WHERE o.customer_id = ?
            ORDER BY o.order_id DESC
        ");
        $statement->execute([$customerId]);
        $rows = $statement->fetchAll();

        $orders = [];
        foreach ($rows as $row) {
            $orderId = $row['order_id'];
            if (!isset($orders[$orderId])) {
                $orders[$orderId] = [
                    'order_id' => $orderId,
                    'total_amount' => $row['total_amount'],
                    'order_date' => $row['order_date'] ?? null,
                    'status' => $row['status'] ?? 'Нове',
                    'items' => []
                ];
            }
            if ($row['title']) {
                $orders[$orderId]['items'][] = [
                    'title' => $row['title'],
                    'quantity' => $row['quantity'],
                    'price' => $row['price']
                ];
            }
        }
        return array_values($orders);
    }

    public function getAllPaginated(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $statement = $this->db->prepare("
            SELECT o.order_id, o.total_amount, o.order_date, o.status,
                   c.first_name, c.last_name, c.email
            FROM orders o
            LEFT JOIN customers c ON o.customer_id = c.customer_id
            ORDER BY o.order_id DESC
            LIMIT ? OFFSET ?
        ");
        $statement->bindValue(1, $perPage, \PDO::PARAM_INT);
        $statement->bindValue(2, $offset, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function getTotalCount(): int
    {
        $statement = $this->db->query("SELECT COUNT(*) FROM orders");
        return (int)$statement->fetchColumn();
    }

    public function getDetailedById(int $orderId): ?array
    {
        $statement = $this->db->prepare("
            SELECT o.order_id, o.total_amount, o.order_date, o.status,
                   c.first_name, c.last_name, c.phone, c.email
            FROM orders o
            LEFT JOIN customers c ON o.customer_id = c.customer_id
            WHERE o.order_id = ?
        ");
        $statement->execute([$orderId]);
        $order = $statement->fetch();

        if (!$order) {
            return null;
        }

        $itemStatement = $this->db->prepare("
            SELECT oi.quantity, oi.price, b.title, b.book_id
            FROM order_items oi
            LEFT JOIN books b ON oi.book_id = b.book_id
            WHERE oi.order_id = ?
        ");
        $itemStatement->execute([$orderId]);
        $order['items'] = $itemStatement->fetchAll();

        return $order;
    }

    public function updateStatus(int $orderId, string $newStatus): bool
    {
        try {
            $this->db->beginTransaction();

            $checkStatement = $this->db->prepare("SELECT status FROM orders WHERE order_id = ?");
            $checkStatement->execute([$orderId]);
            $currentStatus = $checkStatement->fetchColumn();

            if (!$currentStatus) {
                $this->db->rollBack();
                return false;
            }

            if ($currentStatus !== 'Скасовано' && $newStatus === 'Скасовано') {
                $itemStatement = $this->db->prepare("SELECT book_id, quantity FROM order_items WHERE order_id = ?");
                $itemStatement->execute([$orderId]);
                $items = $itemStatement->fetchAll();

                $restoreStockStatement = $this->db->prepare("UPDATE books SET stock_quantity = stock_quantity + ? WHERE book_id = ?");
                foreach ($items as $item) {
                    $restoreStockStatement->execute([$item['quantity'], $item['book_id']]);
                }
            }

            $updateStatement = $this->db->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
            $updateStatement->execute([$newStatus, $orderId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    public function getTotalRevenue(): float
    {
        $statement = $this->db->query("SELECT SUM(total_amount) FROM orders WHERE status != 'Скасовано'");
        return (float)$statement->fetchColumn();
    }
}
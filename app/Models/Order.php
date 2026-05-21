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
            SELECT o.order_id, o.total_amount, o.order_date, 
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
}
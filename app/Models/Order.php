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
}
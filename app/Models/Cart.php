<?php

namespace App\Models;

use App\Core\Model;

class Cart extends Model
{
    public function getByCustomerId(int $customerId): array
    {
        $statement = $this->db->prepare("SELECT book_id, quantity FROM cart_items WHERE customer_id = ?");
        $statement->execute([$customerId]);
        return $statement->fetchAll(\PDO::FETCH_KEY_PAIR) ?: [];
    }

    public function saveItem(int $customerId, int $bookId, int $quantity): bool
    {
        $statement = $this->db->prepare("
            INSERT INTO cart_items (customer_id, book_id, quantity) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE quantity = ?
        ");
        return $statement->execute([$customerId, $bookId, $quantity, $quantity]);
    }

    public function removeItem(int $customerId, int $bookId): bool
    {
        $statement = $this->db->prepare("DELETE FROM cart_items WHERE customer_id = ? AND book_id = ?");
        return $statement->execute([$customerId, $bookId]);
    }

    public function clear(int $customerId): bool
    {
        $statement = $this->db->prepare("DELETE FROM cart_items WHERE customer_id = ?");
        return $statement->execute([$customerId]);
    }
}
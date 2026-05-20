<?php

namespace App\Models;

use App\Core\Model;

class Book extends Model
{
    public function getAll(): array
    {
        $statement = $this->db->query("
            SELECT books.*, publishers.publisher_name 
            FROM books 
            JOIN publishers ON books.publisher_id = publishers.publisher_id 
            ORDER BY books.book_id DESC
        ");
        return $statement->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $statement = $this->db->prepare("
            SELECT books.*, publishers.publisher_name 
            FROM books 
            JOIN publishers ON books.publisher_id = publishers.publisher_id 
            WHERE books.book_id = ?
        ");
        $statement->execute([$id]);
        $result = $statement->fetch();
        return $result ?: null;
    }


    public function create(array $data): bool
    {
        $statement = $this->db->prepare("
            INSERT INTO books (title, isbn, publication_year, price, stock_quantity, publisher_id) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        return $statement->execute([
            $data['title'],
            $data['isbn'],
            (int)$data['publication_year'],
            (float)$data['price'],
            (int)$data['stock_quantity'],
            (int)$data['publisher_id']
        ]);
    }


    public function update(int $id, array $data): bool
    {
        $statement = $this->db->prepare("
            UPDATE books 
            SET title = ?, isbn = ?, publication_year = ?, price = ?, stock_quantity = ?, publisher_id = ? 
            WHERE book_id = ?
        ");
        return $statement->execute([
            $data['title'],
            $data['isbn'],
            (int)$data['publication_year'],
            (float)$data['price'],
            (int)$data['stock_quantity'],
            (int)$data['publisher_id'],
            $id
        ]);
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare("DELETE FROM books WHERE book_id = ?");
        return $statement->execute([$id]);
    }
}
<?php

namespace App\Models;

use App\Core\Model;

class Author extends Model
{
    public function getAll(): array
    {
        $statement = $this->db->query("SELECT * FROM authors ORDER BY last_name ASC, first_name ASC");
        return $statement->fetchAll();
    }

    public function create(array $data): bool
    {
        $statement = $this->db->prepare("
            INSERT INTO authors (first_name, last_name, biography) 
            VALUES (?, ?, ?)
        ");

        return $statement->execute([
            $data['first_name'],
            $data['last_name'],
            !empty(trim($data['biography'])) ? trim($data['biography']) : null
        ]);
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare("DELETE FROM authors WHERE author_id = ?");
        return $statement->execute([$id]);
    }
}
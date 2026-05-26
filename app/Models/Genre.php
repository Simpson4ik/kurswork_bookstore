<?php

namespace App\Models;

use App\Core\Model;

class Genre extends Model
{
    public function getAll(): array
    {
        $statement = $this->db->query("SELECT * FROM genres ORDER BY genre_name ASC");
        return $statement->fetchAll();
    }

    public function create(string $name): bool
    {
        $statement = $this->db->prepare("INSERT INTO genres (genre_name) VALUES (?)");
        return $statement->execute([$name]);
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare("DELETE FROM genres WHERE genre_id = ?");
        return $statement->execute([$id]);
    }

    public function getByName(string $name): ?array
    {
        $statement = $this->db->prepare("SELECT * FROM genres WHERE genre_name = ?");
        $statement->execute([$name]);
        $result = $statement->fetch();
        return $result ?: null;
    }
}
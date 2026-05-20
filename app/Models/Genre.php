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
}
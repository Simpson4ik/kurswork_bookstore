<?php

namespace App\Models;

use App\Core\Model;

class Publisher extends Model
{
    public function getAll(): array
    {
        $statement = $this->db->query("SELECT * FROM publishers ORDER BY publisher_name ASC");
        return $statement->fetchAll();
    }

    public function create(string $name): bool
    {
        $statement = $this->db->prepare("INSERT INTO publishers (publisher_name) VALUES (?)");
        return $statement->execute([$name]);
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare("DELETE FROM publishers WHERE publisher_id = ?");
        return $statement->execute([$id]);
    }

    public function getByName(string $name): ?array
    {
        $statement = $this->db->prepare("SELECT * FROM publishers WHERE publisher_name = ?");
        $statement->execute([$name]);
        $result = $statement->fetch();
        return $result ?: null;
    }
}
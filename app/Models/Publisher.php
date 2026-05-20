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
}
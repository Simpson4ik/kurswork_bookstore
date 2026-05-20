<?php

namespace App\Models;

use App\Core\Model;

class Book extends Model
{
    public function getAll(): array
    {
        $statement = $this->db->query("SELECT * FROM books ORDER BY book_id DESC");
        return $statement->fetchAll();
    }
}
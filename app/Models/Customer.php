<?php

namespace App\Models;

use App\Core\Model;

class Customer extends Model
{
    public function create(array $data): bool
    {
        $statement = $this->db->prepare("
            INSERT INTO customers (first_name, last_name, email, password_hash) 
            VALUES (?, ?, ?, ?)
        ");

        return $statement->execute([
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT)
        ]);
    }

    public function getByEmail(string $email): ?array
    {
        $statement = $this->db->prepare("SELECT * FROM customers WHERE email = ?");
        $statement->execute([$email]);
        $result = $statement->fetch();
        return $result ?: null;
    }
}
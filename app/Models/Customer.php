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

    public function updateRememberToken(int $id, ?string $token): bool
    {
        $statement = $this->db->prepare("UPDATE customers SET remember_token = ? WHERE customer_id = ?");
        return $statement->execute([$token, $id]);
    }

    public function getByRememberToken(string $token): ?array
    {
        $statement = $this->db->prepare("SELECT * FROM customers WHERE remember_token = ?");
        $statement->execute([$token]);
        $result = $statement->fetch();
        return $result ?: null;
    }

    public function getById(int $id): ?array
    {
        $statement = $this->db->prepare("SELECT * FROM customers WHERE customer_id = ?");
        $statement->execute([$id]);
        $result = $statement->fetch();
        return $result ?: null;
    }

    public function updateProfile(int $id, array $data): bool
    {
        $statement = $this->db->prepare("
            UPDATE customers 
            SET first_name = ?, last_name = ?, phone = ?, email = ? 
            WHERE customer_id = ?
        ");

        return $statement->execute([
            $data['first_name'],
            $data['last_name'],
            $data['phone'],
            $data['email'],
            $id
        ]);
    }
}
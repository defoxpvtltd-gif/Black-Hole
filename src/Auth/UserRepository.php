<?php
declare(strict_types=1);

namespace App\Auth;

use App\Database\Database;

final class UserRepository
{
    public function findByEmail(string $email): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => mb_strtolower(trim($email))]);
        $user = $stmt->fetch();
        return is_array($user) ? $user : null;
    }

    public function findById(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT id, name, email, created_at FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return is_array($user) ? $user : null;
    }

    public function create(string $name, string $email, string $password): array
    {
        $normalizedEmail = mb_strtolower(trim($email));
        $stmt = Database::connection()->prepare(
            'INSERT INTO users (name, email, password_hash, created_at) VALUES (:name, :email, :password_hash, :created_at)'
        );
        $stmt->execute([
            'name' => trim($name),
            'email' => $normalizedEmail,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'created_at' => date('c'),
        ]);

        $id = (int) Database::connection()->lastInsertId();
        return $this->findById($id) ?? [
            'id' => $id,
            'name' => trim($name),
            'email' => $normalizedEmail,
            'created_at' => date('c'),
        ];
    }
}

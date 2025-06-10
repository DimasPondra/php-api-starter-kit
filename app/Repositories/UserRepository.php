<?php

namespace Pondra\PhpApiStarterKit\Repositories;

use PDO;
use Pondra\PhpApiStarterKit\Helpers\DateTimeHelper;
use Pondra\PhpApiStarterKit\Models\User;

class UserRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getAllUsers(array $filters = []): ?array
    {
        $sql = 'SELECT id, name, email FROM users';
        $params = [];

        if (!empty($filters)) {
            $conditions = [];
            foreach ($filters as $column => $value) {
                $conditions[] = "$column = ?";
                $params[] = $value;
            }

            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $statement = $this->connection->prepare($sql);
        $statement->execute($params);

        try {
            if ($rows = $statement->fetchAll()) {
                $users = [];

                foreach ($rows as $row) {
                    $user = new User();
                    $user->id = $row['id'];
                    $user->name = $row['name'];
                    $user->email = $row['email'];

                    $users[] = $user;
                }

                return $users;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    public function findById(string $id): ?User
    {
        $statement = $this->connection->prepare('SELECT id, name, email, email_verified_at, role_id FROM users WHERE id = ?');
        $statement->execute([$id]);

        try {
            if ($row = $statement->fetch()) {
                $user = new User();
                $user->id = $row['id'];
                $user->name = $row['name'];
                $user->email = $row['email'];
                $user->emailVerifiedAt = $row['email_verified_at'] === null ? null : DateTimeHelper::convertUtcToLocal($row['email_verified_at']);
                $user->role_id = $row['role_id'];

                return $user;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    public function findByEmail(string $email): ?User
    {
        $statement = $this->connection->prepare('SELECT id, name, email, email_verified_at, password, role_id FROM users WHERE email = ?');
        $statement->execute([$email]);

        try {
            if ($row = $statement->fetch()) {
                $user = new User();
                $user->id = $row['id'];
                $user->name = $row['name'];
                $user->email = $row['email'];
                $user->emailVerifiedAt = $row['email_verified_at'] === null ? null : DateTimeHelper::convertUtcToLocal($row['email_verified_at']);
                $user->password = $row['password'];
                $user->role_id = $row['role_id'];

                return $user;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    public function save(User $user): User
    {
        $statement = $this->connection->prepare('INSERT INTO users(id, name, email, password, role_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $statement->execute([
            $user->id, $user->name, $user->email, $user->password,
            $user->role_id, $user->createdAt->format('Y-m-d H:i:s'), $user->updatedAt->format('Y-m-d H:i:s')
        ]);

        return $user;
    }

    public function verifyEmail(User $user): User
    {
        $statement = $this->connection->prepare('UPDATE users SET email_verified_at = ?, updated_at = ? WHERE id = ?');
        $statement->execute([
            $user->emailVerifiedAt->format('Y-m-d H:i:s'),
            $user->updatedAt->format('Y-m-d H:i:s'),
            $user->id
        ]);

        return $user;
    }

    public function resetPassword(User $user): User
    {
        $statement = $this->connection->prepare('UPDATE users SET password = ?, updated_at = ? WHERE id = ?');
        $statement->execute([
            $user->password,
            $user->updatedAt->format('Y-m-d H:i:s'),
            $user->id
        ]);

        return $user;
    }

    public function deleteAll()
    {
        $this->connection->exec('DELETE FROM users');
    }
}
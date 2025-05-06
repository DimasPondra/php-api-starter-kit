<?php

namespace Pondra\PhpApiStarterKit\Repositories;

use PDO;
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

    public function findByEmail(string $email): ?User
    {
        $statement = $this->connection->prepare('SELECT id, name, email, password, role_id FROM users WHERE email = ?');
        $statement->execute([$email]);

        try {
            if ($row = $statement->fetch()) {
                $user = new User();
                $user->id = $row['id'];
                $user->name = $row['name'];
                $user->email = $row['email'];
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
            $user->role_id, $user->created_at, $user->updated_at
        ]);

        return $user;
    }
}
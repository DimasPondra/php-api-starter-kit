<?php

namespace Pondra\PhpApiStarterKit\Repositories;

use PDO;
use Pondra\PhpApiStarterKit\Models\Role;

class RoleRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findByName(string $name): ?Role
    {
        $statement = $this->connection->prepare('SELECT id, name, slug FROM roles WHERE name = ?');
        $statement->execute([$name]);

        try {
            if ($row = $statement->fetch()) {
                $role = new Role();
                $role->id = $row['id'];
                $role->name = $row['name'];
                $role->slug = $row['slug'];

                return $role;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    public function save(Role $role): Role
    {
        $statement = $this->connection->prepare('INSERT INTO roles(id, name, slug, created_at, updated_at) VALUES (?, ?, ?, ?, ?)');
        $statement->execute([
            $role->id, $role->name, $role->slug, $role->createdAt,
            $role->updatedAt
        ]);

        return $role;
    }
}
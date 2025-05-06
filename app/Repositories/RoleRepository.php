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

    public function getAllRoles(): ?array
    {
        $statement = $this->connection->prepare('SELECT id, name, slug FROM roles');
        $statement->execute();

        try {
            if ($rows = $statement->fetchAll()) {
                $roles = [];

                foreach ($rows as $row) {
                    $role = new Role();
                    $role->id = $row['id'];
                    $role->name = $row['name'];
                    $role->slug = $row['slug'];

                    $roles[] = $role;
                }

                return $roles;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    public function findById(string $id): ?Role
    {
        $statement = $this->connection->prepare('SELECT id, name, slug FROM roles WHERE id = ?');
        $statement->execute([$id]);

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

    public function update(Role $role): Role
    {
        $statement = $this->connection->prepare('UPDATE roles SET name = ?, slug = ?, updated_at = ? WHERE id = ?');
        $statement->execute([
            $role->name, $role->slug, $role->updatedAt, $role->id
        ]);

        return $role;
    }

    public function deleteById(string $id)
    {
        $statement = $this->connection->prepare('DELETE FROM roles WHERE id = ?');
        $statement->execute([$id]);
    }
}
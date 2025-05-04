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
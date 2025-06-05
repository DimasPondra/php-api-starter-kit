<?php

namespace Pondra\PhpApiStarterKit\Repositories;

use PDO;
use Pondra\PhpApiStarterKit\Models\File;

class FileRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findById(string $id): ?File
    {
        $statement = $this->connection->prepare('SELECT id, name, location FROM files WHERE id = ?');
        $statement->execute([$id]);

        try {
            if ($row = $statement->fetch()) {
                $file = new File();
                $file->id = $row['id'];
                $file->name = $row['name'];
                $file->location = $row['location'];

                return $file;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    public function save(File $file): File
    {
        $statement = $this->connection->prepare('INSERT INTO files(id, name, location, created_at) VALUES (?, ?, ?, ?)');
        $statement->execute([
            $file->id, $file->name, $file->location, $file->createdAt->format('Y-m-d H:i:s')
        ]);

        return $file;
    }

    public function deleteById(string $id)
    {
        $statement = $this->connection->prepare('DELETE FROM files WHERE id = ?');
        $statement->execute([$id]);
    }
}
<?php

namespace Pondra\PhpApiStarterKit\Repositories;

use PDO;
use Pondra\PhpApiStarterKit\Models\Verification;

class EmailRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(Verification $verification): Verification
    {
        $statement = $this->connection->prepare('INSERT INTO verifications(id, token, expires_at, user_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)');
        $statement->execute([
            $verification->id,
            $verification->token,
            $verification->expiresAt->format('Y-m-d H:i:s'),
            $verification->user_id,
            $verification->createdAt->format('Y-m-d H:i:s'),
            $verification->updatedAt->format('Y-m-d H:i:s')
        ]);

        return $verification;
    }
}
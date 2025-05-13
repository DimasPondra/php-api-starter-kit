<?php

namespace Pondra\PhpApiStarterKit\Repositories;

use DateTime;
use PDO;
use Pondra\PhpApiStarterKit\Models\Verification;

class EmailRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findByToken(string $token): ?Verification
    {
        $statement = $this->connection->prepare('SELECT id, token, expires_at, user_id FROM verifications WHERE token = ?');
        $statement->execute([$token]);

        try {
            if ($row = $statement->fetch()) {
                $verification = new Verification();
                $verification->id = $row['id'];
                $verification->token = $row['token'];
                $verification->expiresAt = new DateTime($row['expires_at']);
                $verification->user_id = $row['user_id'];

                return $verification;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
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

    public function deleteByUserId(string $userId)
    {
        $statement = $this->connection->prepare('DELETE FROM verifications WHERE user_id = ?');
        $statement->execute([$userId]);
    }
}
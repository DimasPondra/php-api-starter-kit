<?php

namespace Pondra\PhpApiStarterKit\Repositories;

use PDO;
use Pondra\PhpApiStarterKit\Helpers\DateTimeHelper;
use Pondra\PhpApiStarterKit\Models\PasswordResetToken;

class PasswordRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findByToken(string $token): ?PasswordResetToken
    {
        $statement = $this->connection->prepare('SELECT id, email, token, expires_at FROM password_reset_tokens WHERE token = ?');
        $statement->execute([$token]);

        try {
            if ($row = $statement->fetch()) {
                $prt = new PasswordResetToken();
                $prt->id = $row['id'];
                $prt->email = $row['email'];
                $prt->token = $row['token'];
                $prt->expiresAt = DateTimeHelper::convertUtcToLocal($row['expires_at']);

                return $prt;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    public function save(PasswordResetToken $prt): PasswordResetToken
    {
        $statement = $this->connection->prepare('INSERT INTO password_reset_tokens (id, email, token, expires_at, created_at) VALUES (?, ?, ?, ?, ?)');
        $statement->execute([
            $prt->id, $prt->email, $prt->token,
            $prt->expiresAt->format('Y-m-d H:i:s'), 
            $prt->createdAt->format('Y-m-d H:i:s')
        ]);

        return $prt;
    }

    public function deleteByEmail(string $email)
    {
        $statement = $this->connection->prepare('DELETE FROM password_reset_tokens WHERE email = ?');
        $statement->execute([$email]);
    }

    public function deleteAll()
    {
        $this->connection->exec('DELETE FROM password_reset_tokens');
    }
}
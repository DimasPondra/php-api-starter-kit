<?php

namespace Pondra\PhpApiStarterKit\Repositories;

use PDO;
use Pondra\PhpApiStarterKit\Helpers\DateTimeHelper;
use Pondra\PhpApiStarterKit\Models\PersonalAccessToken;

class PersonalAccessTokenRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findByToken(string $token): ?PersonalAccessToken
    {
        $statement = $this->connection->prepare('SELECT id, user_id, abilities, expires_at FROM personal_access_tokens WHERE token = ?');
        $statement->execute([$token]);

        try {
            if ($row = $statement->fetch()) {
                $pat = new PersonalAccessToken();
                $pat->id = $row['id'];
                $pat->user_id = $row['user_id'];
                $pat->abilities = $row['abilities'];
                $pat->expiresAt = DateTimeHelper::convertUtcToLocal($row['expires_at']);

                return $pat;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    public function save(PersonalAccessToken $pat): PersonalAccessToken
    {
        $statement = $this->connection->prepare('INSERT INTO personal_access_tokens (id, user_id, name, token, abilities, expires_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $statement->execute([
            $pat->id, $pat->user_id, $pat->name, $pat->token,
            $pat->abilities, $pat->expiresAt->format('Y-m-d H:i:s'), 
            $pat->createdAt->format('Y-m-d H:i:s'), $pat->updatedAt->format('Y-m-d H:i:s')
        ]);

        return $pat;
    }

    public function update(PersonalAccessToken $pat): PersonalAccessToken
    {
        $statement = $this->connection->prepare('UPDATE personal_access_tokens SET last_used_at = ? WHERE id = ?');
        $statement->execute([
            $pat->lastUsedAt->format('Y-m-d H:i:s'), $pat->id
        ]);

        return $pat;
    }

    public function deleteByUserId(string $userId)
    {
        $statement = $this->connection->prepare('DELETE FROM personal_access_tokens WHERE user_id = ?');
        $statement->execute([$userId]);
    }
}
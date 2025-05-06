<?php

namespace Pondra\PhpApiStarterKit\Repositories;

use PDO;
use Pondra\PhpApiStarterKit\Models\PersonalAccessToken;

class PersonalAccessTokenRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
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

    public function deleteByUserId(string $userId)
    {
        $statement = $this->connection->prepare('DELETE FROM personal_access_tokens WHERE user_id = ?');
        $statement->execute([$userId]);
    }
}
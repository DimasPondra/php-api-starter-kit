<?php

namespace Pondra\PhpApiStarterKit\Models;

use DateTime;

class PersonalAccessToken
{
    public string $id;
    public string $user_id;
    public string $name;
    public string $token;
    public string $abilities;
    public DateTime $lastUsedAt;
    public DateTime $expiresAt;
    public DateTime $createdAt;
    public DateTime $updatedAt;
}
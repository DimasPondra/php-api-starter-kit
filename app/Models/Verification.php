<?php

namespace Pondra\PhpApiStarterKit\Models;

use DateTime;

class Verification
{
    public string $id;
    public string $token;
    public DateTime $expiresAt;
    public string $user_id;
    public DateTime $createdAt;
    public DateTime $updatedAt;
}
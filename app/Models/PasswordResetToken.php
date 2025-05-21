<?php

namespace Pondra\PhpApiStarterKit\Models;

use DateTime;

class PasswordResetToken
{
    public string $id;
    public string $email;
    public string $token;
    public DateTime $expiresAt;
    public ?DateTime $createdAt;
}
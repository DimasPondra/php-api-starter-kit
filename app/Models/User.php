<?php

namespace Pondra\PhpApiStarterKit\Models;

use DateTime;

class User
{
    public string $id;
    public string $name;
    public string $email;
    public string $password;
    public ?DateTime $emailVerifiedAt;
    public string $role_id;
    public string $created_at;
    public string $updated_at;
}
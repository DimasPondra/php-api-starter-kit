<?php

namespace Pondra\PhpApiStarterKit\Models;

use DateTime;

class EmailQueue
{
    public string $id;
    public string $name;
    public string $email;
    public string $emailType;
    public string $token;
    public string $status;
    public ?DateTime $createdAt;
    public ?DateTime $sentAt;
}
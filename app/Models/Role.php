<?php

namespace Pondra\PhpApiStarterKit\Models;

use DateTime;

class Role
{
    public string $id;
    public string $name;
    public string $slug;
    public ?DateTime $createdAt;
    public ?DateTime $updatedAt;
}
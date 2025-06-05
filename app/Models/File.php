<?php

namespace Pondra\PhpApiStarterKit\Models;

use DateTime;

class File
{
    public string $id;
    public string $name;
    public string $location;
    public ?DateTime $createdAt;
}
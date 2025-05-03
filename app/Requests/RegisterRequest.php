<?php

namespace Pondra\PhpApiStarterKit\Requests;

class RegisterRequest
{
    public ?string $name = null;
    public ?string $email = null;
    public ?string $password = null;
}
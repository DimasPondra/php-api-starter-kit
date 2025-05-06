<?php

namespace Pondra\PhpApiStarterKit\Requests;

class LoginRequest
{
    public ?string $email = null;
    public ?string $password = null;
}
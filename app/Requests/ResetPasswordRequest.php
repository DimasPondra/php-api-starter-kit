<?php

namespace Pondra\PhpApiStarterKit\Requests;

class ResetPasswordRequest
{
    public ?string $token = null;
    public ?string $password = null;
}
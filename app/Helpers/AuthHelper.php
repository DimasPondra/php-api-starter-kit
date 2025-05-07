<?php

namespace Pondra\PhpApiStarterKit\Helpers;

class AuthHelper
{
    public static function getToken(): ?string
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        
        $token = null;
        $pattern = '/Bearer\s(\S+)/';

        if ($authHeader && preg_match($pattern, $authHeader, $matches)) {
            $token = $matches[1];
        }

        return $token;
    }
}
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

    public static function getClientIp()
    {
        $ipAddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipAddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipAddress = 'UNKNOWN';

        return $ipAddress;
    }
}
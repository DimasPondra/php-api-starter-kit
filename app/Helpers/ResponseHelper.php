<?php

namespace Pondra\PhpApiStarterKit\Helpers;

class ResponseHelper
{
    public static function success(
        string $message, 
        array|object $data = null, 
        int $code = 200, 
        string $status = 'OK'
    ) {
        header("HTTP/1.1 $code $status");
        header('Content-Type: application/json');
        
        echo json_encode([
            'code' => $code,
            'status' => $status,
            'message' => $message,
            'data' => $data
        ]);
    }

    public static function error(
        string $message, 
        array|object|string $errors = null, 
        int $code = 500, 
        string $status = 'Internal Server Error'
    ) {
        header("HTTP/1.1 $code $status");
        header('Content-Type: application/json');
        
        echo json_encode([
            'code' => $code,
            'status' => $status,
            'message' => $message,
            'errors' => $errors
        ]);
    }
}
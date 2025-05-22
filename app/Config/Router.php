<?php

namespace Pondra\PhpApiStarterKit\Config;

use Dotenv\Dotenv;

class Router
{
    private static array $routes = [];

    public static function add
    (
        string $method, 
        string $path, 
        string|object $controller, 
        string $function, 
        array $middlewares = []
    ): void
    {
        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'function' => $function,
            'middleware' => $middlewares
        ];
    }

    public static function run(): void
    {
        $path = '/';

        if (isset($_SERVER['PATH_INFO'])) $path = $_SERVER['PATH_INFO'];
        $method = $_SERVER['REQUEST_METHOD'];

        // CORS Handling
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
        
        $allowedOrigins = explode(',', $_ENV['CORS_ORIGINS']);
        $allowedOrigins = array_map('trim', $allowedOrigins);

        if (in_array($origin, $allowedOrigins)) {
            header("Access-Control-Allow-Origin: $origin");
            header("Access-Control-Allow-Credentials: true"); // If you want to send it with a cookie/session
        } else {
            header("Access-Control-Allow-Origin: null");
        }
        
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");

        // Handle preflight request OPTIONS
        if ($method === 'OPTIONS') {
            http_response_code(204); // No Content
            exit;
        }

        foreach (self::$routes as $route) {
            $pattern = "#^" . $route['path'] . "$#";

            if ($route['method'] == $method && preg_match($pattern, $path, $variables)) {
                
                // call middlewares
                foreach ($route['middleware'] as $middleware) {
                    $instance = new $middleware;
                    $instance->before();
                }

                $function = $route['function'];

                if (is_object($route['controller'])) {
                    $controller = $route['controller'];
                } else {
                    $controller = new $route['controller'];
                }

                array_shift($variables);
                call_user_func_array([$controller, $function], $variables);

                return;
            }
        }

        http_response_code(404);
        echo "Controller not found.";
    }
}
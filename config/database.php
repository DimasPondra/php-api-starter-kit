<?php

use Dotenv\Dotenv;

function getDatabaseConfig(): array
{
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();

    $host = $_ENV['DB_HOST'];
    $port = $_ENV['DB_PORT'];
    $dbname = $_ENV['DB_NAME'];
    $username = $_ENV['DB_USERNAME'];
    $password = $_ENV['DB_PASSWORD'];

    return [
        'database' => [
            'local' => [
                'url' => "mysql:host=$host:$port;dbname=$dbname",
                'username' => $username,
                'password' => $password
            ],
            'production' => [
                'url' => "mysql:host=$host:$port;dbname=$dbname",
                'username' => $username,
                'password' => $password
            ]
        ]
    ];
}
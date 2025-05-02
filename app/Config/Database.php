<?php

namespace Pondra\PhpApiStarterKit\Config;

use Dotenv\Dotenv;
use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo == null) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->load();
    
            $env = $_ENV['APP_ENV'];

            // Create new PDO
            require_once __DIR__ . '/../../config/database.php';
            $config = getDatabaseConfig();

            try {
                self::$pdo = new PDO(
                    $config['database'][$env]['url'],
                    $config['database'][$env]['username'],
                    $config['database'][$env]['password'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $e) {
                // Error log here.

                throw new RuntimeException('Failed to connect database. ' . $e->getMessage());
            }
        }

        return self::$pdo;
    }

    public static function beginTransaction()
    {
        self::$pdo->beginTransaction();
    }

    public static function commitTransaction()
    {
        self::$pdo->commit();
    }

    public static function rollbackTransaction()
    {
        self::$pdo->rollBack(); 
    }
}
<?php

namespace Pondra\PhpApiStarterKit\Config;

use Dotenv\Dotenv;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\SlackWebhookHandler;
use Monolog\Level;
use Monolog\Logger;

class AppLogger
{
    private static $logger;

    public static function getLogger(): Logger
    {
        if (!self::$logger) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->load();
            
            $logger = new Logger('api-logger');
    
            // Daily rotating file log.
            $logger->pushHandler(new RotatingFileHandler(__DIR__ . '/../../storage/logs/monolog/api.log', 0, Level::Debug));
    
            // Slack webhook URL.
            $slackWebhookUrl = $_ENV['LOG_SLACK_WEBHOOK_URL'];
            $slackUsername = $_ENV['LOG_SLACK_USERNAME'];
    
            // Send log ERROR to slack channel
            $logger->pushHandler(new SlackWebhookHandler($slackWebhookUrl, 'php-api-starter-kit', $slackUsername, true, null, false, true, Level::Critical));

            self::$logger = $logger;
        }
    
        return self::$logger;
    }
}
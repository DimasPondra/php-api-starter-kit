<?php

namespace Pondra\PhpApiStarterKit\Helpers;

use DateTime;
use Pondra\PhpApiStarterKit\Config\AppLogger;

class LoggerHelper
{
    private static $logger;

    public static function init()
    {
        if (!self::$logger) {
            self::$logger = AppLogger::getLogger();
        }
    }

    public static function info($message, array $context = [])
    {
        self::init();
        $context = self::formatContext($context);
        self::$logger->info($message, $context);
    }

    public static function notice($message, array $context = [])
    {
        self::init();
        $context = self::formatContext($context);
        self::$logger->notice($message, $context);
    }

    public static function warning($message, array $context = [])
    {
        self::init();
        $context = self::formatContext($context);
        self::$logger->warning($message, $context);
    }

    public static function error($message, array $context = [])
    {
        self::init();
        $context = self::formatContext($context);
        self::$logger->error($message, $context);
    }

    public static function critical($message, array $context = [])
    {
        self::init();
        $context = self::formatContext($context);
        self::$logger->critical($message, $context);
    }

    public static function alert($message, array $context = [])
    {
        self::init();
        $context = self::formatContext($context);
        self::$logger->alert($message, $context);
    }

    public static function emergency($message, array $context = [])
    {
        self::init();
        $context = self::formatContext($context);
        self::$logger->emergency($message, $context);
    }

    private static function formatContext(array $context)
    {
        // $context['user_id'] = 'user_id';
        
        $context['ip_address'] = self::getClientIp();
        $context['user_agent'] = self::getUserAgent();
        $context['datetime'] = DateTimeHelper::nowLocal();

        return $context;
    }

    private static function getClientIp()
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

    private static function getUserAgent()
    {
        $userAgent = '';
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
        } else {
            $userAgent = 'UNKNOWN';
        }

        return $userAgent;
    }
}
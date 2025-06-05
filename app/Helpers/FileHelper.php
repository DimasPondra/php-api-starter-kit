<?php

namespace Pondra\PhpApiStarterKit\Helpers;

use Dotenv\Dotenv;

class FileHelper
{
    public static function reArrayFiles($files): ?array
    {
        if ($files === null) {
            return [];
        }

        $fileArr = [];
        $fileCount = count($files['name']);
        $fileKeys = array_keys($files);
        
        for ($i=0; $i < $fileCount; $i++) { 
            foreach ($fileKeys as $key) {
                $fileArr[$i][$key] = $files[$key][$i];
            }
        }
        
        return $fileArr;
    }

    public static function getExtentionFromFile($name): string
    {
        $extention = pathinfo($name, PATHINFO_EXTENSION);

        return $extention;
    }

    public static function randomName(): string
    {
        $hex = bin2hex(random_bytes(16));
        $random = time() . '-' . $hex;

        return $random;
    }

    public static function getDirectoryRootProject(): string
    {
        return dirname(dirname(__DIR__));
    }

    public static function appUrlFile(): string
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
        
        $appUrl = $_ENV['APP_URL'] . '/uploads/';

        return $appUrl;
    }
}
<?php

namespace Pondra\PhpApiStarterKit\Helpers;

class StringHelper
{
    public static function slug($str): string
    {
        $str = strtolower(trim($str));
        $str = preg_replace('/[^a-z0-9-]/', '-', $str);
        $str = preg_replace('/-+/', "-", $str);
        $str = trim($str, '-');

        return $str;
    }

    public static function capitalize($str): string
    {
        $str = strtolower(trim($str));
        $str = ucwords($str);

        return $str;
    }
}
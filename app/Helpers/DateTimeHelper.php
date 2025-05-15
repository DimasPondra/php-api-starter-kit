<?php

namespace Pondra\PhpApiStarterKit\Helpers;

use DateTime;
use DateTimeZone;

class DateTimeHelper
{
    public static function convertUtcToLocal(string $utc): DateTime
    {
        $utc = new DateTime($utc);

        $jakartaTimeZone = new DateTimeZone('Asia/Jakarta');
        $jakartaDateTime = clone $utc;
        $jakartaDateTime->setTimezone($jakartaTimeZone);

        return $jakartaDateTime;
    }

    public static function nowLocal(): DateTime
    {
        $now = new DateTime('now', new DateTimeZone('Asia/Jakarta'));

        return $now;
    }
}
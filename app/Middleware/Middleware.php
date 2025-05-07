<?php

namespace Pondra\PhpApiStarterKit\Middleware;

interface Middleware
{
    function before(): void;
}
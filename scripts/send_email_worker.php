<?php

use Pondra\PhpApiStarterKit\Jobs\EmailQueueJob;

require_once __DIR__ . '/../vendor/autoload.php';

$job = new EmailQueueJob();
$job->handle();
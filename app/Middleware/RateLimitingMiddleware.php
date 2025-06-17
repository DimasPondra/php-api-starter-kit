<?php

namespace Pondra\PhpApiStarterKit\Middleware;

use Dotenv\Dotenv;
use Pondra\PhpApiStarterKit\Helpers\AuthHelper;
use Pondra\PhpApiStarterKit\Helpers\LoggerHelper;
use Pondra\PhpApiStarterKit\Helpers\ResponseHelper;

class RateLimitingMiddleware implements Middleware
{
    private string $method;
    private string $endpoint;
    private int $limit;
    private int $durationSeconds;

    public function __construct($method, $endpoint, int $limit = 10, int $durationSeconds = 60)
    {
        $this->method = $method;
        $this->endpoint = $endpoint;
        $this->limit = $limit;
        $this->durationSeconds = $durationSeconds;
    }

    public function before(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $host = $_ENV['REDIS_HOST'];
        $port = $_ENV['REDIS_PORT'];
        $username = $_ENV['REDIS_USERNAME'];
        $password = $_ENV['REDIS_PASSWORD'];

        $token = AuthHelper::getToken();
        $keyValue = $token !== null ? $token : AuthHelper::getClientIp();

        try {
            $redis = new \Redis();
            $redis->connect($host, $port);
            $redis->auth([$username, $password]);

            $key = "rate_limit:{$keyValue}:{$this->method}:{$this->endpoint}";
            $current = $redis->get($key);
            
            if ($current === false) {
                $redis->set($key, 1, ['EX' => $this->durationSeconds]);

            } else if($current < $this->limit) {
                $redis->incr($key);
                
            } else {
                $retryAfter = $redis->ttl($key);

                header('X-RateLimit-Limit: ' . $this->limit);
                header('X-RateLimit-Remaining: 0');
                header('Retry-After: ' . $retryAfter);

                ResponseHelper::error('Too many requests.', [
                    'message' => "Rate limit exceeded. Try again in {$retryAfter} seconds."
                ], 429, 'Too Many Requests');

                exit;
            }

        } catch (\Throwable $th) {
            LoggerHelper::emergency('Failed to handle rate limiting.', [
                'action' => 'rate-limiting-middleware',
                'error' => $th->getMessage()
            ]);

            ResponseHelper::error('Something went wrong, Please try again.');
            exit;
        }
    }
}
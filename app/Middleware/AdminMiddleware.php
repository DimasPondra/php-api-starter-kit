<?php

namespace Pondra\PhpApiStarterKit\Middleware;

use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Helpers\AuthHelper;
use Pondra\PhpApiStarterKit\Helpers\ResponseHelper;
use Pondra\PhpApiStarterKit\Repositories\PersonalAccessTokenRepository;

class AdminMiddleware implements Middleware
{
    private PersonalAccessTokenRepository $patRepository;

    public function __construct()
    {
        $this->patRepository = new PersonalAccessTokenRepository(Database::getConnection());
    }

    public function before(): void
    {
        $token = AuthHelper::getToken();

        $pat = $this->patRepository->findByToken($token);
        
        if (!in_array('admin', json_decode($pat->abilities))) {
            ResponseHelper::error("You don’t have access to this resource.", null, 403, 'Forbidden');
            exit;
        }
    }
}
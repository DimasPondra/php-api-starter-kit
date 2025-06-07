<?php

namespace Pondra\PhpApiStarterKit\Middleware;

use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Helpers\AuthHelper;
use Pondra\PhpApiStarterKit\Helpers\LoggerHelper;
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

        $hashToken = hash('sha256', $token);
        $pat = $this->patRepository->findByToken($hashToken);
        
        if (!in_array('admin', json_decode($pat->abilities))) {
            
            LoggerHelper::error('Token verification failed.', [
                'action' => 'admin-middleware',
                'error' => 'from token, your role is not admin.'
            ]);

            ResponseHelper::error("You don’t have access to this resource.", null, 403, 'Forbidden');
            exit;
        }
    }
}
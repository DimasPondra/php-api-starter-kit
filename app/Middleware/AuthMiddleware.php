<?php

namespace Pondra\PhpApiStarterKit\Middleware;

use DateTime;
use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Helpers\AuthHelper;
use Pondra\PhpApiStarterKit\Helpers\ResponseHelper;
use Pondra\PhpApiStarterKit\Repositories\PersonalAccessTokenRepository;
use Pondra\PhpApiStarterKit\Repositories\UserRepository;

class AuthMiddleware implements Middleware
{
    private PersonalAccessTokenRepository $patRepository;
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->patRepository = new PersonalAccessTokenRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());
    }

    public function before(): void
    {
        $token = AuthHelper::getToken();

        if ($token === null) {
            ResponseHelper::error('Authentication token is missing. Please provide a valid token to access this resource.', null, 401, 'Unauthorized');
            exit;
        }
        
        $pat = $this->patRepository->findByToken($token);
        if ($pat === null) {
            ResponseHelper::error('Unauthorized.', ['token' => 'Token invalid.'], 401, 'Unauthorized');
            exit;
        }

        $user = $this->userRepository->findById($pat->user_id);
        if ($user === null) {
            ResponseHelper::error('Unauthorized.', ['token' => 'User not found.'], 401, 'Unauthorized');
            exit;
        }

        if ($pat->expiresAt < new DateTime()) {

            try {
                Database::beginTransaction();

                $this->patRepository->deleteByUserId($user->id);

                Database::commitTransaction();
            } catch (\Throwable $th) {
                Database::rollbackTransaction();

                ResponseHelper::error('Something went wrong, Please try again.');
                exit;
            }

            ResponseHelper::error('Unauthorized.', ['token' => 'Token invalid.'], 401, 'Unauthorized');
            exit;
        }

        $pat->lastUsedAt = new DateTime();

        try {
            Database::beginTransaction();

            $this->patRepository->update($pat);

            Database::commitTransaction();
        } catch (\Throwable $th) {
            Database::rollbackTransaction();

            ResponseHelper::error('Something went wrong, Please try again.');
            exit;
        }
    }
}
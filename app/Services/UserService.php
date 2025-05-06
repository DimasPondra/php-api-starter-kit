<?php

namespace Pondra\PhpApiStarterKit\Services;

use DateTime;
use Exception;
use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Exceptions\ValidationException;
use Pondra\PhpApiStarterKit\Helpers\StringHelper;
use Pondra\PhpApiStarterKit\Models\PersonalAccessToken;
use Pondra\PhpApiStarterKit\Models\User;
use Pondra\PhpApiStarterKit\Repositories\PersonalAccessTokenRepository;
use Pondra\PhpApiStarterKit\Repositories\RoleRepository;
use Pondra\PhpApiStarterKit\Repositories\UserRepository;
use Pondra\PhpApiStarterKit\Requests\LoginRequest;
use Pondra\PhpApiStarterKit\Requests\RegisterRequest;
use Pondra\PhpApiStarterKit\Validations\LoginValidation;
use Pondra\PhpApiStarterKit\Validations\RegisterValidation;
use Ramsey\Uuid\Uuid;

class UserService
{
    private UserRepository $userRepository;
    private RoleRepository $roleRepository;
    private PersonalAccessTokenRepository $patRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        RegisterValidation::setUserRepository($this->userRepository);

        $connection = Database::getConnection();
        $this->roleRepository = new RoleRepository($connection);
        $this->patRepository = new PersonalAccessTokenRepository($connection);
    }

    public function register(RegisterRequest $request)
    {
        RegisterValidation::validation($request);

        $role = $this->roleRepository->findByName('Customer');
        if ($role === null) {
            throw new Exception('Role customer uncreated.', 500);
        }

        try {
            Database::beginTransaction();

            date_default_timezone_set("Asia/Jakarta");

            $user = new User();
            $user->id = Uuid::uuid4();
            $user->name = StringHelper::capitalize($request->name);
            $user->email = StringHelper::lower($request->email);
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);
            $user->role_id = $role->id;
            $user->created_at = date('Y-m-d H:i:s');
            $user->updated_at = date('Y-m-d H:i:s');

            $this->userRepository->save($user);

            Database::commitTransaction();
            
            return [
                'message' => 'Register new account successfully.',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ];
        } catch (\Throwable $th) {
            Database::rollbackTransaction();

            throw $th;
        }
    }

    public function login(LoginRequest $request)
    {
        LoginValidation::validation($request);

        $user = $this->userRepository->findByEmail($request->email);
        
        if ($user === null || !password_verify($request->password, $user->password)) {
            throw new ValidationException(null, 'Email or password is invalid.', 401);
        }

        $role = $this->roleRepository->findById($user->role_id);

        $abilities[] = $role->slug;
        $abilitiesJson = json_encode($abilities);

        try {
            Database::beginTransaction();

            $this->patRepository->deleteByUserId($user->id);

            date_default_timezone_set("Asia/Jakarta");
            $dateTimeNow = new DateTime();

            $pat = new PersonalAccessToken();
            $pat->id = Uuid::uuid4();
            $pat->user_id = $user->id;
            $pat->name = 'token-auth-php-api';
            $pat->token = Uuid::uuid4();
            $pat->abilities = $abilitiesJson;
            $pat->expiresAt = $dateTimeNow->modify('+1 day');
            $pat->createdAt = new DateTime();
            $pat->updatedAt = new DateTime();

            $this->patRepository->save($pat);

            Database::commitTransaction();

            return [
                'message' => 'Login successfully.',
                'data' => [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'token' => $pat->token
                ]
            ];
        } catch (\Throwable $th) {
            Database::rollbackTransaction();

            throw $th;
        }
    }
}
<?php

namespace Pondra\PhpApiStarterKit\Services;

use Exception;
use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Helpers\StringHelper;
use Pondra\PhpApiStarterKit\Models\User;
use Pondra\PhpApiStarterKit\Repositories\RoleRepository;
use Pondra\PhpApiStarterKit\Repositories\UserRepository;
use Pondra\PhpApiStarterKit\Requests\RegisterRequest;
use Pondra\PhpApiStarterKit\Validations\RegisterValidation;
use Ramsey\Uuid\Uuid;

class UserService
{
    private UserRepository $userRepository;
    private RoleRepository $roleRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        RegisterValidation::setUserRepository($this->userRepository);

        $connection = Database::getConnection();
        $this->roleRepository = new RoleRepository($connection);
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
}
<?php

namespace Pondra\PhpApiStarterKit\Services;

use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Helpers\ResponseHelper;
use Pondra\PhpApiStarterKit\Models\User;
use Pondra\PhpApiStarterKit\Repositories\UserRepository;
use Pondra\PhpApiStarterKit\Requests\RegisterRequest;
use Pondra\PhpApiStarterKit\Validations\RegisterValidation;
use Ramsey\Uuid\Uuid;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        RegisterValidation::setUserRepository($this->userRepository);
    }

    public function register(RegisterRequest $request)
    {
        RegisterValidation::validation($request);

        try {
            Database::beginTransaction();

            date_default_timezone_set("Asia/Jakarta");

            $user = new User();
            $user->id = Uuid::uuid4();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);
            $user->role_id = 'ac176755-288b-11f0-b47e-34415d957996';
            $user->created_at = date('Y-m-d H:i:s');
            $user->updated_at = date('Y-m-d H:i:s');

            $this->userRepository->save($user);

            Database::commitTransaction();
            
            $response = ResponseHelper::success('Register new account successfully.', [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]);

            return $response;
        } catch (\Throwable $th) {
            Database::rollbackTransaction();

            $response = ResponseHelper::error('Something went wrong.', $th->getMessage());

            return $response;
        }
    }
}
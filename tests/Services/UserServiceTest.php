<?php

namespace Pondra\PhpApiStarterKit\Services;

use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;
use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Exceptions\ValidationException;
use Pondra\PhpApiStarterKit\Models\Role;
use Pondra\PhpApiStarterKit\Repositories\RoleRepository;
use Pondra\PhpApiStarterKit\Repositories\UserRepository;
use Pondra\PhpApiStarterKit\Requests\LoginRequest;
use Pondra\PhpApiStarterKit\Requests\RegisterRequest;

class UserServiceTest extends TestCase
{
    private UserService $userService;
    private RoleRepository $roleRepository;

    protected function setUp(): void
    {
        $this->roleRepository = new RoleRepository(Database::getConnection());

        $userRepo = new UserRepository(Database::getConnection());
        $this->userService = new UserService($userRepo);

        $userRepo->deleteAll();
        $this->roleRepository->deleteAll();
    }

    public function testRegisterValidationIfDataIsInvalid()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionCode(422);
        $this->expectExceptionMessage('Validation failed.');

        $request = new RegisterRequest();
        $request->name = '';
        $request->email = '';
        $request->password = '';

        $this->userService->register($request);
    }

    public function testRegisterFailedWhenCustomerRoleIsUncreated()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionCode(500);
        $this->expectExceptionMessage('Role customer uncreated.');

        $request = new RegisterRequest();
        $request->name = 'dimas';
        $request->email = 'dimas@mail.com';
        $request->password = 'secret';

        $this->userService->register($request);
    }

    public function testRegisterSuccess()
    {
        $role = new Role();
        $role->id = '123';
        $role->name = 'Customer';
        $role->slug = 'customer';
        $role->createdAt = new DateTime();
        $role->updatedAt = new DateTime();

        $this->roleRepository->save($role);

        $request = new RegisterRequest();
        $request->name = 'dimas';
        $request->email = 'dimas@mail.com';
        $request->password = 'secret';

        $result = $this->userService->register($request);

        self::assertArrayHasKey('message', $result);
        self::assertArrayHasKey('data', $result);

        self::assertEquals($result['message'], 'Register new account successfully.');
    }

    public function testLoginValidationIfDataIsInvalid()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionCode(422);
        $this->expectExceptionMessage('Validation failed.');

        $request = new LoginRequest();
        $request->email = '';
        $request->password = '';

        $this->userService->login($request);
    }

    public function testLoginFailedWhenCredentialIsInvalidOrUserNotFound()
    {
        $role = new Role();
        $role->id = '123';
        $role->name = 'Customer';
        $role->slug = 'customer';
        $role->createdAt = new DateTime();
        $role->updatedAt = new DateTime();

        $this->roleRepository->save($role);

        $registerRequest = new RegisterRequest();
        $registerRequest->name = 'pondra';
        $registerRequest->email = 'dimas@mail.com';
        $registerRequest->password = 'rahasia';

        $this->userService->register($registerRequest);

        $this->expectException(Exception::class);
        $this->expectExceptionCode(401);
        $this->expectExceptionMessage('Email or password is invalid.');

        $request = new LoginRequest();
        $request->email = 'dimas@mail.com';
        $request->password = 'secret';

        $this->userService->login($request);
    }

    public function testLoginSuccess()
    {
        $role = new Role();
        $role->id = '123';
        $role->name = 'Customer';
        $role->slug = 'customer';
        $role->createdAt = new DateTime();
        $role->updatedAt = new DateTime();

        $this->roleRepository->save($role);

        $registerRequest = new RegisterRequest();
        $registerRequest->name = 'pondra';
        $registerRequest->email = 'dimas@mail.com';
        $registerRequest->password = 'rahasia';

        $this->userService->register($registerRequest);

        $request = new LoginRequest();
        $request->email = 'dimas@mail.com';
        $request->password = 'rahasia';

        $result = $this->userService->login($request);

        self::assertArrayHasKey('message', $result);
        self::assertArrayHasKey('data', $result);

        self::assertIsArray($result);
        self::assertEquals($result['message'], 'Login successfully.');
        self::assertEquals($result['data']['name'], $registerRequest->name);
        self::assertArrayHasKey('user_id', $result['data']);
        self::assertArrayHasKey('token', $result['data']);
    }

    public function testGetUserFromTokenSuccess()
    {
        $role = new Role();
        $role->id = '123';
        $role->name = 'Customer';
        $role->slug = 'customer';
        $role->createdAt = new DateTime();
        $role->updatedAt = new DateTime();

        $this->roleRepository->save($role);

        $registerRequest = new RegisterRequest();
        $registerRequest->name = 'pondra';
        $registerRequest->email = 'dimas@mail.com';
        $registerRequest->password = 'rahasia';

        $this->userService->register($registerRequest);

        $request = new LoginRequest();
        $request->email = 'dimas@mail.com';
        $request->password = 'rahasia';

        $result = $this->userService->login($request);

        $token = $result['data']['token'];
        $hashToken = hash('sha256', $token);

        $getUserFromToken = $this->userService->getUserFromToken($hashToken);

        self::assertArrayHasKey('message', $getUserFromToken);
        self::assertArrayHasKey('data', $getUserFromToken);

        self::assertIsArray($getUserFromToken);
        self::assertEquals($getUserFromToken['message'], 'User retrieved successfully.');
        self::assertEquals($getUserFromToken['data']['name'], $registerRequest->name);
        self::assertEquals($getUserFromToken['data']['email'], $registerRequest->email);
    }

    public function testDeleteUserTokenSuccess()
    {
        $role = new Role();
        $role->id = '123';
        $role->name = 'Customer';
        $role->slug = 'customer';
        $role->createdAt = new DateTime();
        $role->updatedAt = new DateTime();

        $this->roleRepository->save($role);

        $registerRequest = new RegisterRequest();
        $registerRequest->name = 'pondra';
        $registerRequest->email = 'dimas@mail.com';
        $registerRequest->password = 'rahasia';

        $this->userService->register($registerRequest);

        $request = new LoginRequest();
        $request->email = 'dimas@mail.com';
        $request->password = 'rahasia';

        $result = $this->userService->login($request);

        $token = $result['data']['token'];
        $hashToken = hash('sha256', $token);

        $responseAfterDeleteUserToken = $this->userService->deleteUserToken($hashToken);

        self::assertArrayHasKey('message', $responseAfterDeleteUserToken);
        self::assertArrayHasKey('data', $responseAfterDeleteUserToken);

        self::assertIsArray($responseAfterDeleteUserToken);
        self::assertEquals($responseAfterDeleteUserToken['message'], 'You have successfully logged out.');
        self::assertEquals($responseAfterDeleteUserToken['data'], null);
    }
}
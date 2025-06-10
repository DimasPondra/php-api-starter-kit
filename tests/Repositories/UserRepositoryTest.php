<?php

namespace Pondra\PhpApiStarterKit\Repositories;

use DateTime;
use PHPUnit\Framework\TestCase;
use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Helpers\StringHelper;
use Pondra\PhpApiStarterKit\Models\Role;
use Pondra\PhpApiStarterKit\Models\User;
use Ramsey\Uuid\Uuid;

class UserRepositoryTest extends TestCase
{
    private UserRepository $userRepository;
    private RoleRepository $roleRepository;

    protected function setUp(): void
    {
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->roleRepository = new RoleRepository(Database::getConnection());

        $this->userRepository->deleteAll();
        $this->roleRepository->deleteAll();
    }

    public function testCreateUserSuccess()
    {
        $role = new Role();
        $role->id = Uuid::uuid4()->toString();
        $role->name = 'Customer';
        $role->slug = StringHelper::slug('Customer');
        $role->createdAt = new DateTime();
        $role->updatedAt = new DateTime();

        $this->roleRepository->save($role);

        $user = new User();
        $user->id = Uuid::uuid4()->toString();
        $user->name = 'Dimas Pondra Oktafianto';
        $user->email = 'dimas@mail.com';
        $user->password = password_hash('secret', PASSWORD_BCRYPT);
        $user->role_id = $role->id;
        $user->createdAt = new DateTime();
        $user->updatedAt = new DateTime();

        $result = $this->userRepository->save($user);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->email, $result->email);
    }

    public function testFindUserByIdSuccess()
    {
        $role = new Role();
        $role->id = Uuid::uuid4()->toString();
        $role->name = 'Customer';
        $role->slug = StringHelper::slug('Customer');
        $role->createdAt = new DateTime();
        $role->updatedAt = new DateTime();

        $this->roleRepository->save($role);

        $user = new User();
        $user->id = Uuid::uuid4()->toString();
        $user->name = 'Dimas Pondra Oktafianto';
        $user->email = 'dimas@mail.com';
        $user->password = password_hash('secret', PASSWORD_BCRYPT);
        $user->role_id = $role->id;
        $user->createdAt = new DateTime();
        $user->updatedAt = new DateTime();

        $this->userRepository->save($user);

        $result = $this->userRepository->findById($user->id);

        self::assertIsObject($result);
        self::assertObjectHasAttribute('id', $result);
        self::assertObjectHasAttribute('name', $result);
        self::assertObjectHasAttribute('email', $result);
        self::assertObjectHasAttribute('role_id', $result);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->email, $result->email);
        self::assertEquals($user->role_id, $result->role_id);
    }

    public function testFindUserByEmailSuccess()
    {
        $role = new Role();
        $role->id = Uuid::uuid4()->toString();
        $role->name = 'Customer';
        $role->slug = StringHelper::slug('Customer');
        $role->createdAt = new DateTime();
        $role->updatedAt = new DateTime();

        $this->roleRepository->save($role);

        $user = new User();
        $user->id = Uuid::uuid4()->toString();
        $user->name = 'Dimas Pondra Oktafianto';
        $user->email = 'dimas@mail.com';
        $user->password = password_hash('secret', PASSWORD_BCRYPT);
        $user->role_id = $role->id;
        $user->createdAt = new DateTime();
        $user->updatedAt = new DateTime();

        $this->userRepository->save($user);

        $result = $this->userRepository->findByEmail($user->email);

        self::assertIsObject($result);
        self::assertObjectHasAttribute('id', $result);
        self::assertObjectHasAttribute('name', $result);
        self::assertObjectHasAttribute('email', $result);
        self::assertObjectHasAttribute('role_id', $result);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->email, $result->email);
        self::assertEquals($user->role_id, $result->role_id);
    }

    public function testVerifyEmailSuccess()
    {
        $role = new Role();
        $role->id = Uuid::uuid4()->toString();
        $role->name = 'Customer';
        $role->slug = StringHelper::slug('Customer');
        $role->createdAt = new DateTime();
        $role->updatedAt = new DateTime();

        $this->roleRepository->save($role);

        $user = new User();
        $user->id = Uuid::uuid4()->toString();
        $user->name = 'Dimas Pondra Oktafianto';
        $user->email = 'dimas@mail.com';
        $user->password = password_hash('secret', PASSWORD_BCRYPT);
        $user->role_id = $role->id;
        $user->createdAt = new DateTime();
        $user->updatedAt = new DateTime();

        $getUser = $this->userRepository->save($user);

        $getUser->emailVerifiedAt = new DateTime();
        $getUser->updatedAt = new DateTime();

        $result = $this->userRepository->verifyEmail($getUser);

        self::assertIsObject($result);
        self::assertObjectHasAttribute('id', $result);
        self::assertObjectHasAttribute('name', $result);
        self::assertObjectHasAttribute('email', $result);
        self::assertObjectHasAttribute('emailVerifiedAt', $result);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->email, $result->email);
        self::assertNotNull($result->emailVerifiedAt);
    }

    public function testResetPasswordSuccess()
    {
        $role = new Role();
        $role->id = Uuid::uuid4()->toString();
        $role->name = 'Customer';
        $role->slug = StringHelper::slug('Customer');
        $role->createdAt = new DateTime();
        $role->updatedAt = new DateTime();

        $this->roleRepository->save($role);

        $user = new User();
        $user->id = Uuid::uuid4()->toString();
        $user->name = 'Dimas Pondra Oktafianto';
        $user->email = 'dimas@mail.com';
        $user->password = password_hash('secret', PASSWORD_BCRYPT);
        $user->role_id = $role->id;
        $user->createdAt = new DateTime();
        $user->updatedAt = new DateTime();

        $getUser = $this->userRepository->save($user);

        $getUser->password = password_hash('rahasia', PASSWORD_BCRYPT);
        $getUser->updatedAt = new DateTime();

        $result = $this->userRepository->resetPassword($getUser);

        self::assertIsObject($result);
        self::assertObjectHasAttribute('password', $result);

        self::assertTrue(password_verify('rahasia', $result->password));
    }
}
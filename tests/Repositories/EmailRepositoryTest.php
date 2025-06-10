<?php

namespace Pondra\PhpApiStarterKit\Repositories;

use DateTime;
use PHPUnit\Framework\TestCase;
use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Helpers\StringHelper;
use Pondra\PhpApiStarterKit\Models\Role;
use Pondra\PhpApiStarterKit\Models\User;
use Pondra\PhpApiStarterKit\Models\Verification;
use Ramsey\Uuid\Uuid;

class EmailRepositoryTest extends TestCase
{
    private EmailRepository $emailRepository;
    private RoleRepository $roleRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->emailRepository = new EmailRepository(Database::getConnection());
        $this->roleRepository = new RoleRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());

        $this->emailRepository->deleteAll();
        $this->userRepository->deleteAll();
        $this->roleRepository->deleteAll();
    }

    public function testCreateEmailVerificationSuccess()
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

        $verification = new Verification();
        $verification->id = Uuid::uuid4()->toString();
        $verification->token = 'token';
        $verification->expiresAt = new DateTime();
        $verification->user_id = $user->id;
        $verification->createdAt = new DateTime();
        $verification->updatedAt = new DateTime();

        $result = $this->emailRepository->save($verification);

        self::assertEquals($verification->id, $result->id);
        self::assertEquals($verification->token, $result->token);
    }

    public function testFindEmailVerificationByTokenSuccess()
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

        $verification = new Verification();
        $verification->id = Uuid::uuid4()->toString();
        $verification->token = 'token';
        $verification->expiresAt = new DateTime();
        $verification->user_id = $user->id;
        $verification->createdAt = new DateTime();
        $verification->updatedAt = new DateTime();

        $this->emailRepository->save($verification);

        $result = $this->emailRepository->findByToken($verification->token);

        self::assertEquals($verification->id, $result->id);
        self::assertEquals($verification->token, $result->token);
    }

    public function testDeleteEmailVerificationByUserIdSuccess()
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

        $verification = new Verification();
        $verification->id = Uuid::uuid4()->toString();
        $verification->token = 'token';
        $verification->expiresAt = new DateTime();
        $verification->user_id = $user->id;
        $verification->createdAt = new DateTime();
        $verification->updatedAt = new DateTime();

        $this->emailRepository->save($verification);

        $this->emailRepository->deleteByUserId($user->id);

        $result = $this->emailRepository->findByToken($verification->token);

        self::assertIsNotObject($result);
        self::assertNull($result);
    }
}
<?php

namespace Pondra\PhpApiStarterKit\Services;

use DateTime;
use PHPUnit\Framework\TestCase;
use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Exceptions\ValidationException;
use Pondra\PhpApiStarterKit\Models\PersonalAccessToken;
use Pondra\PhpApiStarterKit\Models\Role;
use Pondra\PhpApiStarterKit\Models\User;
use Pondra\PhpApiStarterKit\Repositories\EmailRepository;
use Pondra\PhpApiStarterKit\Repositories\PersonalAccessTokenRepository;
use Pondra\PhpApiStarterKit\Repositories\RoleRepository;
use Pondra\PhpApiStarterKit\Repositories\UserRepository;

class EmailServiceTest extends TestCase
{
    private EmailService $emailService;
    private RoleRepository $roleRepository;
    private UserRepository $userRepository;
    private PersonalAccessTokenRepository $patRepository;

    protected function setUp(): void
    {
        $emailRepo = new EmailRepository(Database::getConnection());
        $this->emailService = new EmailService($emailRepo);

        $this->roleRepository = new RoleRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->patRepository = new PersonalAccessTokenRepository(Database::getConnection());

        $emailRepo->deleteAll();
        $this->patRepository->deleteAll();
        $this->userRepository->deleteAll();
        $this->roleRepository->deleteAll();
    }

    public function testSendEmailVerificationFailed()
    {
        $role = new Role();
        $role->id  = '1';
        $role->name = 'Customer';
        $role->slug = 'customer';
        $role->createdAt = new DateTime();
        $role->updatedAt = new DateTime();

        $this->roleRepository->save($role);

        $user = new User();
        $user->id = '1';
        $user->name = 'pondra';
        $user->email = 'pondra@mail.com';
        $user->password = 'secret';
        $user->role_id = '1';
        $user->createdAt = new DateTime();
        $user->updatedAt = new DateTime();

        $this->userRepository->save($user);

        $user->emailVerifiedAt = new DateTime();

        $this->userRepository->verifyEmail($user);

        $hashToken = hash('sha256', 'token');

        $pat = new PersonalAccessToken();
        $pat->id = '1';
        $pat->user_id = $user->id;
        $pat->name = 'auth-token';
        $pat->token = $hashToken;
        $pat->abilities = json_encode('customer');
        $pat->expiresAt = new DateTime();
        $pat->createdAt = new DateTime();
        $pat->updatedAt = new DateTime();

        $this->patRepository->save($pat); 

        $this->expectException(ValidationException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('Email has been verified.');

        $this->emailService->sendVerificationEmail($hashToken);
    }

    public function testSendEmailVerificationSuccess()
    {
        $role = new Role();
        $role->id  = '1';
        $role->name = 'Customer';
        $role->slug = 'customer';
        $role->createdAt = new DateTime();
        $role->updatedAt = new DateTime();

        $this->roleRepository->save($role);

        $user = new User();
        $user->id = '1';
        $user->name = 'pondra';
        $user->email = 'pondra@mail.com';
        $user->password = 'secret';
        $user->role_id = '1';
        $user->createdAt = new DateTime();
        $user->updatedAt = new DateTime();

        $this->userRepository->save($user);

        $hashToken = hash('sha256', 'token');

        $pat = new PersonalAccessToken();
        $pat->id = '1';
        $pat->user_id = $user->id;
        $pat->name = 'auth-token';
        $pat->token = $hashToken;
        $pat->abilities = json_encode('customer');
        $pat->expiresAt = new DateTime();
        $pat->createdAt = new DateTime();
        $pat->updatedAt = new DateTime();

        $this->patRepository->save($pat);

        $result = $this->emailService->sendVerificationEmail($hashToken);

        self::assertIsArray($result);
        self::assertArrayHasKey('message', $result);
        self::assertArrayHasKey('data', $result);

        self::assertEquals($result['message'], 'Successfully send email verification.');
        self::assertNull($result['data']);
    }
}
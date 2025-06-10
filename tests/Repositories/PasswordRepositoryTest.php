<?php

namespace Pondra\PhpApiStarterKit\Repositories;

use DateTime;
use PHPUnit\Framework\TestCase;
use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Models\PasswordResetToken;
use Ramsey\Uuid\Uuid;

class PasswordRepositoryTest extends TestCase
{
    private PasswordRepository $passwordRepository;

    protected function setUp(): void
    {
        $this->passwordRepository = new PasswordRepository(Database::getConnection());

        $this->passwordRepository->deleteAll();
    }

    public function testCreatePasswordResetSuccess()
    {
        $prt = new PasswordResetToken();
        $prt->id = Uuid::uuid4()->toString();
        $prt->email = 'dimas@mail.com';
        $prt->token = 'token';
        $prt->expiresAt = new DateTime();
        $prt->createdAt = new DateTime();

        $result = $this->passwordRepository->save($prt);

        self::assertEquals($prt->id, $result->id);
        self::assertEquals($prt->token, $result->token);
    }

    public function testFindPasswordResetByTokenSuccess()
    {
        $prt = new PasswordResetToken();
        $prt->id = Uuid::uuid4()->toString();
        $prt->email = 'dimas@mail.com';
        $prt->token = 'token';
        $prt->expiresAt = new DateTime();
        $prt->createdAt = new DateTime();

        $this->passwordRepository->save($prt);

        $result = $this->passwordRepository->findByToken($prt->token);

        self::assertEquals($prt->id, $result->id);
        self::assertEquals($prt->token, $result->token);
    }

    public function testDeletePasswordResetByEmailSuccess()
    {
        $prt = new PasswordResetToken();
        $prt->id = Uuid::uuid4()->toString();
        $prt->email = 'dimas@mail.com';
        $prt->token = 'token';
        $prt->expiresAt = new DateTime();
        $prt->createdAt = new DateTime();

        $this->passwordRepository->save($prt);

        $this->passwordRepository->deleteByEmail($prt->email);

        $result = $this->passwordRepository->findByToken($prt->token);

        self::assertIsNotObject($result);
        self::assertNull($result);
    }
}
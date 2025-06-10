<?php

namespace Pondra\PhpApiStarterKit\Repositories;

use DateTime;
use PHPUnit\Framework\TestCase;
use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Models\PersonalAccessToken;
use Ramsey\Uuid\Uuid;

class PersonalAccessTokenRepositoryTest extends TestCase
{
    private PersonalAccessTokenRepository $patRepository;

    protected function setUp(): void
    {
        $this->patRepository = new PersonalAccessTokenRepository(Database::getConnection());

        $this->patRepository->deleteAll();
    }

    public function testCreatePersonalAccessTokenSuccess()
    {
        $pat = new PersonalAccessToken();
        $pat->id = Uuid::uuid4()->toString();
        $pat->user_id = '123';
        $pat->name = 'token-name';
        $pat->abilities = json_encode('customer');
        $pat->token = 'token';
        $pat->expiresAt = new DateTime();
        $pat->createdAt = new DateTime();
        $pat->updatedAt = new DateTime();

        $result = $this->patRepository->save($pat);

        self::assertEquals($pat->id, $result->id);
        self::assertEquals($pat->token, $result->token);
    }

    public function testFindPersonalAccessTokenByTokenSuccess()
    {
        $pat = new PersonalAccessToken();
        $pat->id = Uuid::uuid4()->toString();
        $pat->user_id = '123';
        $pat->name = 'token-name';
        $pat->abilities = json_encode('customer');
        $pat->token = 'token';
        $pat->expiresAt = new DateTime();
        $pat->createdAt = new DateTime();
        $pat->updatedAt = new DateTime();

        $this->patRepository->save($pat);

        $result = $this->patRepository->findByToken($pat->token);

        self::assertIsObject($result);
        self::assertEquals($pat->id, $result->id);
    }

    public function testUpdatePersonalAccessTokenSuccess()
    {
        $pat = new PersonalAccessToken();
        $pat->id = Uuid::uuid4()->toString();
        $pat->user_id = '123';
        $pat->name = 'token-name';
        $pat->abilities = json_encode('customer');
        $pat->token = 'token';
        $pat->expiresAt = new DateTime();
        $pat->createdAt = new DateTime();
        $pat->updatedAt = new DateTime();

        $getPersonalAccessToken = $this->patRepository->save($pat);

        $getPersonalAccessToken->lastUsedAt = new DateTime();

        $result = $this->patRepository->update($getPersonalAccessToken);

        self::assertIsObject($result);
        self::assertEquals($pat->id, $result->id);
        self::assertNotNull($result->lastUsedAt);
    }

    public function testDeletePersonalAccessTokenByUserIdSuccess()
    {
        $token = 'token';

        $pat = new PersonalAccessToken();
        $pat->id = Uuid::uuid4()->toString();
        $pat->user_id = '123';
        $pat->name = 'token-name';
        $pat->abilities = json_encode('customer');
        $pat->token = $token;
        $pat->expiresAt = new DateTime();
        $pat->createdAt = new DateTime();
        $pat->updatedAt = new DateTime();

        $this->patRepository->save($pat);

        $this->patRepository->deleteByUserId($pat->user_id);

        $result = $this->patRepository->findByToken($token);

        self::assertIsNotObject($result);
        self::assertNull($result);
    }
}
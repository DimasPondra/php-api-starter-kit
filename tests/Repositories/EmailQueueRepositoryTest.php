<?php

namespace Pondra\PhpApiStarterKit\Repositories;

use DateTime;
use PHPUnit\Framework\TestCase;
use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Models\EmailQueue;

class EmailQueueRepositoryTest extends TestCase
{
    private EmailQueueRepository $emailQueueRepository;

    protected function setUp(): void
    {
        $this->emailQueueRepository = new EmailQueueRepository(Database::getConnection());

        $this->emailQueueRepository->deleteAll();
    }

    public function testCreateEmailQueueSuccess()
    {
        $emailQueue = new EmailQueue();
        $emailQueue->id = '123';
        $emailQueue->name = 'dimas';
        $emailQueue->email = 'dimas@mail.com';
        $emailQueue->emailType = 'verification_email';
        $emailQueue->token = 'token';
        $emailQueue->status = 'pending';
        $emailQueue->createdAt = new DateTime();
        $emailQueue->sentAt = null;

        $result = $this->emailQueueRepository->save($emailQueue);

        self::assertEquals($emailQueue->id, $result->id);
        self::assertEquals($emailQueue->token, $result->token);
    }

    public function testUpdateEmailQueueSuccess()
    {
        $emailQueue = new EmailQueue();
        $emailQueue->id = '123';
        $emailQueue->name = 'dimas';
        $emailQueue->email = 'dimas@mail.com';
        $emailQueue->emailType = 'verification_email';
        $emailQueue->token = 'token';
        $emailQueue->status = 'pending';
        $emailQueue->createdAt = new DateTime();
        $emailQueue->sentAt = null;

        $getEmailQueue = $this->emailQueueRepository->save($emailQueue);

        $getEmailQueue->status = 'sent';
        $getEmailQueue->sentAt = new DateTime();

        $result = $this->emailQueueRepository->update($getEmailQueue);

        self::assertEquals($emailQueue->id, $result->id);
        self::assertEquals($emailQueue->token, $result->token);
        self::assertEquals('sent', $result->status);
        self::assertNotNull($result->sentAt);
    }
}
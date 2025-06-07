<?php

namespace Pondra\PhpApiStarterKit\Services;

use DateTime;
use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Exceptions\ValidationException;
use Pondra\PhpApiStarterKit\Helpers\DateTimeHelper;
use Pondra\PhpApiStarterKit\Helpers\LoggerHelper;
use Pondra\PhpApiStarterKit\Models\EmailQueue;
use Pondra\PhpApiStarterKit\Models\Verification;
use Pondra\PhpApiStarterKit\Repositories\EmailQueueRepository;
use Pondra\PhpApiStarterKit\Repositories\EmailRepository;
use Pondra\PhpApiStarterKit\Repositories\PersonalAccessTokenRepository;
use Pondra\PhpApiStarterKit\Repositories\UserRepository;
use Pondra\PhpApiStarterKit\Requests\VerifyEmailRequest;
use Pondra\PhpApiStarterKit\Validations\VerifyEmailValidation;
use Ramsey\Uuid\Uuid;

class EmailService
{
    private EmailRepository $emailRepository;
    private PersonalAccessTokenRepository $patRepository;
    private UserRepository $userRepository;
    private EmailQueueRepository $emailQueueRepository;

    public function __construct(EmailRepository $emailRepository)
    {
        $this->emailRepository = $emailRepository;

        $connection = Database::getConnection();
        $this->patRepository = new PersonalAccessTokenRepository($connection);
        $this->userRepository = new UserRepository($connection);
        $this->emailQueueRepository = new EmailQueueRepository($connection);
    }
    
    public function sendVerificationEmail(string $token)
    {
        $pat = $this->patRepository->findByToken($token);
        $user = $this->userRepository->findById($pat->user_id);

        if ($user->emailVerifiedAt !== null) {
            throw new ValidationException(null, 'Email has been verified.', 400);
        }

        do {
            $token = Uuid::uuid4()->toString();
            $hashToken = hash('sha256', $token);
        } while ($this->emailRepository->findByToken($hashToken));

        try {
            Database::beginTransaction();

            $this->emailRepository->deleteByUserId($user->id);

            $dateTimeNow = new DateTime();

            $verification = new Verification();
            $verification->id = Uuid::uuid4();
            $verification->token = $hashToken;
            $verification->expiresAt = $dateTimeNow->modify('+1 day');
            $verification->user_id = $user->id;
            $verification->createdAt = new DateTime();
            $verification->updatedAt = new DateTime();

            $this->emailRepository->save($verification);

            $emailQueue = new EmailQueue();
            $emailQueue->id = Uuid::uuid4();
            $emailQueue->name = $user->name;
            $emailQueue->email = $user->email;
            $emailQueue->emailType = 'verification_email';
            $emailQueue->token = $token;
            $emailQueue->createdAt = new DateTime();

            $this->emailQueueRepository->save($emailQueue);

            Database::commitTransaction();

            LoggerHelper::info('Verification created successfully', [
                'action' => 'send-verification',
                'model' => 'Verification',
                'data' => $verification
            ]);

            LoggerHelper::info('Email queue created successfully', [
                'action' => 'send-verification',
                'model' => 'EmailQueue',
                'data' => $emailQueue
            ]);
            
            return [
                'message' => 'Successfully send email verification.',
                'data' => null
            ];
        } catch (\Throwable $th) {
            Database::rollbackTransaction();

            throw $th;
        }
    }

    public function verifyEmail(VerifyEmailRequest $request)
    {
        VerifyEmailValidation::validation($request);

        $hashToken = hash('sha256', $request->token);
        $verification = $this->emailRepository->findByToken($hashToken);

        if ($verification === null) {
            throw new ValidationException(null, 'Token is invalid.', 400);
        }

        if ($verification->expiresAt < DateTimeHelper::nowLocal()) {
            throw new ValidationException(null, 'Token is expired.', 400);
        }

        $user = $this->userRepository->findById($verification->user_id);

        try {
            Database::beginTransaction();

            $user->emailVerifiedAt = new DateTime();
            $user->updatedAt = new DateTime();

            $this->userRepository->verifyEmail($user);
            
            $this->emailRepository->deleteByUserId($user->id);

            Database::commitTransaction();

            LoggerHelper::notice('Verify email user successfully', []);

            return [
                'message' => 'Successfully verify email.',
                'data' => null
            ];
        } catch (\Throwable $th) {
            Database::rollbackTransaction();

            throw $th;
        }
    }
}
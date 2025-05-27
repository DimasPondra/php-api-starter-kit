<?php

namespace Pondra\PhpApiStarterKit\Services;

use DateTime;
use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Exceptions\ValidationException;
use Pondra\PhpApiStarterKit\Helpers\DateTimeHelper;
use Pondra\PhpApiStarterKit\Models\Verification;
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

    public function __construct(EmailRepository $emailRepository)
    {
        $this->emailRepository = $emailRepository;

        $connection = Database::getConnection();
        $this->patRepository = new PersonalAccessTokenRepository($connection);
        $this->userRepository = new UserRepository($connection);
    }
    
    public function sendVerificationEmail(string $token)
    {
        $pat = $this->patRepository->findByToken($token);
        $user = $this->userRepository->findById($pat->user_id);

        if ($user->emailVerifiedAt !== null) {
            throw new ValidationException(null, 'Email has been verified.', 400);
        }

        $tokenVerification = Uuid::uuid4();

        try {
            Database::beginTransaction();

            $this->emailRepository->deleteByUserId($user->id);

            $dateTimeNow = new DateTime();

            $verification = new Verification();
            $verification->id = Uuid::uuid4();
            $verification->token = $tokenVerification;
            $verification->expiresAt = $dateTimeNow->modify('+1 day');
            $verification->user_id = $user->id;
            $verification->createdAt = new DateTime();
            $verification->updatedAt = new DateTime();

            $this->emailRepository->save($verification);

            // Simpan ke queue (verification_email)

            Database::commitTransaction();
            
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

        $verification = $this->emailRepository->findByToken($request->token);

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
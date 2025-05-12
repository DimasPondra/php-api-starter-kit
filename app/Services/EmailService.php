<?php

namespace Pondra\PhpApiStarterKit\Services;

use DateTime;
use Exception;
use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Helpers\EmailHelper;
use Pondra\PhpApiStarterKit\Models\Verification;
use Pondra\PhpApiStarterKit\Repositories\EmailRepository;
use Pondra\PhpApiStarterKit\Repositories\PersonalAccessTokenRepository;
use Pondra\PhpApiStarterKit\Repositories\UserRepository;
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

        $tokenVerification = Uuid::uuid4();
        $urlVerification = "http://localhost:8000/api/emails/$tokenVerification/verify";

        $bodyMail = "<h1>Halo $user->name,</h1><p>Terima kasih telah mendaftar! 
        Untuk menyelesaikan pendaftaran Anda, silakan klik tautan berikut ini 
        untuk memverifikasi alamat email Anda:</p><a href=$urlVerification>
        Verifikasi Alamat Email Saya</a><p>Jika Anda tidak mendaftar akun ini, 
        abaikan saja email ini.</p>Salam,<br>PHP Starter Kit";

        try {
            Database::beginTransaction();

            date_default_timezone_set("Asia/Jakarta");
            $dateTimeNow = new DateTime();

            $verification = new Verification();
            $verification->id = Uuid::uuid4();
            $verification->token = $tokenVerification;
            $verification->expiresAt = $dateTimeNow->modify('+1 day');
            $verification->user_id = $user->id;
            $verification->createdAt = new DateTime();
            $verification->updatedAt = new DateTime();

            $this->emailRepository->save($verification);

            $emailHelper = new EmailHelper();
            $responseSendEmail = $emailHelper->sendEmail($user->email, 'PHP Starter Kit', $bodyMail);

            if (!$responseSendEmail) {
                throw new Exception('Failed send email');
            }

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
}
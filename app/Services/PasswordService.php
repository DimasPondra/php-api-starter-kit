<?php

namespace Pondra\PhpApiStarterKit\Services;

use DateTime;
use Exception;
use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Exceptions\ValidationException;
use Pondra\PhpApiStarterKit\Helpers\DateTimeHelper;
use Pondra\PhpApiStarterKit\Helpers\EmailHelper;
use Pondra\PhpApiStarterKit\Models\PasswordResetToken;
use Pondra\PhpApiStarterKit\Repositories\PasswordRepository;
use Pondra\PhpApiStarterKit\Repositories\UserRepository;
use Pondra\PhpApiStarterKit\Requests\ForgotPasswordRequest;
use Pondra\PhpApiStarterKit\Requests\ResetPasswordRequest;
use Pondra\PhpApiStarterKit\Validations\ForgotPasswordValidation;
use Pondra\PhpApiStarterKit\Validations\ResetPasswordValidation;
use Ramsey\Uuid\Uuid;

class PasswordService
{
    private PasswordRepository $passwordRepository;
    private UserRepository $userRepository;

    public function __construct(PasswordRepository $passwordRepository)
    {
        $this->passwordRepository = $passwordRepository;

        $connection = Database::getConnection();
        $this->userRepository = new UserRepository($connection);
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        ForgotPasswordValidation::validation($request);

        $user = $this->userRepository->findByEmail($request->email);

        if ($user === null) {
            throw new ValidationException(null, 'Email is invalid.', 400);
        }

        $token = Uuid::uuid4()->toString();
        $hashToken = hash('sha256', $token);

        do {
            $token = Uuid::uuid4()->toString();
            $hashToken = hash('sha256', $token);
        } while ($this->passwordRepository->findByToken($hashToken));

        $urlResetPassword = "http://localhost:8000/api/password/$token/reset";

        $bodyMail = "<h1>Halo $user->name,</h1><p>Kami menerima permintaan untuk
        mereset password akun Anda. Silahkan klik tautan berikut ini untuk mengatur
        ulang password Anda:</p><a href=$urlResetPassword>Reset Password Saya</a>
        <p>Jika Anda tidak melakukan permintaan ini, abaikan saja email ini.</p>
        Salam,<br>PHP Starter Kit";

        try {
            Database::beginTransaction();

            $this->passwordRepository->deleteByEmail($user->email);

            $dateTimeNow = new DateTime();

            $prt = new PasswordResetToken();
            $prt->id = Uuid::uuid4();
            $prt->email = $user->email;
            $prt->token = $hashToken;
            $prt->expiresAt = $dateTimeNow->modify('+1 day');
            $prt->createdAt = new DateTime();

            $this->passwordRepository->save($prt);

            $emailHelper = new EmailHelper();
            $responseSendEmail = $emailHelper->sendEmail($user->email, 'PHP Starter Kit', $bodyMail);

            if (!$responseSendEmail) {
                throw new Exception('Failed send email');
            }

            Database::commitTransaction();

            return [
                'message' => 'Successfully send email reset password.',
                'data' => null
            ];
        } catch (\Throwable $th) {
            Database::rollbackTransaction();

            throw $th;
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        ResetPasswordValidation::validation($request);

        $hashToken = hash('sha256', $request->token);
        $prt = $this->passwordRepository->findByToken($hashToken);
        
        if ($prt === null) {
            throw new ValidationException(null, 'Token is invalid.', 400);
        }

        if ($prt->expiresAt < DateTimeHelper::nowLocal()) {
            throw new ValidationException(null, 'Token is expired.', 400);
        }

        $user = $this->userRepository->findByEmail($prt->email);

        try {
            Database::beginTransaction();

            $user->password = password_hash($request->password, PASSWORD_BCRYPT);
            $user->updatedAt = new DateTime();

            $this->userRepository->resetPassword($user);
            
            $this->passwordRepository->deleteByEmail($user->email);

            Database::commitTransaction();

            return [
                'message' => 'Successfully reset password.',
                'data' => null
            ];
        } catch (\Throwable $th) {
            Database::rollbackTransaction();

            throw $th;
        }
    }
}
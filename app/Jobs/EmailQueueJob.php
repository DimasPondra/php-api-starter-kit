<?php

namespace Pondra\PhpApiStarterKit\Jobs;

use DateTime;
use Dotenv\Dotenv;
use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Helpers\EmailHelper;
use Pondra\PhpApiStarterKit\Repositories\EmailQueueRepository;

class EmailQueueJob
{
    private EmailQueueRepository $emailQueueRepository;

    public function __construct()
    {
        $connection = Database::getConnection();
        $this->emailQueueRepository = new EmailQueueRepository($connection);
    }

    public function handle()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
        
        try {
            $emailQueue = $this->emailQueueRepository->getAllEmailQueue([
                'status' => ['pending','failed']
            ], 10);

            if (!empty($emailQueue)) {
                foreach ($emailQueue as $eQ) {
                    $name = $eQ->name;
                    $email = $eQ->email;
                    $type = $eQ->emailType;
                    $token = $eQ->token;

                    $url = null;
                    $body = null;

                    if ($type === 'verification_email') {
                        $url = $_ENV['URL_EMAIL_VERIFICATION'] . "?token=$token";
                        $body = $this->bodyVerificationEmail($name, $url);
                        
                    } else if ($type === 'reset_password') {
                        $url = $_ENV['URL_RESET_PASSWORD'] . "?token=$token";
                        $body = $this->bodyResetPassword($name, $url);
                    }

                    $emailHelper = new EmailHelper();
                    $responseSendEmail = $emailHelper->sendEmail($email, 'PHP Starter Kit', $body); // res true or false
                    
                    if (!$responseSendEmail) {
                        $eQ->status = 'failed';
                        $eQ->sentAt = null;
                    } else {
                        $eQ->status = 'sent';
                        $eQ->sentAt = new DateTime();
                    }

                    Database::beginTransaction();

                    $this->emailQueueRepository->update($eQ);

                    Database::commitTransaction();
                }
            }
        } catch (\Throwable $th) {
            Database::rollbackTransaction();

            var_dump($th->getMessage());
            die();
        }
    }

    private function bodyVerificationEmail($name, $url): string
    {
        $body = "<h1>Halo $name,</h1><p>Terima kasih telah mendaftar! 
        Untuk menyelesaikan pendaftaran Anda, silakan klik tautan berikut ini 
        untuk memverifikasi alamat email Anda:</p><a href=$url>
        Verifikasi Alamat Email Saya</a><p>Jika Anda tidak mendaftar akun ini, 
        abaikan saja email ini.</p>Salam,<br>PHP Starter Kit";

        return $body;
    }

    private function bodyResetPassword($name, $url): string
    {
        $body = "<h1>Halo $name,</h1><p>Kami menerima permintaan untuk
        mereset password akun Anda. Silahkan klik tautan berikut ini untuk mengatur
        ulang password Anda:</p><a href=$url>Reset Password Saya</a>
        <p>Jika Anda tidak melakukan permintaan ini, abaikan saja email ini.</p>
        Salam,<br>PHP Starter Kit";

        return $body;
    }
}
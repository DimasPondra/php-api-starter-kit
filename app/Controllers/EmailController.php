<?php

namespace Pondra\PhpApiStarterKit\Controllers;

use Pondra\PhpApiStarterKit\Helpers\AuthHelper;
use Pondra\PhpApiStarterKit\Helpers\ResponseHelper;
use Pondra\PhpApiStarterKit\Services\EmailService;

class EmailController
{
    private EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function sendVerification()
    {
        $token = AuthHelper::getToken();

        try {
            $response = $this->emailService->sendVerificationEmail($token);

            ResponseHelper::success($response['message'], $response['data']);
        } catch (\Throwable $th) {
            ResponseHelper::error('Something went wrong, Please try again.');
        }
    }

    public function verify(string $token)
    {

    }
}
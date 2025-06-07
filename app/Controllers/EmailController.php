<?php

namespace Pondra\PhpApiStarterKit\Controllers;

use Pondra\PhpApiStarterKit\Exceptions\ValidationException;
use Pondra\PhpApiStarterKit\Helpers\AuthHelper;
use Pondra\PhpApiStarterKit\Helpers\LoggerHelper;
use Pondra\PhpApiStarterKit\Helpers\ResponseHelper;
use Pondra\PhpApiStarterKit\Requests\VerifyEmailRequest;
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
        $hashToken = hash('sha256', $token);

        try {
            $response = $this->emailService->sendVerificationEmail($hashToken);

            ResponseHelper::success($response['message'], $response['data']);
        } catch (ValidationException $ve) {
            ResponseHelper::error(
                $ve->getMessage(), 
                $ve->getErrors(), 
                $ve->getCode(), 
                $ve->getStatusCode()
            );
        } catch (\Throwable $th) {
            LoggerHelper::emergency('Failed to send email verification.', [
                'error' => $th->getMessage()
            ]);

            ResponseHelper::error('Something went wrong, Please try again.');
        }
    }

    public function verify()
    {
        $inputJSON = file_get_contents('php://input');
        $data = json_decode($inputJSON, true);

        $request = new VerifyEmailRequest();
        $request->token = $data['token'] ?? null;

        try {
            $response = $this->emailService->verifyEmail($request);

            ResponseHelper::success($response['message'], $response['data']);
        } catch (ValidationException $ve) {
            ResponseHelper::error(
                $ve->getMessage(), 
                $ve->getErrors(), 
                $ve->getCode(), 
                $ve->getStatusCode()
            );
        } catch (\Throwable $th) {
            LoggerHelper::emergency('Failed to verify email.', [
                'error' => $th->getMessage()
            ]);

            ResponseHelper::error('Something went wrong, Please try again.');
        }
    }
}
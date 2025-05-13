<?php

namespace Pondra\PhpApiStarterKit\Controllers;

use Pondra\PhpApiStarterKit\Exceptions\ValidationException;
use Pondra\PhpApiStarterKit\Helpers\AuthHelper;
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

        try {
            $response = $this->emailService->sendVerificationEmail($token);

            ResponseHelper::success($response['message'], $response['data']);
        } catch (ValidationException $ve) {
            ResponseHelper::error(
                $ve->getMessage(), 
                $ve->getErrors(), 
                $ve->getCode(), 
                $ve->getStatusCode()
            );
        } catch (\Throwable $th) {
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
            ResponseHelper::error('Something went wrong, Please try again.'.$th->getMessage());
        }
    }
}
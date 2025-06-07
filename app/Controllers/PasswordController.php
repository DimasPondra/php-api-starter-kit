<?php

namespace Pondra\PhpApiStarterKit\Controllers;

use Pondra\PhpApiStarterKit\Exceptions\ValidationException;
use Pondra\PhpApiStarterKit\Helpers\LoggerHelper;
use Pondra\PhpApiStarterKit\Helpers\ResponseHelper;
use Pondra\PhpApiStarterKit\Requests\ForgotPasswordRequest;
use Pondra\PhpApiStarterKit\Requests\ResetPasswordRequest;
use Pondra\PhpApiStarterKit\Services\PasswordService;

class PasswordController
{
    private PasswordService $passwordService;

    public function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
    }

    public function forgot()
    {
        $inputJSON = file_get_contents('php://input');
        $data = json_decode($inputJSON, true);

        $request = new ForgotPasswordRequest();
        $request->email = $data['email'] ?? null;

        try {
            $response = $this->passwordService->forgotPassword($request);

            ResponseHelper::success($response['message'], $response['data']);
        } catch (ValidationException $ve) {
            ResponseHelper::error(
                $ve->getMessage(), 
                $ve->getErrors(), 
                $ve->getCode(), 
                $ve->getStatusCode()
            );
        } catch (\Throwable $th) {
            LoggerHelper::emergency('Failed to send email for forgot password.', [
                'error' => $th->getMessage()
            ]);

            ResponseHelper::error('Something went wrong, Please try again.');
        }
    }

    public function reset()
    {
        $inputJSON = file_get_contents('php://input');
        $data = json_decode($inputJSON, true);

        $request = new ResetPasswordRequest();
        $request->token = $data['token'] ?? null;
        $request->password = $data['password'] ?? null;

        try {
            $response = $this->passwordService->resetPassword($request);

            ResponseHelper::success($response['message'], $response['data']);
        } catch (ValidationException $ve) {
            ResponseHelper::error(
                $ve->getMessage(), 
                $ve->getErrors(), 
                $ve->getCode(), 
                $ve->getStatusCode()
            );
        } catch (\Throwable $th) {
            LoggerHelper::emergency('Failed to reset password.', [
                'error' => $th->getMessage()
            ]);

            ResponseHelper::error('Something went wrong, Please try again.');
        }
    }
}
<?php

namespace Pondra\PhpApiStarterKit\Services;

use Pondra\PhpApiStarterKit\Requests\RegisterRequest;

class UserService
{
    public function register(RegisterRequest $request)
    {
        $this->validateRegistrationRequest($request);
    }

    private function validateRegistrationRequest(RegisterRequest $request)
    {
        $errors = [];

        if ($request->name == null || trim($request->name) == null) {
            $errors['name'][] = 'name is required.';
        }

        if ($request->email == null || trim($request->email) == null) {
            $errors['email'][] = 'email is required.';
        }

        if ($request->password == null || trim($request->password) == null) {
            $errors['password'][] = 'password is required.';
        }

        if (!empty($errors)) {
            header("HTTP/1.1 422 Unprocessable Entity");
            header('Content-Type: application/json');
            
            echo json_encode([
                'status' => 'error',
                'message' => 'validation failed.',
                'errors' => $errors
            ]);
            exit();
        }
    }
}
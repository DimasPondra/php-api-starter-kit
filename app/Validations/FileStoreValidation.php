<?php

namespace Pondra\PhpApiStarterKit\Validations;

use Pondra\PhpApiStarterKit\Exceptions\ValidationException;
use Pondra\PhpApiStarterKit\Helpers\StringHelper;
use Pondra\PhpApiStarterKit\Requests\FileStoreRequest;

class FileStoreValidation
{
    public static function validation(FileStoreRequest $request): bool
    {
        $allowedTypes = ['image/jpeg', 'image/png'];
        $maxSize = 2 * 1024 * 1024;

        $errors = [];

        if (count($request->files) === 0) {
            $errors['files'][] = 'files is required.';
        }
        
        foreach ($request->files as $key => $file) {
            $key += 1;

            if (empty($file['name'])) {
                $errors['files'][] = "file $key is empty.";
            } else {
                // check file type.
                if (!in_array($file['type'], $allowedTypes)) {
                    $errors['files'][] = "file $key type not allowed.";
                }
    
                // check file size.
                if ($file['size'] > $maxSize) {
                    $errors['files'][] = "file $key size to big.";
                }
            }
        }

        $request->directory = StringHelper::lower($request->directory);

        if ($request->directory == null) {
            $errors['name'][] = 'directory is required.';
        } else if (strlen($request->directory) > 255) {
            $errors['name'][] = 'directory name to long, max 255 characters.';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return true;
    }
}
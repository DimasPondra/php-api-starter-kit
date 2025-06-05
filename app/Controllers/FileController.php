<?php

namespace Pondra\PhpApiStarterKit\Controllers;

use Pondra\PhpApiStarterKit\Exceptions\ValidationException;
use Pondra\PhpApiStarterKit\Helpers\FileHelper;
use Pondra\PhpApiStarterKit\Helpers\ResponseHelper;
use Pondra\PhpApiStarterKit\Requests\FileStoreRequest;
use Pondra\PhpApiStarterKit\Services\FileService;

class FileController
{
    private FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function upload()
    {
        $files = FileHelper::reArrayFiles($_FILES['files']);

        $request = new FileStoreRequest();
        $request->files = $files;
        $request->directory = $_POST['directory'] ?? null;

        try {
            $response = $this->fileService->createFile($request);
            
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
}
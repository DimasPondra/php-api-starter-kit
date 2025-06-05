<?php

namespace Pondra\PhpApiStarterKit\Services;

use DateTime;
use Dotenv\Dotenv;
use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Helpers\FileHelper;
use Pondra\PhpApiStarterKit\Models\File;
use Pondra\PhpApiStarterKit\Repositories\FileRepository;
use Pondra\PhpApiStarterKit\Requests\FileStoreRequest;
use Pondra\PhpApiStarterKit\Validations\FileStoreValidation;
use Ramsey\Uuid\Uuid;

class FileService
{
    private FileRepository $fileRepository;

    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    public function createFile(FileStoreRequest $request)
    {
        FileStoreValidation::validation($request);

        $files = [];
        $res = [];

        try {
            Database::beginTransaction();

            foreach ($request->files as $f) {
                $extention = FileHelper::getExtentionFromFile($f['name']);
                $name = FileHelper::randomName() . '.' . $extention;

                $file = new File();
                $file->id = Uuid::uuid4()->toString();
                $file->name = $name;
                $file->location = $request->directory;
                $file->createdAt = new DateTime();
                
                $this->fileRepository->save($file);

                $res[] = [
                    'id' => $file->id,
                    'url' => FileHelper::appUrlFile() . $request->directory . '/' . $file->name
                ];

                $storagePath = FileHelper::getDirectoryRootProject() . '/storage/uploads/' . $request->directory;

                if (!is_dir($storagePath)) {
                    mkdir($storagePath, 0777, true);
                }

                $fullStoragePath = $storagePath . '/' . $name;

                $files[] = [
                    'tmp_name' => $f['tmp_name'],
                    'destination' => $fullStoragePath
                ];
            }
            
            Database::commitTransaction();
        } catch (\Throwable $th) {
            Database::rollbackTransaction();

            throw $th;
        } finally {
            foreach ($files as $file) {
                move_uploaded_file($file['tmp_name'], $file['destination']);
            }
        }

        return [
            'message' => 'File successfully uploaded.',
            'data' => [
                'files' => $res
            ]
        ];
    }
}
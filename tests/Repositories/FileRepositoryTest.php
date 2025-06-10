<?php

namespace Pondra\PhpApiStarterKit\Repositories;

use DateTime;
use PHPUnit\Framework\TestCase;
use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Models\File;

class FileRepositoryTest extends TestCase
{
    private FileRepository $fileRepository;

    protected function setUp(): void
    {
        $this->fileRepository = new FileRepository(Database::getConnection());

        $this->fileRepository->deleteAll();
    }

    public function testCreateFileSuccess()
    {
        $file = new File();
        $file->id = '123';
        $file->name = 'user.jpg';
        $file->location = 'users';
        $file->createdAt = new DateTime();

        $result = $this->fileRepository->save($file);

        self::assertEquals($file->id, $result->id);
        self::assertEquals($file->name, $result->name);
    }

    public function testFindFileByIdSuccess()
    {
        $file = new File();
        $file->id = '123';
        $file->name = 'user.jpg';
        $file->location = 'users';
        $file->createdAt = new DateTime();

        $this->fileRepository->save($file);

        $result = $this->fileRepository->findById($file->id);

        self::assertEquals($file->id, $result->id);
        self::assertEquals($file->name, $result->name);
    }

    public function testDeleteFileByIdSuccess()
    {
        $id = '123';

        $file = new File();
        $file->id = $id;
        $file->name = 'user.jpg';
        $file->location = 'users';
        $file->createdAt = new DateTime();

        $this->fileRepository->save($file);

        $this->fileRepository->deleteById($id);

        $result = $this->fileRepository->findById($id);

        self::assertIsNotObject($result);
        self::assertNull($result);
    }
}
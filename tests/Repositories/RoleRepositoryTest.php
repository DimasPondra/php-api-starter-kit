<?php

namespace Pondra\PhpApiStarterKit\Repositories;

use DateTime;
use PHPUnit\Framework\TestCase;
use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Helpers\StringHelper;
use Pondra\PhpApiStarterKit\Models\Role;
use Ramsey\Uuid\Uuid;

class RoleRepositoryTest extends TestCase
{
    private RoleRepository $roleRepository;

    protected function setUp(): void
    {
        $this->roleRepository = new RoleRepository(Database::getConnection());

        $this->roleRepository->deleteAll();
    }

    public function testCreateRoleSuccess()
    {
        $role = new Role();
        $role->id = Uuid::uuid4()->toString();
        $role->name = 'Customer';
        $role->slug = StringHelper::slug('Customer');
        $role->createdAt = new DateTime();
        $role->updatedAt = new DateTime();

        $this->roleRepository->save($role);

        $result = $this->roleRepository->findByName('Customer');

        self::assertEquals($role->id, $result->id);
        self::assertEquals($role->name, $result->name);
        self::assertEquals($role->slug, $result->slug);
    }

    public function testFindRoleByIdSuccess()
    {
        $role = new Role();
        $role->id = Uuid::uuid4()->toString();
        $role->name = 'Customer';
        $role->slug = StringHelper::slug('Customer');
        $role->createdAt = new DateTime();
        $role->updatedAt = new DateTime();

        $this->roleRepository->save($role);

        $result = $this->roleRepository->findById($role->id);

        self::assertIsObject($result);
        self::assertObjectHasAttribute('id', $result);
        self::assertObjectHasAttribute('name', $result);
        self::assertObjectHasAttribute('slug', $result);
        self::assertEquals($role->id, $result->id);
        self::assertEquals($role->name, $result->name);
        self::assertEquals($role->slug, $result->slug);
    }

    public function testUpdateRoleSuccess()
    {
        $role = new Role();
        $role->id = Uuid::uuid4()->toString();
        $role->name = 'Customer';
        $role->slug = StringHelper::slug('Customer');
        $role->createdAt = new DateTime();
        $role->updatedAt = new DateTime();

        $this->roleRepository->save($role);

        $getRole = $this->roleRepository->findById($role->id);

        $getRole->name = 'Admin';
        $getRole->slug = StringHelper::slug('Admin');
        $getRole->updatedAt = new DateTime();

        $result = $this->roleRepository->update($getRole);

        self::assertIsObject($result);
        self::assertObjectHasAttribute('id', $result);
        self::assertObjectHasAttribute('name', $result);
        self::assertObjectHasAttribute('slug', $result);

        self::assertNotEquals($role->name, $result->name);
        self::assertNotEquals($role->slug, $result->slug);

        self::assertEquals($getRole->id, $result->id);
        self::assertEquals($getRole->name, $result->name);
        self::assertEquals($getRole->slug, $result->slug);
    }

    public function testDeleteRoleByIdSuccess()
    {
        $id = Uuid::uuid4()->toString();

        $role = new Role();
        $role->id = $id;
        $role->name = 'Customer';
        $role->slug = StringHelper::slug('Customer');
        $role->createdAt = new DateTime();
        $role->updatedAt = new DateTime();

        $this->roleRepository->save($role);
        
        $this->roleRepository->deleteById($id);

        $result = $this->roleRepository->findById($id);

        self::assertIsNotObject($result);
        self::assertNull($result);
    }
}
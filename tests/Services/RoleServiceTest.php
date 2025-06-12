<?php

namespace Pondra\PhpApiStarterKit\Services;

use DateTime;
use PHPUnit\Framework\TestCase;
use Pondra\PhpApiStarterKit\Config\Database;
use Pondra\PhpApiStarterKit\Exceptions\ValidationException;
use Pondra\PhpApiStarterKit\Models\Role;
use Pondra\PhpApiStarterKit\Models\User;
use Pondra\PhpApiStarterKit\Repositories\RoleRepository;
use Pondra\PhpApiStarterKit\Repositories\UserRepository;
use Pondra\PhpApiStarterKit\Requests\RoleStoreRequest;
use Pondra\PhpApiStarterKit\Requests\RoleUpdateRequest;

class RoleServiceTest extends TestCase
{
    private RoleService $roleService;
    private RoleRepository $roleRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = new UserRepository(Database::getConnection());

        $this->roleRepository = new RoleRepository(Database::getConnection());
        $this->roleService = new RoleService($this->roleRepository);

        $this->userRepository->deleteAll();
        $this->roleRepository->deleteAll();
    }

    public function testGetRolesButIsEmpty()
    {
        $result = $this->roleService->getRoles();

        self::assertIsArray($result);
        self::assertArrayHasKey('message', $result);
        self::assertArrayHasKey('data', $result);

        self::assertEquals($result['message'], 'No role data has been created yet.');
        self::assertEquals($result['data'], null);
    }

    public function testGetRolesSuccess()
    {
        $role = new Role();
        $role->id  = '1';
        $role->name = 'Customer';
        $role->slug = 'customer';
        $role->createdAt = new DateTime();
        $role->updatedAt = new DateTime();

        $this->roleRepository->save($role);

        $result = $this->roleService->getRoles();

        self::assertIsArray($result);
        self::assertArrayHasKey('message', $result);
        self::assertArrayHasKey('data', $result);

        self::assertEquals($result['message'], 'All roles retrieved successfully.');
        self::assertIsArray($result['data']);
    }

    public function testGetRoleByIdButNotFound()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('Role not found.');

        $result = $this->roleService->getRole(1);
    }

    public function testGetRoleSuccess()
    {
        $role = new Role();
        $role->id  = '1';
        $role->name = 'Customer';
        $role->slug = 'customer';
        $role->createdAt = new DateTime();
        $role->updatedAt = new DateTime();

        $this->roleRepository->save($role);

        $result = $this->roleService->getRole(1);

        self::assertIsArray($result);
        self::assertArrayHasKey('message', $result);
        self::assertArrayHasKey('data', $result);

        self::assertEquals($result['message'], 'Role retrieved successfully.');
        self::assertIsObject($result['data']);
        self::assertEquals('Customer', $result['data']->name);
    }

    public function testValidationCreateRoleIfDataIsInvalid()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionCode(422);
        $this->expectExceptionMessage('Validation failed.');

        $request = new RoleStoreRequest();
        $request->name = '';

        $this->roleService->createRole($request);
    }

    public function testCreateRoleSuccess()
    {
        $request = new RoleStoreRequest();
        $request->name = 'Customer';

        $result = $this->roleService->createRole($request);

        self::assertIsArray($result);
        self::assertArrayHasKey('message', $result);
        self::assertArrayHasKey('data', $result);

        self::assertEquals($result['message'], 'Role successfully created.');
        self::assertIsArray($result['data']);
        self::assertEquals($result['data']['name'], $request->name);
    }

    public function testUpdateRoleByIdButDataNotFound()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('Role not found.');

        $request = new RoleUpdateRequest();

        $this->roleService->updateRole($request, 1);
    }

    public function testValidationUpdateRoleIfDataIsInvalid()
    {
        $role = new Role();
        $role->id  = '1';
        $role->name = 'Customer';
        $role->slug = 'customer';
        $role->createdAt = new DateTime();
        $role->updatedAt = new DateTime();

        $this->roleRepository->save($role);

        $this->expectException(ValidationException::class);
        $this->expectExceptionCode(422);
        $this->expectExceptionMessage('Validation failed.');

        $request = new RoleUpdateRequest();
        $request->name = '';

        $this->roleService->updateRole($request, 1);
    }

    public function testUpdateRoleSuccess()
    {
        $role = new Role();
        $role->id  = '1';
        $role->name = 'Customer';
        $role->slug = 'customer';
        $role->createdAt = new DateTime();
        $role->updatedAt = new DateTime();

        $this->roleRepository->save($role);

        $request = new RoleUpdateRequest();
        $request->name = 'Customer Update';

        $result = $this->roleService->updateRole($request, 1);

        self::assertIsArray($result);
        self::assertArrayHasKey('message', $result);
        self::assertArrayHasKey('data', $result);

        self::assertEquals($result['message'], 'Role successfully updated.');
        self::assertIsArray($result['data']);
        self::assertEquals($result['data']['name'], $request->name);
    }

    public function testDeleteRoleByIdButDataNotFound()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('Role not found.');

        $this->roleService->deleteRole(1);
    }

    public function testDeleteRoleByIdButDataHasRelation()
    {
        $role = new Role();
        $role->id  = '1';
        $role->name = 'Customer';
        $role->slug = 'customer';
        $role->createdAt = new DateTime();
        $role->updatedAt = new DateTime();

        $this->roleRepository->save($role);

        $user = new User();
        $user->id = '1';
        $user->name = 'pondra';
        $user->email = 'pondra@mail.com';
        $user->password = 'secret';
        $user->role_id = '1';
        $user->createdAt = new DateTime();
        $user->updatedAt = new DateTime();

        $this->userRepository->save($user);

        $this->expectException(ValidationException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('Cannot delete. This record has existing relationships.');

        $this->roleService->deleteRole(1);
    }

    public function testDeleteRoleByIdSuccess()
    {
        $role = new Role();
        $role->id  = '1';
        $role->name = 'Customer';
        $role->slug = 'customer';
        $role->createdAt = new DateTime();
        $role->updatedAt = new DateTime();

        $this->roleRepository->save($role);
        
        $result = $this->roleService->deleteRole(1);

        self::assertIsArray($result);
        self::assertArrayHasKey('message', $result);
        self::assertArrayHasKey('data', $result);

        self::assertEquals($result['message'], 'Role successfully deleted.');
        self::assertNull($result['data']);

    }
}
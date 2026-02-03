<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use UserRepository;
use User;

class UserRepositoryTest extends TestCase
{
    private UserRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new UserRepository();
    }

    /**
     * Helper method: Create and persist test user with unique email
     */
    private function createTestUser(string $suffix, string $firstName, string $lastName, string $role = 'user'): User
    {
        $testEmail = 'phpunit.' . $suffix . '.' . time() . '@example.com';
        $user = new User($testEmail, password_hash('Test123', PASSWORD_BCRYPT), $firstName, $lastName, $role);
        $this->repository->addUser($user);
        return $this->repository->getUserByEmail($testEmail);
    }

    /**
     * Test that getUserByEmail returns null for non-existent user
     */
    public function testGetUserByEmailReturnsNullForNonExistentUser(): void
    {
        $user = $this->repository->getUserByEmail('nonexistent@example.com');
        $this->assertNull($user);
    }

    /**
     * Test that getUserById returns null for non-existent ID
     */
    public function testGetUserByIdReturnsNullForNonExistentId(): void
    {
        $user = $this->repository->getUserById(999999);
        $this->assertNull($user);
    }

    /**
     * Test that getUsers returns an array
     */
    public function testGetUsersReturnsArray(): void
    {
        $users = $this->repository->getUsers();
        $this->assertIsArray($users);
    }

    /**
     * Test User model constructor and getters
     */
    public function testUserModelHasRequiredProperties(): void
    {
        $user = new User(
            'test@example.com',
            'password123',
            'John',
            'Doe',
            'user',
            1
        );

        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertEquals('password123', $user->getPassword());
        $this->assertEquals('John', $user->getFirstName());
        $this->assertEquals('Doe', $user->getLastName());
        $this->assertEquals('user', $user->getRole());
        $this->assertEquals(1, $user->getId());
    }

    /**
     * Test addUser creates user with transaction
     */
    public function testAddUserCreatesUserSuccessfully(): void
    {
        $createdUser = $this->createTestUser('test', 'PHPUnit', 'Test');
        
        $this->assertNotNull($createdUser);
        $this->assertStringContainsString('phpunit.test.', $createdUser->getEmail());
        $this->assertEquals('PHPUnit', $createdUser->getFirstName());
        $this->assertEquals('Test', $createdUser->getLastName());
        
        $this->repository->deleteUser($createdUser->getId());
    }

    /**
     * Test updateUser modifies user data with transaction
     */
    public function testUpdateUserModifiesUserSuccessfully(): void
    {
        $createdUser = $this->createTestUser('update', 'Original', 'Name');

        $result = $this->repository->updateUser($createdUser->getId(), [
            'firstname' => 'Updated',
            'lastname' => 'User',
            'role' => 'organizer'
        ]);

        $this->assertTrue($result);

        $updatedUser = $this->repository->getUserById($createdUser->getId());
        $this->assertEquals('Updated', $updatedUser->getFirstName());
        $this->assertEquals('User', $updatedUser->getLastName());
        $this->assertEquals('organizer', $updatedUser->getRole());

        $this->repository->deleteUser($createdUser->getId());
    }

    /**
     * Test deleteUser removes user from database
     */
    public function testDeleteUserRemovesUserSuccessfully(): void
    {
        $createdUser = $this->createTestUser('delete', 'Delete', 'Me');

        $result = $this->repository->deleteUser($createdUser->getId());
        $this->assertTrue($result);

        $deletedUser = $this->repository->getUserById($createdUser->getId());
        $this->assertNull($deletedUser);
    }

    /**
     * Test that getUserByEmail returns correct User object
     */
    public function testGetUserByEmailReturnsCorrectUser(): void
    {
        $createdUser = $this->createTestUser('get', 'Get', 'Test');
        
        $this->assertInstanceOf(User::class, $createdUser);
        $this->assertStringContainsString('phpunit.get.', $createdUser->getEmail());
        $this->assertEquals('Get', $createdUser->getFirstName());
        $this->assertEquals('Test', $createdUser->getLastName());
        $this->assertEquals('user', $createdUser->getRole());

        $this->repository->deleteUser($createdUser->getId());
    }

    /**
     * Test that getUserById returns correct User object
     */
    public function testGetUserByIdReturnsCorrectUser(): void
    {
        $createdUser = $this->createTestUser('getid', 'GetById', 'Test');

        $foundUser = $this->repository->getUserById($createdUser->getId());
        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals($createdUser->getId(), $foundUser->getId());
        $this->assertStringContainsString('phpunit.getid.', $foundUser->getEmail());
        $this->assertEquals('GetById', $foundUser->getFirstName());

        $this->repository->deleteUser($createdUser->getId());
    }
}
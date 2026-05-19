<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../models/Auth.php';
require_once __DIR__ . '/../../models/Register.php';
require_once __DIR__ . '/../../config/Database.php';

class RegisterAuthTest extends TestCase
{
    private Database $db;
    private string $marker = '__TEST__REGISTER_AUTH__';

    /**
     * Prepares a real database connection and removes stale marked test data.
     */
    protected function setUp(): void
    {
        $this->assertTestEnvironment();
        $this->db = new Database();
        $this->cleanupTestUsers();
    }

    /**
     * Removes marked test users after each integration test.
     */
    protected function tearDown(): void
    {
        $this->assertTestEnvironment();
        $this->cleanupTestUsers();
    }

    /**
     * Tests that registration writes a marked user to the database.
     */
    public function testRegistrationWritesUserToDatabase()
    {
        // Register through the service and verify the user row exists in the test database.
        $email = $this->testEmail('registered');

        $result = RegisterService::register([
            'name' => $this->marker . 'registered',
            'email' => $email,
            'password' => 'Password1',
            'confirm' => 'Password1',
        ], $this->db);

        $this->assertTrue($result['success']);
        $this->assertSame($this->marker . 'registered', $result['user']['username']);
        $this->assertSame('user', $result['user']['role']);
        $this->assertSame('active', $result['user']['status']);

        $user = Auth::findUserByEmail($email, $this->db);

        $this->assertIsArray($user);
        $this->assertSame($this->marker . 'registered', $user['username']);
        $this->assertSame($email, $user['email']);
        $this->assertSame('user', $user['role']);
        $this->assertSame('active', $user['status']);
        $this->assertTrue(password_verify('Password1', $user['password']));
    }

    /**
     * Tests that an active user can log in through the real database.
     */
    public function testLoginWorksForActiveUser()
    {
        // Create an active marked user directly, then authenticate through the service.
        $email = $this->testEmail('active');
        $this->createUser($this->marker . 'active', $email, 'Secret123', 'active');

        $result = AuthService::login([
            'email' => $email,
            'password' => 'Secret123',
        ], $this->db);

        $this->assertTrue($result['success']);
        $this->assertSame($this->marker . 'active', $result['user']['username']);
        $this->assertSame($email, $result['user']['email']);
        $this->assertSame('active', $result['user']['status']);
    }

    /**
     * Tests that a blocked user cannot log in through the real database.
     */
    public function testBlockedUserCannotLogin()
    {
        // Blocked users should be rejected after password verification.
        $email = $this->testEmail('blocked');
        $this->createUser($this->marker . 'blocked', $email, 'Secret123', 'blocked');

        $result = AuthService::login([
            'email' => $email,
            'password' => 'Secret123',
        ], $this->db);

        $this->assertFalse($result['success']);
        $this->assertTrue($result['blocked']);
        $this->assertSame(['Your account is blocked. Please contact the administrator.'], $result['errors']);
    }

    /**
     * Creates a marked user directly in the test database.
     */
    private function createUser(string $username, string $email, string $password, string $status): int
    {
        $this->db->executeRun(
            "INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `status`, `created_at`) VALUES (NULL, ?, ?, ?, 'user', ?, ?)",
            [$username, $email, password_hash($password, PASSWORD_DEFAULT), $status, date('Y-m-d')]
        );

        return (int)$this->db->getLastInsertId();
    }

    /**
     * Stops the test before any write if the test database is not active.
     */
    private function assertTestEnvironment(): void
    {
        $dbName = $_ENV['DB_NAME'] ?? '';

        if (($_SERVER['APP_ENV'] ?? '') !== 'test' || $dbName !== 'art_portal_test') {
            $this->fail('Integration tests must run only with APP_ENV=test and DB_NAME=art_portal_test.');
        }
    }

    /**
     * Builds a unique email address for a marked test user.
     */
    private function testEmail(string $suffix): string
    {
        return strtolower(str_replace('_', '-', trim($this->marker, '_'))) . '-' . $suffix . '@example.test';
    }

    /**
     * Deletes only users created by this integration test.
     */
    private function cleanupTestUsers(): void
    {
        $this->db->executeRun(
            "DELETE FROM `users` WHERE `username` LIKE ? OR `email` LIKE ?",
            [$this->marker . '%', strtolower(str_replace('_', '-', trim($this->marker, '_'))) . '%@example.test']
        );
    }
}

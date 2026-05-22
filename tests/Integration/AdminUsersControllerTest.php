<?php
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../admin/controllers/Admin/UsersController.php';
require_once __DIR__ . '/../../models/Auth.php';
require_once __DIR__ . '/../../config/Database.php';

class AdminUsersControllerTest extends TestCase
{
    private Database $db;
    private string $marker = '__TEST__ADMIN_USERS_CONTROLLER__';
    private string $originalCwd;

    /**
     * Prepares a real database connection and remembers the project working directory.
     */
    protected function setUp(): void
    {
        $this->assertTestEnvironment();
        $this->originalCwd = getcwd();
        $this->db = new Database();
        $this->cleanupTestData();
    }

    /**
     * Removes marked users and restores the project working directory.
     */
    protected function tearDown(): void
    {
        $this->cleanupTestData();

        if (!empty($this->originalCwd)) {
            chdir($this->originalCwd);
        }
    }

    /**
     * Tests that the admin users page renders filtered users from the test database.
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testIndexRendersFilteredMarkedUser()
    {
        // Admin views use paths relative to the admin front controller directory.
        chdir(__DIR__ . '/../../admin');

        $userId = $this->createUser();

        $_SESSION = [
            'userId' => 1,
            'name' => 'Test Admin',
            'status' => 'admin',
        ];
        $_GET = [
            'q' => $this->marker,
        ];

        ob_start();
        try {
            UsersController::index();
            $output = ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        $this->assertStringContainsString('<html', $output);
        $this->assertStringContainsString('Users', $output);
        $this->assertStringContainsString('value="' . $this->marker . '"', $output);
        $this->assertStringContainsString($this->marker . 'user', $output);
        $this->assertStringContainsString($this->testEmail(), $output);
        $this->assertStringContainsString('Block', $output);
        $this->assertStringContainsString('value="' . $userId . '"', $output);
    }

    /**
     * Creates a marked user directly in the test database.
     */
    private function createUser(): int
    {
        $this->db->executeRun(
            "INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `status`, `created_at`) VALUES (NULL, ?, ?, ?, 'user', 'active', ?)",
            [$this->marker . 'user', $this->testEmail(), password_hash('Password1', PASSWORD_DEFAULT), date('Y-m-d')]
        );

        return (int)$this->db->getLastInsertId();
    }

    /**
     * Stops the test if the test database is not active.
     */
    private function assertTestEnvironment(): void
    {
        $dbName = $_ENV['DB_NAME'] ?? '';

        if (($_SERVER['APP_ENV'] ?? '') !== 'test' || $dbName !== 'art_portal_test') {
            $this->fail('Integration tests must run only with APP_ENV=test and DB_NAME=art_portal_test.');
        }
    }

    /**
     * Builds a unique email address for the marked user.
     */
    private function testEmail(): string
    {
        return strtolower(str_replace('_', '-', trim($this->marker, '_'))) . '@example.test';
    }

    /**
     * Deletes only users created by this integration test.
     */
    private function cleanupTestData(): void
    {
        $this->db->executeRun(
            "DELETE FROM `users` WHERE `username` LIKE ? OR `email` = ?",
            [$this->marker . '%', $this->testEmail()]
        );
    }
}

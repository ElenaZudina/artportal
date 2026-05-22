<?php
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../admin/controllers/Admin/CategoryController.php';
require_once __DIR__ . '/../../models/Categories.php';
require_once __DIR__ . '/../../config/Database.php';

class AdminCategoryControllerTest extends TestCase
{
    private Database $db;
    private string $marker = '__TEST__ADMIN_CATEGORY_CONTROLLER__';
    private string $originalCwd;

    /**
     * Prepares a real database connection and admin working directory.
     */
    protected function setUp(): void
    {
        $this->assertTestEnvironment();
        $this->originalCwd = getcwd();
        $this->db = new Database();
        $this->cleanupTestData();
    }

    /**
     * Removes marked category rows and restores the original working directory.
     */
    protected function tearDown(): void
    {
        $this->assertTestEnvironment();
        $this->cleanupTestData();

        if (!empty($this->originalCwd)) {
            chdir($this->originalCwd);
        }
    }

    /**
     * Tests that the admin category list renders categories from the test database.
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testCategoryListRendersMarkedCategory()
    {
        // Admin views use paths relative to the admin front controller directory.
        chdir(__DIR__ . '/../../admin');

        $categoryName = $this->marker . ' Category';
        $categoryId = $this->createCategory($categoryName);

        $_SESSION = [
            'userId' => 1,
            'name' => 'Test Admin',
            'status' => 'admin',
        ];

        ob_start();
        try {
            CategoryController::categoryList();
            $output = ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        $this->assertStringContainsString('<html', $output);
        $this->assertStringContainsString('<title>Dashboard Test Admin</title>', $output);
        $this->assertStringContainsString('Categories List', $output);
        $this->assertStringContainsString('Add category', $output);
        $this->assertStringContainsString($categoryName, $output);
        $this->assertStringContainsString('edit-category?id=' . $categoryId, $output);
        $this->assertStringContainsString('delete-category?id=' . $categoryId, $output);
    }

    /**
     * Creates a marked category directly in the test database.
     */
    private function createCategory(string $categoryName): int
    {
        $this->db->executeRun(
            "INSERT INTO `categories` (`name`) VALUES (?)",
            [$categoryName]
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
     * Deletes only category rows created by this integration test.
     */
    private function cleanupTestData(): void
    {
        $this->db->executeRun(
            "DELETE FROM `categories` WHERE `name` LIKE ?",
            [$this->marker . '%']
        );
    }
}

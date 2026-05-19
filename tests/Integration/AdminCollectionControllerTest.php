<?php
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../admin/controllers/Admin/CollectionController.php';
require_once __DIR__ . '/../../models/Collections.php';
require_once __DIR__ . '/../../config/Database.php';

class AdminCollectionControllerTest extends TestCase
{
    private Database $db;
    private string $marker = '__TEST__ADMIN_COLLECTION_CONTROLLER__';
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
     * Removes marked collection rows and restores the original working directory.
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
     * Tests that the admin collections list renders collections from the test database.
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testCollectionsListRendersMarkedCollection()
    {
        // Admin views use paths relative to the admin front controller directory.
        chdir(__DIR__ . '/../../admin');

        $collectionTitle = $this->marker . ' Collection';
        $collectionId = $this->createCollection($collectionTitle);

        $_SESSION = [
            'userId' => 1,
            'name' => 'Test Admin',
            'status' => 'admin',
        ];

        ob_start();
        try {
            CollectionController::collectionsList();
            $output = ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        $this->assertStringContainsString('<html', $output);
        $this->assertStringContainsString('<title>Dashboard Test Admin</title>', $output);
        $this->assertStringContainsString('Collections List', $output);
        $this->assertStringContainsString('Add collection', $output);
        $this->assertStringContainsString($collectionTitle, $output);
        $this->assertStringContainsString('keyword', $output);
        $this->assertStringContainsString('blue', $output);
        $this->assertStringContainsString('edit-collection?id=' . $collectionId, $output);
        $this->assertStringContainsString('delete-collection?id=' . $collectionId, $output);
    }

    /**
     * Creates a marked collection directly in the test database.
     */
    private function createCollection(string $collectionTitle): int
    {
        $this->db->executeRun(
            "INSERT INTO `collections` (`title`, `type`, `param`) VALUES (?, 'keyword', 'blue')",
            [$collectionTitle]
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
     * Deletes only collection rows created by this integration test.
     */
    private function cleanupTestData(): void
    {
        $this->db->executeRun(
            "DELETE FROM `exhibitions` WHERE `collection_id` IN (SELECT `id` FROM `collections` WHERE `title` LIKE ?)",
            [$this->marker . '%']
        );

        $this->db->executeRun(
            "DELETE FROM `collections` WHERE `title` LIKE ?",
            [$this->marker . '%']
        );
    }
}

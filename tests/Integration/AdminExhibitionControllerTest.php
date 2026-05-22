<?php
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../admin/controllers/Admin/ExhibitionController.php';
require_once __DIR__ . '/../../models/Collections.php';
require_once __DIR__ . '/../../models/Exhibitions.php';
require_once __DIR__ . '/../../config/Database.php';

class AdminExhibitionControllerTest extends TestCase
{
    private Database $db;
    private string $marker = '__TEST__ADMIN_EXHIBITION_CONTROLLER__';
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
     * Removes marked exhibitions and restores the project working directory.
     */
    protected function tearDown(): void
    {
        $this->cleanupTestData();

        if (!empty($this->originalCwd)) {
            chdir($this->originalCwd);
        }
    }

    /**
     * Tests that the admin exhibitions list renders exhibitions from the test database.
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testExhibitionsListRendersMarkedExhibition()
    {
        // Admin views use paths relative to the admin front controller directory.
        chdir(__DIR__ . '/../../admin');

        $ids = $this->createExhibition();
        $_SESSION = [
            'userId' => 1,
            'name' => 'Test Admin',
            'status' => 'admin',
        ];

        ob_start();
        try {
            ExhibitionController::exhibitionsList();
            $output = ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        $this->assertStringContainsString('<html', $output);
        $this->assertStringContainsString('Exhibitions List', $output);
        $this->assertStringContainsString('Add exhibition', $output);
        $this->assertStringContainsString($this->marker . ' Exhibition', $output);
        $this->assertStringContainsString($this->marker . ' Collection', $output);
        $this->assertStringContainsString('edit-exhibition?id=' . $ids['exhibition_id'], $output);
        $this->assertStringContainsString('delete-exhibition?id=' . $ids['exhibition_id'], $output);
    }

    /**
     * Creates a marked collection and exhibition directly in the test database.
     */
    private function createExhibition(): array
    {
        $this->db->executeRun(
            "INSERT INTO `collections` (`title`, `type`, `param`) VALUES (?, 'latest', '')",
            [$this->marker . ' Collection']
        );
        $collectionId = (int)$this->db->getLastInsertId();

        $this->db->executeRun(
            "INSERT INTO `exhibitions` (`title`, `description`, `collection_id`, `start_date`, `end_date`) VALUES (?, ?, ?, '2026-01-01', '2026-01-31')",
            [$this->marker . ' Exhibition', $this->marker . ' Description', $collectionId]
        );

        return [
            'collection_id' => $collectionId,
            'exhibition_id' => (int)$this->db->getLastInsertId(),
        ];
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
     * Deletes only exhibition rows created by this integration test.
     */
    private function cleanupTestData(): void
    {
        $this->db->executeRun(
            "DELETE FROM `exhibitions` WHERE `title` LIKE ? OR `collection_id` IN (SELECT `id` FROM `collections` WHERE `title` LIKE ?)",
            [$this->marker . '%', $this->marker . '%']
        );

        $this->db->executeRun(
            "DELETE FROM `collections` WHERE `title` LIKE ?",
            [$this->marker . '%']
        );
    }
}

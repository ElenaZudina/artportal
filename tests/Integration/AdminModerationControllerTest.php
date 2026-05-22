<?php
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../admin/controllers/Admin/ModerationController.php';
require_once __DIR__ . '/../../models/Artists.php';
require_once __DIR__ . '/../../models/Paintings.php';
require_once __DIR__ . '/../../config/Database.php';

class AdminModerationControllerTest extends TestCase
{
    private Database $db;
    private string $marker = '__TEST__ADMIN_MODERATION_CONTROLLER__';
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
     * Removes marked artists and restores the project working directory.
     */
    protected function tearDown(): void
    {
        $this->cleanupTestData();

        if (!empty($this->originalCwd)) {
            chdir($this->originalCwd);
        }
    }

    /**
     * Tests that the pending moderation list renders marked pending artists.
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testPendingListRendersMarkedArtist()
    {
        // Admin views use paths relative to the admin front controller directory.
        chdir(__DIR__ . '/../../admin');

        $artistId = $this->createPendingArtist();
        $_SESSION = $this->adminSession();

        ob_start();
        try {
            ModerationController::pendingList();
            $output = ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        $this->assertStringContainsString('<html', $output);
        $this->assertStringContainsString('Artist Requests', $output);
        $this->assertStringContainsString($this->marker . ' Artist', $output);
        $this->assertStringContainsString('approve-artist?id=' . $artistId, $output);
        $this->assertStringContainsString('moderation-artist?id=' . $artistId, $output);
    }

    /**
     * Tests that the moderation profile page renders the selected artist profile.
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testViewProfileRendersMarkedArtist()
    {
        // The detail view should load the artist and their optional portfolio.
        chdir(__DIR__ . '/../../admin');

        $artistId = $this->createPendingArtist();
        $_SESSION = $this->adminSession();

        ob_start();
        try {
            ModerationController::viewProfile($artistId);
            $output = ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        $this->assertStringContainsString('<html', $output);
        $this->assertStringContainsString($this->marker . ' Artist', $output);
        $this->assertStringContainsString('Tallinn', $output);
        $this->assertStringContainsString('Pending', $output);
        $this->assertStringContainsString('This artist does not have any paintings yet.', $output);
    }

    /**
     * Creates a marked pending artist directly in the test database.
     */
    private function createPendingArtist(): int
    {
        $this->db->executeRun(
            "INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `status`, `created_at`) VALUES (NULL, ?, ?, ?, 'user', 'active', ?)",
            [$this->marker . 'user', $this->testEmail(), password_hash('Password1', PASSWORD_DEFAULT), date('Y-m-d')]
        );
        $userId = (int)$this->db->getLastInsertId();

        $this->db->executeRun(
            "INSERT INTO `artists` (`name`, `location`, `birth_date`, `bio`, `picture`, `status`, `user_id`, `created_at`, `updated_at`) VALUES (?, 'Tallinn', '1990-01-01', ?, 'test-artist.jpg', 'pending', ?, NOW(), NOW())",
            [$this->marker . ' Artist', $this->marker . ' bio', $userId]
        );

        return (int)$this->db->getLastInsertId();
    }

    /**
     * Builds a fake admin session for rendering admin views.
     */
    private function adminSession(): array
    {
        return [
            'userId' => 1,
            'name' => 'Test Admin',
            'status' => 'admin',
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
     * Builds a unique email address for the marked user.
     */
    private function testEmail(): string
    {
        return strtolower(str_replace('_', '-', trim($this->marker, '_'))) . '@example.test';
    }

    /**
     * Deletes only moderation rows created by this integration test.
     */
    private function cleanupTestData(): void
    {
        $this->db->executeRun(
            "DELETE FROM `artists` WHERE `name` LIKE ? OR `bio` LIKE ? OR `user_id` IN (SELECT `id` FROM `users` WHERE `username` LIKE ? OR `email` = ?)",
            [$this->marker . '%', $this->marker . '%', $this->marker . '%', $this->testEmail()]
        );

        $this->db->executeRun(
            "DELETE FROM `users` WHERE `username` LIKE ? OR `email` = ?",
            [$this->marker . '%', $this->testEmail()]
        );
    }
}

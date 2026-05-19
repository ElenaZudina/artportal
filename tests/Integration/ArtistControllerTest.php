<?php
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../controllers/ArtistController.php';
require_once __DIR__ . '/../../helpers/MenuHelper.php';
require_once __DIR__ . '/../../helpers/PaginationHelper.php';
require_once __DIR__ . '/../../helpers/UIHelper.php';
require_once __DIR__ . '/../../models/Artists.php';
require_once __DIR__ . '/../../models/Categories.php';
require_once __DIR__ . '/../../models/Paintings.php';
require_once __DIR__ . '/../../config/Database.php';

class ArtistControllerTest extends TestCase
{
    private Database $db;
    private string $marker = '__TEST__ARTIST_CONTROLLER__';

    /**
     * Prepares a real database connection and removes stale marked test data.
     */
    protected function setUp(): void
    {
        $this->assertTestEnvironment();
        $this->db = new Database();
        $this->cleanupTestData();
    }

    /**
     * Removes marked artist and user rows after each integration test.
     */
    protected function tearDown(): void
    {
        $this->assertTestEnvironment();
        $this->cleanupTestData();
    }

    /**
     * Tests that the artists list controller renders searchable approved artists.
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testAllArtistsRendersSearchableApprovedArtist()
    {
        // The controller reads from the test database through the real model.
        $artistName = $this->marker . ' Visible Artist';
        $this->createApprovedArtist($artistName);

        $_SESSION = [];
        $_GET = [
            'search' => $this->marker,
        ];

        if (!defined('ACTIVE_ROUTE')) {
            define('ACTIVE_ROUTE', 'artists');
        }

        ob_start();
        try {
            ArtistController::AllArtists();
            $output = ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        $this->assertStringContainsString('<html', $output);
        $this->assertStringContainsString('<title>Art Portal</title>', $output);
        $this->assertStringContainsString('Meet our artists', $output);
        $this->assertStringContainsString('<form class="artist-search-form" method="get" action="artists">', $output);
        $this->assertStringContainsString('value="' . $this->marker . '"', $output);
        $this->assertStringContainsString($artistName, $output);
        $this->assertStringContainsString('Tartu', $output);
        $this->assertStringContainsString('View Profile', $output);
    }

    /**
     * Creates a marked approved artist directly in the test database.
     */
    private function createApprovedArtist(string $artistName): int
    {
        $this->db->executeRun(
            "INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `status`, `created_at`) VALUES (NULL, ?, ?, ?, 'artist', 'active', ?)",
            [$this->marker . 'user', $this->testEmail(), password_hash('Password1', PASSWORD_DEFAULT), date('Y-m-d')]
        );

        $userId = (int)$this->db->getLastInsertId();

        $this->db->executeRun(
            "INSERT INTO `artists` (`name`, `location`, `birth_date`, `bio`, `picture`, `status`, `user_id`, `created_at`, `updated_at`) VALUES (?, 'Tartu', '1990-01-01', ?, 'test-artist.jpg', 'approved', ?, NOW(), NOW())",
            [$artistName, $this->marker . ' public biography', $userId]
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
    private function testEmail(): string
    {
        return strtolower(str_replace('_', '-', trim($this->marker, '_'))) . '@example.test';
    }

    /**
     * Deletes only artist profiles and users created by this integration test.
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

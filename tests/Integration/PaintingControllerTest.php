<?php
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../controllers/PaintingController.php';
require_once __DIR__ . '/../../helpers/MenuHelper.php';
require_once __DIR__ . '/../../helpers/PaginationHelper.php';
require_once __DIR__ . '/../../helpers/UIHelper.php';
require_once __DIR__ . '/../../models/Artists.php';
require_once __DIR__ . '/../../models/Categories.php';
require_once __DIR__ . '/../../models/Favourite.php';
require_once __DIR__ . '/../../models/Paintings.php';
require_once __DIR__ . '/../../config/Database.php';

class PaintingControllerTest extends TestCase
{
    private Database $db;
    private string $marker = '__TEST__PAINTING_CONTROLLER__';

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
     * Removes marked painting, artist, category, and user rows after each integration test.
     */
    protected function tearDown(): void
    {
        $this->assertTestEnvironment();
        $this->cleanupTestData();
    }

    /**
     * Tests that the paintings list controller renders searchable public paintings.
     */
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testAllPaintingsRendersSearchableApprovedPainting()
    {
        // The controller reads related artist, category, and painting rows from the test database.
        $paintingTitle = $this->marker . ' Visible Painting';
        $artistName = $this->marker . ' Artist';
        $categoryName = $this->marker . ' Category';
        $this->createPublicPainting($paintingTitle, $artistName, $categoryName);

        $_SESSION = [];
        $_GET = [
            'search' => $this->marker,
        ];

        if (!defined('ACTIVE_ROUTE')) {
            define('ACTIVE_ROUTE', 'all');
        }

        ob_start();
        try {
            PaintingController::AllPaintings();
            $output = ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        $this->assertStringContainsString('<html', $output);
        $this->assertStringContainsString('<title>Art Portal</title>', $output);
        $this->assertStringContainsString('All paintings', $output);
        $this->assertStringContainsString('<form class="painting-search-form" method="get" action="all">', $output);
        $this->assertStringContainsString('value="' . $this->marker . '"', $output);
        $this->assertStringContainsString($paintingTitle, $output);
        $this->assertStringContainsString($artistName, $output);
        $this->assertStringContainsString($categoryName, $output);
        $this->assertStringContainsString('250.00', $output);
    }

    /**
     * Creates a marked approved artist, category, and public painting directly in the test database.
     */
    private function createPublicPainting(string $paintingTitle, string $artistName, string $categoryName): int
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
        $artistId = (int)$this->db->getLastInsertId();

        $this->db->executeRun(
            "INSERT INTO `categories` (`name`) VALUES (?)",
            [$categoryName]
        );
        $categoryId = (int)$this->db->getLastInsertId();

        $this->db->executeRun(
            "INSERT INTO `paintings` (`title`, `description`, `image`, `year_created`, `category_id`, `artist_id`, `medium`, `dimensions`, `price`, `created_at`, `updated_at`) VALUES (?, ?, 'test-painting.jpg', '2024', ?, ?, 'Oil', '40x50', '250.00', NOW(), NOW())",
            [$paintingTitle, $this->marker . ' public description', $categoryId, $artistId]
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
     * Deletes only painting-related rows created by this integration test.
     */
    private function cleanupTestData(): void
    {
        $this->db->executeRun(
            "DELETE FROM `purchase_requests` WHERE `painting_id` IN (SELECT `id` FROM `paintings` WHERE `title` LIKE ?)",
            [$this->marker . '%']
        );

        $this->db->executeRun(
            "DELETE FROM `favorites` WHERE `painting_id` IN (SELECT `id` FROM `paintings` WHERE `title` LIKE ?)",
            [$this->marker . '%']
        );

        $this->db->executeRun(
            "DELETE FROM `painting_tags` WHERE `painting_id` IN (SELECT `id` FROM `paintings` WHERE `title` LIKE ?)",
            [$this->marker . '%']
        );

        $this->db->executeRun(
            "DELETE FROM `paintings` WHERE `title` LIKE ? OR `description` LIKE ?",
            [$this->marker . '%', $this->marker . '%']
        );

        $this->db->executeRun(
            "DELETE FROM `artists` WHERE `name` LIKE ? OR `bio` LIKE ? OR `user_id` IN (SELECT `id` FROM `users` WHERE `username` LIKE ? OR `email` = ?)",
            [$this->marker . '%', $this->marker . '%', $this->marker . '%', $this->testEmail()]
        );

        $this->db->executeRun(
            "DELETE FROM `categories` WHERE `name` LIKE ?",
            [$this->marker . '%']
        );

        $this->db->executeRun(
            "DELETE FROM `users` WHERE `username` LIKE ? OR `email` = ?",
            [$this->marker . '%', $this->testEmail()]
        );
    }
}

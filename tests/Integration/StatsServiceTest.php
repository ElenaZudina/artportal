<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/StatsService.php';
require_once __DIR__ . '/../../models/Artists.php';
require_once __DIR__ . '/../../models/Auth.php';
require_once __DIR__ . '/../../models/Categories.php';
require_once __DIR__ . '/../../models/Collections.php';
require_once __DIR__ . '/../../models/Exhibitions.php';
require_once __DIR__ . '/../../models/Favourite.php';
require_once __DIR__ . '/../../models/Paintings.php';
require_once __DIR__ . '/../../models/PurchaseRequest.php';
require_once __DIR__ . '/../../config/Database.php';

class StatsServiceTest extends TestCase
{
    private Database $db;
    private string $marker = '__TEST__STATS_SERVICE__';

    /**
     * Prepares a real test database connection and removes stale marked rows.
     */
    protected function setUp(): void
    {
        $this->assertTestEnvironment();
        $this->db = new Database();
        $this->cleanupTestData();
    }

    /**
     * Removes marked rows after each integration test.
     */
    protected function tearDown(): void
    {
        $this->cleanupTestData();
    }

    /**
     * Tests that base dashboard counts include marked test data.
     */
    public function testGetCountsReflectsMarkedRows()
    {
        // Compare counts before and after creating marked dashboard entities.
        $before = StatsService::getCounts();

        $this->createCountRows();

        $after = StatsService::getCounts();

        $this->assertSame((int)$before['artists'] + 1, (int)$after['artists']);
        $this->assertSame((int)$before['users'] + 1, (int)$after['users']);
        $this->assertSame((int)$before['pending_profiles'] + 1, (int)$after['pending_profiles']);
        $this->assertSame((int)$before['collections'] + 1, (int)$after['collections']);
        $this->assertSame((int)$before['exhibitions'] + 1, (int)$after['exhibitions']);
        $this->assertSame((int)$before['categories'] + 1, (int)$after['categories']);
    }

    /**
     * Tests that artist dashboard statistics aggregate portfolio and request data.
     */
    public function testGetArtistDashboardStatsReturnsPortfolioRequestsAndFavorites()
    {
        // Artist dashboard stats should combine user, artist, paintings, requests, and favorites.
        $ids = $this->createDashboardRows();

        $stats = StatsService::getArtistDashboardStats($ids['artist_user_id']);

        $this->assertSame($this->marker . 'artist_user', $stats['user']['username']);
        $this->assertSame($this->marker . ' Artist', $stats['artist']['name']);
        $this->assertSame(1, $stats['requestsCount']);
        $this->assertSame(1, $stats['paintingsCount']);
        $this->assertSame(1, $stats['favoritesCount']);
        $this->assertSame(0, $stats['viewsTotal']);
        $this->assertCount(1, $stats['requests']);
        $this->assertCount(1, $stats['paintings']);
        $this->assertSame($this->marker . ' Painting', $stats['requests'][0]['painting_title']);
    }

    /**
     * Tests that regular user dashboard statistics aggregate favorites and requests.
     */
    public function testGetUserDashboardStatsReturnsFavoritesRequestsAndRecentPaintings()
    {
        // User dashboard stats should include favorite paintings and purchase requests.
        $ids = $this->createDashboardRows();

        $stats = StatsService::getUserDashboardStats($ids['buyer_id']);

        $this->assertSame($this->marker . 'buyer', $stats['user']['username']);
        $this->assertSame(1, $stats['favoritesCount']);
        $this->assertSame(1, $stats['userRequestsCount']);
        $this->assertCount(1, $stats['favorites']);
        $this->assertCount(1, $stats['userRequests']);
        $this->assertNotEmpty($stats['recentPaintings']);
        $this->assertSame($this->marker . ' Painting', $stats['favorites'][0]['title']);
        $this->assertSame($this->marker . ' Painting', $stats['userRequests'][0]['painting_title']);
    }

    /**
     * Creates marked rows needed for count checks.
     */
    private function createCountRows(): void
    {
        $artistUserId = $this->createUser($this->marker . 'count_artist', $this->testEmail('count-artist'), 'artist');
        $regularUserId = $this->createUser($this->marker . 'count_user', $this->testEmail('count-user'), 'user');

        $this->createArtist($artistUserId, $this->marker . ' Count Pending Artist', 'pending');
        $categoryId = $this->createCategory($this->marker . ' Count Category');
        $collectionId = $this->createCollection($this->marker . ' Count Collection');
        $this->createExhibition($collectionId, $this->marker . ' Count Exhibition');

        $this->assertGreaterThan(0, $regularUserId);
        $this->assertGreaterThan(0, $categoryId);
    }

    /**
     * Creates marked rows needed for artist and user dashboard checks.
     */
    private function createDashboardRows(): array
    {
        $buyerId = $this->createUser($this->marker . 'buyer', $this->testEmail('buyer'), 'user');
        $artistUserId = $this->createUser($this->marker . 'artist_user', $this->testEmail('artist'), 'artist');
        $artistId = $this->createArtist($artistUserId, $this->marker . ' Artist', 'approved');
        $categoryId = $this->createCategory($this->marker . ' Dashboard Category');
        $paintingId = $this->createPainting($artistId, $categoryId);

        $this->db->executeRun(
            "INSERT INTO `favorites` (`user_id`, `painting_id`) VALUES (?, ?)",
            [$buyerId, $paintingId]
        );

        $this->db->executeRun(
            "INSERT INTO `purchase_requests` (`user_id`, `painting_id`) VALUES (?, ?)",
            [$buyerId, $paintingId]
        );

        return [
            'buyer_id' => $buyerId,
            'artist_user_id' => $artistUserId,
            'artist_id' => $artistId,
            'painting_id' => $paintingId,
        ];
    }

    /**
     * Creates a marked user directly in the test database.
     */
    private function createUser(string $username, string $email, string $role): int
    {
        $this->db->executeRun(
            "INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `status`, `created_at`) VALUES (NULL, ?, ?, ?, ?, 'active', ?)",
            [$username, $email, password_hash('Password1', PASSWORD_DEFAULT), $role, date('Y-m-d')]
        );

        return (int)$this->db->getLastInsertId();
    }

    /**
     * Creates a marked artist profile directly in the test database.
     */
    private function createArtist(int $userId, string $name, string $status): int
    {
        $this->db->executeRun(
            "INSERT INTO `artists` (`name`, `location`, `birth_date`, `bio`, `picture`, `status`, `user_id`, `created_at`, `updated_at`) VALUES (?, 'Tallinn', '1990-01-01', ?, 'test-artist.jpg', ?, ?, NOW(), NOW())",
            [$name, $this->marker . ' bio', $status, $userId]
        );

        return (int)$this->db->getLastInsertId();
    }

    /**
     * Creates a marked category directly in the test database.
     */
    private function createCategory(string $name): int
    {
        $this->db->executeRun(
            "INSERT INTO `categories` (`name`) VALUES (?)",
            [$name]
        );

        return (int)$this->db->getLastInsertId();
    }

    /**
     * Creates a marked collection directly in the test database.
     */
    private function createCollection(string $title): int
    {
        $this->db->executeRun(
            "INSERT INTO `collections` (`title`, `type`, `param`) VALUES (?, 'latest', '')",
            [$title]
        );

        return (int)$this->db->getLastInsertId();
    }

    /**
     * Creates a marked exhibition directly in the test database.
     */
    private function createExhibition(int $collectionId, string $title): int
    {
        $this->db->executeRun(
            "INSERT INTO `exhibitions` (`title`, `description`, `collection_id`, `start_date`, `end_date`) VALUES (?, ?, ?, '2026-01-01', '2026-01-31')",
            [$title, $this->marker . ' description', $collectionId]
        );

        return (int)$this->db->getLastInsertId();
    }

    /**
     * Creates a marked painting directly in the test database.
     */
    private function createPainting(int $artistId, int $categoryId): int
    {
        $this->db->executeRun(
            "INSERT INTO `paintings` (`title`, `description`, `image`, `year_created`, `category_id`, `artist_id`, `medium`, `dimensions`, `price`, `created_at`, `updated_at`) VALUES (?, ?, 'test-painting.jpg', '2024', ?, ?, 'Oil', '40x50', '150.00', NOW(), NOW())",
            [$this->marker . ' Painting', $this->marker . ' painting description', $categoryId, $artistId]
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
     * Deletes only rows created by this integration test.
     */
    private function cleanupTestData(): void
    {
        $emailPattern = strtolower(str_replace('_', '-', trim($this->marker, '_'))) . '%@example.test';

        $this->db->executeRun(
            "DELETE FROM `purchase_requests` WHERE `painting_id` IN (SELECT `id` FROM `paintings` WHERE `title` LIKE ?) OR `user_id` IN (SELECT `id` FROM `users` WHERE `email` LIKE ?)",
            [$this->marker . '%', $emailPattern]
        );

        $this->db->executeRun(
            "DELETE FROM `favorites` WHERE `painting_id` IN (SELECT `id` FROM `paintings` WHERE `title` LIKE ?) OR `user_id` IN (SELECT `id` FROM `users` WHERE `email` LIKE ?)",
            [$this->marker . '%', $emailPattern]
        );

        $this->db->executeRun(
            "DELETE FROM `paintings` WHERE `title` LIKE ? OR `description` LIKE ?",
            [$this->marker . '%', $this->marker . '%']
        );

        $this->db->executeRun(
            "DELETE FROM `artists` WHERE `name` LIKE ? OR `bio` LIKE ? OR `user_id` IN (SELECT `id` FROM `users` WHERE `email` LIKE ?)",
            [$this->marker . '%', $this->marker . '%', $emailPattern]
        );

        $this->db->executeRun(
            "DELETE FROM `exhibitions` WHERE `title` LIKE ? OR `description` LIKE ? OR `collection_id` IN (SELECT `id` FROM `collections` WHERE `title` LIKE ?)",
            [$this->marker . '%', $this->marker . '%', $this->marker . '%']
        );

        $this->db->executeRun(
            "DELETE FROM `collections` WHERE `title` LIKE ?",
            [$this->marker . '%']
        );

        $this->db->executeRun(
            "DELETE FROM `categories` WHERE `name` LIKE ?",
            [$this->marker . '%']
        );

        $this->db->executeRun(
            "DELETE FROM `users` WHERE `username` LIKE ? OR `email` LIKE ?",
            [$this->marker . '%', $emailPattern]
        );
    }
}

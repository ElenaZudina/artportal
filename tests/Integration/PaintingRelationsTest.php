<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/ArtistProfileService.php';
require_once __DIR__ . '/../../services/CategoryService.php';
require_once __DIR__ . '/../../services/PaintingService.php';
require_once __DIR__ . '/../../models/Artists.php';
require_once __DIR__ . '/../../models/Categories.php';
require_once __DIR__ . '/../../models/Favourite.php';
require_once __DIR__ . '/../../models/Paintings.php';
require_once __DIR__ . '/../../models/PurchaseRequest.php';
require_once __DIR__ . '/../../config/Database.php';

class PaintingRelationsTest extends TestCase
{
    private Database $db;
    private string $marker = '__TEST__PAINTING_RELATIONS__';

    /**
     * Prepares a real test database connection and removes stale marked relation data.
     */
    protected function setUp(): void
    {
        $this->assertTestEnvironment();
        $this->db = new Database();
        $this->cleanupTestData();
    }

    /**
     * Removes marked relation rows after each integration test.
     */
    protected function tearDown(): void
    {
        $this->assertTestEnvironment();
        $this->cleanupTestData();
    }

    /**
     * Tests painting creation, favorites, and purchase request visibility.
     */
    public function testPaintingFavoriteAndPurchaseRequestRelations()
    {
        // Create the buyer, artist profile, category, and painting needed for relation checks.
        $buyerId = $this->createUser($this->marker . 'buyer', $this->testEmail('buyer'), 'user');
        $artistUserId = $this->createUser($this->marker . 'artist_user', $this->testEmail('artist'), 'artist');
        $artistId = $this->createArtistProfile($artistUserId);
        $categoryId = $this->createCategory();
        $paintingId = $this->createPainting($artistUserId, $categoryId);

        $painting = Paintings::getPaintingByID($paintingId, $this->db);
        $this->assertIsArray($painting);
        $this->assertSame($this->marker . ' Painting', $painting['title']);
        $this->assertSame($artistId, (int)$painting['artist_id']);
        $this->assertSame($categoryId, (int)$painting['category_id']);

        // Favorites should be addable, detectable, and removable for the buyer.
        $this->assertTrue((bool)Favorite::addToFavorite($buyerId, $paintingId, $this->db));
        $this->assertTrue(Favorite::isFavorite($buyerId, $paintingId, $this->db));
        $this->assertTrue((bool)Favorite::removeFromFavorite($buyerId, $paintingId, $this->db));
        $this->assertFalse(Favorite::isFavorite($buyerId, $paintingId, $this->db));

        // Purchase requests should be created and visible to both the buyer and the artist.
        $request = PurchaseRequest::create($buyerId, $paintingId, $this->db);
        $this->assertTrue($request['success']);
        $this->assertSame('Request sent successfully', $request['message']);

        $userRequests = PurchaseRequest::getUserRequests($buyerId, 10, 0, $this->db);
        $artistRequests = PurchaseRequest::getArtistRequests($artistId, 10, 0, $this->db);

        $this->assertCount(1, $userRequests);
        $this->assertCount(1, $artistRequests);
        $this->assertSame($this->marker . ' Painting', $userRequests[0]['painting_title']);
        $this->assertSame($this->marker . ' Artist', $userRequests[0]['artist_name']);
        $this->assertSame($this->marker . ' Painting', $artistRequests[0]['painting_title']);
        $this->assertSame($this->marker . 'buyer', $artistRequests[0]['user_name']);
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
     * Creates a marked artist profile through the service.
     */
    private function createArtistProfile(int $artistUserId): int
    {
        $result = ArtistProfileService::createProfile([
            'name' => $this->marker . ' Artist',
            'location' => 'Tallinn',
            'birth_date' => '1990-01-01',
            'bio' => 'Integration relation artist',
            'picture' => 'test-artist.jpg',
        ], [], $artistUserId, $this->db);

        $this->assertTrue($result['success']);

        $artist = Artists::getArtistByUserId($artistUserId, $this->db);
        $this->assertIsArray($artist);

        return (int)$artist['id'];
    }

    /**
     * Creates a marked category through the service.
     */
    private function createCategory(): int
    {
        $result = CategoryService::createCategory([
            'name' => $this->marker . ' Category',
        ], $this->db);

        $this->assertTrue($result['success']);

        $category = $this->db->getOne(
            'SELECT * FROM categories WHERE name = ? LIMIT 1',
            [$this->marker . ' Category']
        );

        $this->assertIsArray($category);

        return (int)$category['id'];
    }

    /**
     * Creates a marked painting through the service with fake AI labels.
     */
    private function createPainting(int $artistUserId, int $categoryId): int
    {
        $visionService = new class {
            public function detectLabels(string $imagePath): array
            {
                return [];
            }

            public function buildTags(array $response): array
            {
                return [];
            }
        };

        $result = PaintingService::createPainting([
            'title' => $this->marker . ' Painting',
            'description' => 'Integration relation painting',
            'image' => 'test-painting.jpg',
            'year_created' => '2024',
            'category_id' => $categoryId,
            'medium' => 'Oil',
            'dimensions' => '40x50',
            'price' => '150.00',
        ], [], $artistUserId, $this->db, $visionService);

        $this->assertTrue($result['success']);

        $painting = $this->db->getOne(
            'SELECT * FROM paintings WHERE title = ? LIMIT 1',
            [$this->marker . ' Painting']
        );

        $this->assertIsArray($painting);

        return (int)$painting['id'];
    }

    /**
     * Builds a unique email address for a marked test user.
     */
    private function testEmail(string $suffix): string
    {
        return strtolower(str_replace('_', '-', trim($this->marker, '_'))) . '-' . $suffix . '@example.test';
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
     * Deletes only rows created by this integration test.
     */
    private function cleanupTestData(): void
    {
        $this->db->executeRun(
            "DELETE FROM `purchase_requests` WHERE `painting_id` IN (SELECT `id` FROM `paintings` WHERE `title` LIKE ?) OR `user_id` IN (SELECT `id` FROM `users` WHERE `username` LIKE ? OR `email` LIKE ?)",
            [$this->marker . '%', $this->marker . '%', strtolower(str_replace('_', '-', trim($this->marker, '_'))) . '%@example.test']
        );

        $this->db->executeRun(
            "DELETE FROM `favorites` WHERE `painting_id` IN (SELECT `id` FROM `paintings` WHERE `title` LIKE ?) OR `user_id` IN (SELECT `id` FROM `users` WHERE `username` LIKE ? OR `email` LIKE ?)",
            [$this->marker . '%', $this->marker . '%', strtolower(str_replace('_', '-', trim($this->marker, '_'))) . '%@example.test']
        );

        $this->db->executeRun(
            "DELETE FROM `painting_tags` WHERE `painting_id` IN (SELECT `id` FROM `paintings` WHERE `title` LIKE ?)",
            [$this->marker . '%']
        );

        $this->db->executeRun(
            "DELETE FROM `paintings` WHERE `title` LIKE ?",
            [$this->marker . '%']
        );

        $this->db->executeRun(
            "DELETE FROM `artists` WHERE `name` LIKE ? OR `user_id` IN (SELECT `id` FROM `users` WHERE `username` LIKE ? OR `email` LIKE ?)",
            [$this->marker . '%', $this->marker . '%', strtolower(str_replace('_', '-', trim($this->marker, '_'))) . '%@example.test']
        );

        $this->db->executeRun(
            "DELETE FROM `categories` WHERE `name` LIKE ?",
            [$this->marker . '%']
        );

        $this->db->executeRun(
            "DELETE FROM `users` WHERE `username` LIKE ? OR `email` LIKE ?",
            [$this->marker . '%', strtolower(str_replace('_', '-', trim($this->marker, '_'))) . '%@example.test']
        );
    }
}

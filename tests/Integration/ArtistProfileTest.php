<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/ArtistProfileService.php';
require_once __DIR__ . '/../../models/Artists.php';
require_once __DIR__ . '/../../models/Auth.php';
require_once __DIR__ . '/../../config/Database.php';

class ArtistProfileTest extends TestCase
{
    private Database $db;
    private string $marker = '__TEST__ARTIST_PROFILE__';

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
     * Tests that artist profile creation writes a profile to the database.
     */
    public function testCreateArtistProfileWritesProfileToDatabase()
    {
        // Create a marked artist user and save a profile through the service.
        $userId = $this->createUser($this->marker . 'artist_create', $this->testEmail('create'));

        $result = ArtistProfileService::createProfile([
            'name' => $this->marker . ' Artist Create ',
            'location' => ' Tallinn ',
            'birth_date' => '1992-04-15',
            'bio' => ' Integration artist bio ',
            'picture' => ' test-artist.jpg ',
        ], [], $userId, $this->db);

        $this->assertTrue($result['success']);
        $this->assertSame($this->marker . ' Artist Create', $result['data']['name']);
        $this->assertSame('pending', $result['data']['status']);

        $artist = Artists::getArtistByUserId($userId, $this->db);

        $this->assertIsArray($artist);
        $this->assertSame($this->marker . ' Artist Create', $artist['name']);
        $this->assertSame('Tallinn', $artist['location']);
        $this->assertSame('1992-04-15', $artist['birth_date']);
        $this->assertSame('Integration artist bio', $artist['bio']);
        $this->assertSame('test-artist.jpg', $artist['picture']);
        $this->assertSame('pending', $artist['status']);
    }

    /**
     * Tests that creating a second profile for the same user is forbidden.
     */
    public function testCreateArtistProfileRejectsDuplicateUserProfile()
    {
        // A user can only have one artist profile because artists.user_id is unique.
        $userId = $this->createUser($this->marker . 'artist_duplicate', $this->testEmail('duplicate'));

        $first = ArtistProfileService::createProfile([
            'name' => $this->marker . ' Artist Duplicate',
            'location' => 'Tartu',
            'birth_date' => '',
            'bio' => '',
            'picture' => '',
        ], [], $userId, $this->db);

        $second = ArtistProfileService::createProfile([
            'name' => $this->marker . ' Artist Duplicate Again',
            'location' => 'Parnu',
            'birth_date' => '',
            'bio' => '',
            'picture' => '',
        ], [], $userId, $this->db);

        $this->assertTrue($first['success']);
        $this->assertFalse($second['success']);
        $this->assertContains('Artist profile already exists for this user', $second['errors']);
    }

    /**
     * Tests that profile updates save changed data and preserve status.
     */
    public function testUpdateArtistProfileSavesDataAndPreservesStatus()
    {
        // Update should change editable fields while keeping the existing moderation status.
        $userId = $this->createUser($this->marker . 'artist_update', $this->testEmail('update'));
        ArtistProfileService::createProfile([
            'name' => $this->marker . ' Artist Before Update',
            'location' => 'Narva',
            'birth_date' => '1988-01-02',
            'bio' => 'Before update',
            'picture' => 'before.jpg',
        ], [], $userId, $this->db);

        $this->db->executeRun(
            "UPDATE `artists` SET `status` = 'approved' WHERE `user_id` = ?",
            [$userId]
        );

        $result = ArtistProfileService::updateProfile([
            'name' => $this->marker . ' Artist After Update ',
            'location' => ' Viljandi ',
            'birth_date' => '1989-03-04',
            'bio' => ' After update ',
            'picture' => 'ignored-legacy.jpg',
        ], [], $userId, $this->db);

        $this->assertTrue($result['success']);
        $this->assertSame('approved', $result['data']['status']);

        $artist = Artists::getArtistByUserId($userId, $this->db);

        $this->assertSame($this->marker . ' Artist After Update', $artist['name']);
        $this->assertSame('Viljandi', $artist['location']);
        $this->assertSame('1989-03-04', $artist['birth_date']);
        $this->assertSame('After update', $artist['bio']);
        $this->assertSame('before.jpg', $artist['picture']);
        $this->assertSame('approved', $artist['status']);
    }

    /**
     * Creates a marked artist user directly in the test database.
     */
    private function createUser(string $username, string $email): int
    {
        $this->db->executeRun(
            "INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `status`, `created_at`) VALUES (NULL, ?, ?, ?, 'artist', 'active', ?)",
            [$username, $email, password_hash('Password1', PASSWORD_DEFAULT), date('Y-m-d')]
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
     * Deletes only artist profiles and users created by this integration test.
     */
    private function cleanupTestData(): void
    {
        $this->db->executeRun(
            "DELETE FROM `artists` WHERE `name` LIKE ? OR `user_id` IN (SELECT `id` FROM `users` WHERE `username` LIKE ? OR `email` LIKE ?)",
            [$this->marker . '%', $this->marker . '%', strtolower(str_replace('_', '-', trim($this->marker, '_'))) . '%@example.test']
        );

        $this->db->executeRun(
            "DELETE FROM `users` WHERE `username` LIKE ? OR `email` LIKE ?",
            [$this->marker . '%', strtolower(str_replace('_', '-', trim($this->marker, '_'))) . '%@example.test']
        );
    }
}

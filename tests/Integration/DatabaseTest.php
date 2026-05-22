<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../config/Database.php';

class DatabaseTest extends TestCase
{
    private Database $db;
    private string $marker = '__TEST__DATABASE__';

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
        $this->db->disconnect();
    }

    /**
     * Tests the core database wrapper methods against the test database.
     */
    public function testDatabaseCoreMethodsUseTestDatabase()
    {
        // connect() should create a PDO connection lazily.
        $connection = $this->db->connect();
        $this->assertInstanceOf(PDO::class, $connection);

        // executeRun() should run a write query and getLastInsertId() should expose the new id.
        $this->db->executeRun(
            "INSERT INTO `categories` (`name`) VALUES (?)",
            [$this->marker . ' Category']
        );

        $categoryId = (int)$this->db->getLastInsertId();
        $this->assertGreaterThan(0, $categoryId);

        // getOne() should return the marked row.
        $row = $this->db->getOne(
            "SELECT * FROM `categories` WHERE `id` = ?",
            [$categoryId]
        );

        $this->assertIsArray($row);
        $this->assertSame($this->marker . ' Category', $row['name']);

        // getAll() should return an array of rows for the marked data.
        $rows = $this->db->getAll(
            "SELECT * FROM `categories` WHERE `name` LIKE ?",
            [$this->marker . '%']
        );

        $this->assertCount(1, $rows);
        $this->assertSame($categoryId, (int)$rows[0]['id']);

        // disconnect() should close the connection; the next query should reconnect lazily.
        $this->db->disconnect();
        $afterReconnect = $this->db->getOne("SELECT 1 AS value");

        $this->assertSame(1, (int)$afterReconnect['value']);
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

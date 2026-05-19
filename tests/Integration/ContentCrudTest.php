<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/CategoryService.php';
require_once __DIR__ . '/../../services/CollectionService.php';
require_once __DIR__ . '/../../services/ExhibitionService.php';
require_once __DIR__ . '/../../models/Categories.php';
require_once __DIR__ . '/../../models/Collections.php';
require_once __DIR__ . '/../../models/Exhibitions.php';
require_once __DIR__ . '/../../config/Database.php';

class ContentCrudTest extends TestCase
{
    private Database $db;
    private string $marker = '__TEST__CONTENT_CRUD__';

    /**
     * Prepares a real test database connection and removes stale marked content.
     */
    protected function setUp(): void
    {
        $this->assertTestEnvironment();
        $this->db = new Database();
        $this->cleanupTestData();
    }

    /**
     * Removes marked content rows after each integration test.
     */
    protected function tearDown(): void
    {
        $this->assertTestEnvironment();
        $this->cleanupTestData();
    }

    /**
     * Tests category creation, update, and deletion through the service and database.
     */
    public function testCategoryCreateUpdateDelete()
    {
        // Create a marked category and verify it is written to the database.
        $create = CategoryService::createCategory([
            'name' => $this->marker . ' Category',
        ], $this->db);

        $this->assertTrue($create['success']);

        $category = $this->db->getOne(
            'SELECT * FROM categories WHERE name = ? LIMIT 1',
            [$this->marker . ' Category']
        );

        $this->assertIsArray($category);
        $categoryId = (int)$category['id'];

        // Update the marked category and verify the changed value.
        $update = CategoryService::updateCategory($categoryId, [
            'name' => $this->marker . ' Category Updated',
        ], $this->db);

        $this->assertTrue($update['success']);
        $updated = Categories::getCategoryByID($categoryId, $this->db);
        $this->assertSame($this->marker . ' Category Updated', $updated['name']);

        // Delete the marked category and verify the row is gone.
        $delete = CategoryService::deleteCategory($categoryId, ['save' => '1'], $this->db);

        $this->assertTrue($delete['success']);
        $this->assertFalse(Categories::getCategoryByID($categoryId, $this->db));
    }

    /**
     * Tests collection creation, update, and deletion through the service and database.
     */
    public function testCollectionCreateUpdateDelete()
    {
        // Create a marked collection and verify the returned id can load the row.
        $create = CollectionService::createCollection([
            'title' => $this->marker . ' Collection',
            'type' => 'keyword',
            'param' => 'blue',
        ], $this->db);

        $this->assertTrue($create['success']);
        $collectionId = (int)$create['id'];

        $collection = Collections::getCollectionByID($collectionId, $this->db);
        $this->assertSame($this->marker . ' Collection', $collection['title']);
        $this->assertSame('keyword', $collection['type']);
        $this->assertSame('blue', $collection['param']);

        // Update the marked collection and verify the changed values.
        $update = CollectionService::updateCollection($collectionId, [
            'title' => $this->marker . ' Collection Updated',
            'type' => 'latest',
            'param' => '',
        ], $this->db);

        $this->assertTrue($update['success']);
        $updated = Collections::getCollectionByID($collectionId, $this->db);
        $this->assertSame($this->marker . ' Collection Updated', $updated['title']);
        $this->assertSame('latest', $updated['type']);

        // Delete the marked collection and verify the row is gone.
        $delete = CollectionService::deleteCollection($collectionId, ['save' => '1'], $this->db);

        $this->assertTrue($delete['success']);
        $this->assertFalse(Collections::getCollectionByID($collectionId, $this->db));
    }

    /**
     * Tests exhibition creation, update, and deletion with a real collection relation.
     */
    public function testExhibitionCreateUpdateDeleteWithCollectionRelation()
    {
        // Create a collection first because exhibitions require a valid collection_id.
        $collectionCreate = CollectionService::createCollection([
            'title' => $this->marker . ' Exhibition Collection',
            'type' => 'latest',
            'param' => '',
        ], $this->db);

        $this->assertTrue($collectionCreate['success']);
        $collectionId = (int)$collectionCreate['id'];

        // Create a marked exhibition linked to the marked collection.
        $create = ExhibitionService::createExhibition([
            'title' => $this->marker . ' Exhibition',
            'description' => 'Integration exhibition',
            'collection_id' => $collectionId,
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-31',
        ], $this->db);

        $this->assertTrue($create['success']);

        $exhibition = $this->db->getOne(
            'SELECT * FROM exhibitions WHERE title = ? LIMIT 1',
            [$this->marker . ' Exhibition']
        );

        $this->assertIsArray($exhibition);
        $exhibitionId = (int)$exhibition['id'];
        $this->assertSame($collectionId, (int)$exhibition['collection_id']);

        // Update the marked exhibition and verify the changed values.
        $update = ExhibitionService::updateExhibition($exhibitionId, [
            'title' => $this->marker . ' Exhibition Updated',
            'description' => 'Updated integration exhibition',
            'collection_id' => $collectionId,
            'start_date' => '2026-02-01',
            'end_date' => '2026-02-15',
        ], $this->db);

        $this->assertTrue($update['success']);
        $updated = Exhibitions::getExhibitionByID($exhibitionId, $this->db);
        $this->assertSame($this->marker . ' Exhibition Updated', $updated['title']);
        $this->assertSame('Updated integration exhibition', $updated['description']);
        $this->assertSame($collectionId, (int)$updated['collection_id']);

        // Delete the exhibition first, then delete the collection it referenced.
        $deleteExhibition = ExhibitionService::deleteExhibition($exhibitionId, ['save' => '1'], $this->db);
        $deleteCollection = CollectionService::deleteCollection($collectionId, ['save' => '1'], $this->db);

        $this->assertTrue($deleteExhibition['success']);
        $this->assertTrue($deleteCollection['success']);
        $this->assertFalse(Exhibitions::getExhibitionByID($exhibitionId, $this->db));
        $this->assertFalse(Collections::getCollectionByID($collectionId, $this->db));
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
     * Deletes only content rows created by this integration test.
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

        $this->db->executeRun(
            "DELETE FROM `categories` WHERE `name` LIKE ?",
            [$this->marker . '%']
        );
    }
}

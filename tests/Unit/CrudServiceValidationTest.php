<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/CategoryService.php';
require_once __DIR__ . '/../../services/CollectionService.php';
require_once __DIR__ . '/../../services/ExhibitionService.php';

class CrudServiceValidationTest extends TestCase
{
    /**
     * Tests that category creation rejects an empty name.
     */
    public function testCategoryCreateRejectsEmptyName()
    {
        // Empty names are rejected before any category model call is needed.
        $result = CategoryService::createCategory(['name' => '   ']);

        $this->assertFalse($result['success']);
        $this->assertSame('Category name is required', $result['errorMessage']);
    }

    /**
     * Tests that category update rejects an empty name.
     */
    public function testCategoryUpdateRejectsEmptyName()
    {
        // Update validation uses the same required-name rule as creation.
        $result = CategoryService::updateCategory(10, ['name' => '']);

        $this->assertFalse($result['success']);
        $this->assertSame('Category name is required', $result['errorMessage']);
    }

    /**
     * Tests that category deletion requires confirmation.
     */
    public function testCategoryDeleteRequiresConfirmation()
    {
        // Missing confirmation avoids the destructive model call.
        $result = CategoryService::deleteCategory(10, []);

        $this->assertFalse($result['success']);
        $this->assertSame('Delete action was not confirmed', $result['errorMessage']);
    }

    /**
     * Tests that collection creation rejects an empty title.
     */
    public function testCollectionCreateRejectsEmptyTitle()
    {
        // Title validation happens before type and duplicate checks.
        $result = CollectionService::createCollection(['title' => ' ', 'type' => 'latest']);

        $this->assertFalse($result['success']);
        $this->assertSame('Collection title is required', $result['errorMessage']);
    }

    /**
     * Tests that collection creation rejects unsupported collection types.
     */
    public function testCollectionCreateRejectsInvalidType()
    {
        // Invalid collection types are rejected before model calls.
        $result = CollectionService::createCollection(['title' => 'Featured', 'type' => 'unknown']);

        $this->assertFalse($result['success']);
        $this->assertSame('Please select a valid collection type', $result['errorMessage']);
    }

    /**
     * Tests that collection update rejects unsupported collection types.
     */
    public function testCollectionUpdateRejectsInvalidType()
    {
        // Update uses the same allowed type whitelist as creation.
        $result = CollectionService::updateCollection(5, ['title' => 'Featured', 'type' => 'unknown']);

        $this->assertFalse($result['success']);
        $this->assertSame('Please select a valid collection type', $result['errorMessage']);
    }

    /**
     * Tests that collection deletion requires confirmation.
     */
    public function testCollectionDeleteRequiresConfirmation()
    {
        // Missing confirmation returns early before deleting anything.
        $result = CollectionService::deleteCollection(5, []);

        $this->assertFalse($result['success']);
        $this->assertSame('Delete action was not confirmed', $result['errorMessage']);
    }

    /**
     * Tests that exhibition creation rejects an empty title.
     */
    public function testExhibitionCreateRejectsEmptyTitle()
    {
        // Empty title is the first validation branch in exhibition creation.
        $result = ExhibitionService::createExhibition(['title' => '']);

        $this->assertFalse($result['success']);
        $this->assertSame('Exhibition title is required', $result['errorMessage']);
    }

    /**
     * Tests that exhibition creation requires a selected collection.
     */
    public function testExhibitionCreateRejectsMissingCollection()
    {
        // Collection id must be positive before the model is consulted.
        $result = ExhibitionService::createExhibition(['title' => 'Spring Show', 'collection_id' => 0]);

        $this->assertFalse($result['success']);
        $this->assertSame('Please select a collection', $result['errorMessage']);
    }

    /**
     * Tests that exhibition update requires a selected collection.
     */
    public function testExhibitionUpdateRejectsMissingCollection()
    {
        // Update has the same positive collection id requirement.
        $result = ExhibitionService::updateExhibition(7, ['title' => 'Spring Show', 'collection_id' => 0]);

        $this->assertFalse($result['success']);
        $this->assertSame('Please select a collection', $result['errorMessage']);
    }

    /**
     * Tests that exhibition deletion requires confirmation.
     */
    public function testExhibitionDeleteRequiresConfirmation()
    {
        // Missing confirmation avoids the exhibition delete model call.
        $result = ExhibitionService::deleteExhibition(7, []);

        $this->assertFalse($result['success']);
        $this->assertSame('Delete action was not confirmed', $result['errorMessage']);
    }
}

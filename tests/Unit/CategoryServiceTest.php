<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/CategoryService.php';
require_once __DIR__ . '/../../models/Categories.php';
require_once __DIR__ . '/../../config/Database.php';

class CategoryServiceTest extends TestCase
{
    /**
     * Tests that category creation rejects duplicate names.
     */
    public function testCreateCategoryRejectsDuplicateName()
    {
        // Duplicate category names should stop creation before insert.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT id FROM categories WHERE TRIM(LOWER(name)) = TRIM(LOWER(?)) LIMIT 1'),
                $this->equalTo(['Landscape'])
            )
            ->willReturn(['id' => 3]);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = CategoryService::createCategory(['name' => ' Landscape '], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame('Category already exists', $result['errorMessage']);
    }

    /**
     * Tests that category creation reports database failures.
     */
    public function testCreateCategoryReturnsErrorWhenInsertFails()
    {
        // Valid unique names should surface a failed insert operation.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(null);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->willReturn(false);

        $result = CategoryService::createCategory(['name' => ' Landscape '], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame('Database error while adding category', $result['errorMessage']);
    }

    /**
     * Tests that category creation trims and saves a valid name.
     */
    public function testCreateCategoryReturnsSuccessWhenSaved()
    {
        // The category name should be trimmed before duplicate checks and insert.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT id FROM categories WHERE TRIM(LOWER(name)) = TRIM(LOWER(?)) LIMIT 1'),
                $this->equalTo(['Landscape'])
            )
            ->willReturn(null);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('INSERT INTO `categories` (`name`) VALUES (?)'),
                $this->equalTo(['Landscape'])
            )
            ->willReturn(true);

        $result = CategoryService::createCategory(['name' => ' Landscape '], $dbMock);

        $this->assertTrue($result['success']);
        $this->assertNull($result['errorMessage']);
    }

    /**
     * Tests that category updates reject duplicate names.
     */
    public function testUpdateCategoryRejectsDuplicateName()
    {
        // Update duplicate checks should exclude the current category id.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT id FROM categories WHERE TRIM(LOWER(name)) = TRIM(LOWER(?)) AND id <> ? LIMIT 1'),
                $this->equalTo(['Portrait', 7])
            )
            ->willReturn(['id' => 8]);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = CategoryService::updateCategory(7, ['name' => ' Portrait '], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame('Category already exists', $result['errorMessage']);
    }

    /**
     * Tests that category updates report database failures.
     */
    public function testUpdateCategoryReturnsErrorWhenUpdateFails()
    {
        // Valid unique names should surface a failed update operation.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(null);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->willReturn(false);

        $result = CategoryService::updateCategory(7, ['name' => ' Portrait '], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame('Database error while updating category', $result['errorMessage']);
    }

    /**
     * Tests that category updates trim and save a valid name.
     */
    public function testUpdateCategoryReturnsSuccessWhenSaved()
    {
        // The update should bind the trimmed category name and category id.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(null);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('UPDATE `categories` SET `name` = ? WHERE `id` = ?'),
                $this->equalTo(['Portrait', 7])
            )
            ->willReturn(true);

        $result = CategoryService::updateCategory(7, ['name' => ' Portrait '], $dbMock);

        $this->assertTrue($result['success']);
        $this->assertNull($result['errorMessage']);
    }

    /**
     * Tests that category deletion reports database failures.
     */
    public function testDeleteCategoryReturnsErrorWhenDeleteFails()
    {
        // Confirmed deletion should surface a failed delete operation.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('DELETE FROM `categories` WHERE `id` = ?'),
                $this->equalTo([7])
            )
            ->willReturn(false);

        $result = CategoryService::deleteCategory(7, ['save' => '1'], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame('Database error while deleting category', $result['errorMessage']);
    }

    /**
     * Tests that category deletion returns success when the model deletes the row.
     */
    public function testDeleteCategoryReturnsSuccessWhenDeleted()
    {
        // Confirmed deletion should call the model and return success.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('DELETE FROM `categories` WHERE `id` = ?'),
                $this->equalTo([7])
            )
            ->willReturn(true);

        $result = CategoryService::deleteCategory(7, ['save' => '1'], $dbMock);

        $this->assertTrue($result['success']);
        $this->assertNull($result['errorMessage']);
    }
}

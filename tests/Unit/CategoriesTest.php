<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../models/Categories.php';
require_once __DIR__ . '/../../config/Database.php';

class CategoriesTest extends TestCase
{
    /**
     * Tests that the model returns all category rows from the injected database.
     */
    public function testGetAllCategoriesReturnsRowsFromDatabase()
    {
        // The model should delegate the plain list query to Database::getAll().
        $rows = [
            ['id' => 1, 'name' => 'Abstract'],
            ['id' => 2, 'name' => 'Portrait'],
        ];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with($this->equalTo('SELECT * FROM categories'))
            ->willReturn($rows);

        $this->assertSame($rows, Categories::getAllCategories($dbMock));
    }

    /**
     * Tests that category lookup uses a parameterized id query.
     */
    public function testGetCategoryByIdUsesParameterizedQuery()
    {
        // Category lookup should use the provided id as a bound parameter.
        $category = ['id' => 5, 'name' => 'Modern'];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT * FROM categories WHERE id = ?'),
                $this->equalTo([5])
            )
            ->willReturn($category);

        $this->assertSame($category, Categories::getCategoryByID(5, $dbMock));
    }

    /**
     * Tests that the admin category list uses the ordered list query.
     */
    public function testGetCategoriesListReturnsOrderedRows()
    {
        // Admin selectors use the alphabetically ordered list query.
        $rows = [
            ['id' => 1, 'name' => 'Abstract'],
            ['id' => 2, 'name' => 'Landscape'],
        ];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with($this->equalTo('SELECT * FROM categories ORDER BY categories.name ASC'))
            ->willReturn($rows);

        $this->assertSame($rows, Categories::getCategoriesList($dbMock));
    }

    /**
     * Tests that an existing category name is reported as taken.
     */
    public function testExistsByNameReturnsTrueWhenRowExists()
    {
        // Any returned row means the normalized category name already exists.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT id FROM categories WHERE TRIM(LOWER(name)) = TRIM(LOWER(?)) LIMIT 1'),
                $this->equalTo(['Abstract'])
            )
            ->willReturn(['id' => 1]);

        $this->assertTrue(Categories::existsByName('Abstract', $dbMock));
    }

    /**
     * Tests that a missing category name is reported as available.
     */
    public function testExistsByNameReturnsFalseWhenNoRowExists()
    {
        // Null lookup results should be converted to false.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(null);

        $this->assertFalse(Categories::existsByName('Missing', $dbMock));
    }

    /**
     * Tests that successful category insertion returns true.
     */
    public function testCreateReturnsTrueWhenInsertSucceeds()
    {
        // A truthy executeRun result is normalized to boolean true.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('INSERT INTO `categories` (`name`) VALUES (?)'),
                $this->equalTo(['Sculpture'])
            )
            ->willReturn(true);

        $this->assertTrue(Categories::create('Sculpture', $dbMock));
    }

    /**
     * Tests that failed category insertion returns false.
     */
    public function testCreateReturnsFalseWhenInsertFails()
    {
        // A failed insert is normalized to boolean false.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->willReturn(false);

        $this->assertFalse(Categories::create('Sculpture', $dbMock));
    }

    /**
     * Tests that duplicate checks exclude the current category id.
     */
    public function testExistsByNameExceptIdUsesExcludedId()
    {
        // Duplicate checks during update must exclude the current category id.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT id FROM categories WHERE TRIM(LOWER(name)) = TRIM(LOWER(?)) AND id <> ? LIMIT 1'),
                $this->equalTo(['Landscape', 7])
            )
            ->willReturn(['id' => 8]);

        $this->assertTrue(Categories::existsByNameExceptId('Landscape', 7, $dbMock));
    }

    /**
     * Tests that successful category updates return true.
     */
    public function testUpdateCategoryReturnsTrueWhenUpdateSucceeds()
    {
        // Update should bind the new name and category id in order.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('UPDATE `categories` SET `name` = ? WHERE `id` = ?'),
                $this->equalTo(['Updated', 3])
            )
            ->willReturn(true);

        $this->assertTrue(Categories::updateCategory(3, 'Updated', $dbMock));
    }

    /**
     * Tests that successful category deletion returns true.
     */
    public function testDeleteCategoryReturnsTrueWhenDeleteSucceeds()
    {
        // Delete should pass the category id as the only bound parameter.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('DELETE FROM `categories` WHERE `id` = ?'),
                $this->equalTo([4])
            )
            ->willReturn(true);

        $this->assertTrue(Categories::deleteCategory(4, $dbMock));
    }

    /**
     * Tests that the category count is cast to an integer.
     */
    public function testCountReturnsIntegerCount()
    {
        // The count method should cast the database count field to integer.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with($this->equalTo('SELECT COUNT(*) AS cnt FROM categories'))
            ->willReturn(['cnt' => '12']);

        $this->assertSame(12, Categories::count($dbMock));
    }
}

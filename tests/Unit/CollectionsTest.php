<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../models/Collections.php';
require_once __DIR__ . '/../../config/Database.php';

class CollectionsTest extends TestCase
{
    /**
     * Tests that collection lookup uses a parameterized id query.
     */
    public function testGetCollectionByIdUsesParameterizedQuery()
    {
        // Collection lookup should bind the id parameter instead of interpolating it.
        $collection = ['id' => 3, 'title' => 'Latest Works', 'type' => 'latest', 'param' => ''];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT * FROM collections WHERE id = ?'),
                $this->equalTo([3])
            )
            ->willReturn($collection);

        $this->assertSame($collection, Collections::getCollectionByID(3, $dbMock));
    }

    /**
     * Tests that the collection list returns rows from the injected database.
     */
    public function testGetCollectionsListReturnsRowsFromDatabase()
    {
        // Admin list query should return collections ordered by newest id first.
        $rows = [
            ['id' => 2, 'title' => 'Random Picks'],
            ['id' => 1, 'title' => 'Latest Works'],
        ];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with($this->equalTo('SELECT * FROM collections ORDER BY id DESC'))
            ->willReturn($rows);

        $this->assertSame($rows, Collections::getCollectionsList($dbMock));
    }

    /**
     * Tests that an existing collection title is reported as taken.
     */
    public function testExistsByTitleReturnsTrueWhenRowExists()
    {
        // Any row from the normalized title lookup means the title is taken.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT id FROM collections WHERE TRIM(LOWER(title)) = TRIM(LOWER(?)) LIMIT 1'),
                $this->equalTo(['Featured'])
            )
            ->willReturn(['id' => 1]);

        $this->assertTrue(Collections::existsByTitle('Featured', $dbMock));
    }

    /**
     * Tests that a missing collection title is reported as available.
     */
    public function testExistsByTitleReturnsFalseWhenNoRowExists()
    {
        // Null lookup results should be converted to false.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(null);

        $this->assertFalse(Collections::existsByTitle('Missing', $dbMock));
    }

    /**
     * Tests that successful collection insertion returns the new id.
     */
    public function testCreateReturnsNewIdWhenInsertSucceeds()
    {
        // Successful inserts return the last inserted collection id.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('INSERT INTO `collections` (`title`, `type`, `param`) VALUES (?, ?, ?)'),
                $this->equalTo(['Featured', 'keyword', 'blue'])
            )
            ->willReturn(true);
        $dbMock->expects($this->once())
            ->method('getLastInsertId')
            ->willReturn('42');

        $this->assertSame('42', Collections::create('Featured', 'keyword', 'blue', $dbMock));
    }

    /**
     * Tests that failed collection insertion returns false.
     */
    public function testCreateReturnsFalseWhenInsertFails()
    {
        // Failed inserts should not request a last insert id.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->willReturn(false);
        $dbMock->expects($this->never())
            ->method('getLastInsertId');

        $this->assertFalse(Collections::create('Featured', 'keyword', 'blue', $dbMock));
    }

    /**
     * Tests that duplicate checks exclude the current collection id.
     */
    public function testExistsByTitleExceptIdUsesExcludedId()
    {
        // Duplicate checks during update must exclude the current collection id.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT id FROM collections WHERE TRIM(LOWER(title)) = TRIM(LOWER(?)) AND id <> ? LIMIT 1'),
                $this->equalTo(['Featured', 5])
            )
            ->willReturn(['id' => 6]);

        $this->assertTrue(Collections::existsByTitleExceptId('Featured', 5, $dbMock));
    }

    /**
     * Tests that successful collection updates return true.
     */
    public function testUpdateCollectionReturnsTrueWhenUpdateSucceeds()
    {
        // Update should bind title, type, param, and id in that order.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('UPDATE `collections` SET `title` = ?, `type` = ?, `param` = ? WHERE `id` = ?'),
                $this->equalTo(['Updated', 'latest', '', 7])
            )
            ->willReturn(true);

        $this->assertTrue(Collections::updateCollection(7, 'Updated', 'latest', '', $dbMock));
    }

    /**
     * Tests that failed collection updates return false.
     */
    public function testUpdateCollectionReturnsFalseWhenUpdateFails()
    {
        // Failed updates should be normalized to boolean false.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->willReturn(false);

        $this->assertFalse(Collections::updateCollection(7, 'Updated', 'latest', '', $dbMock));
    }

    /**
     * Tests that successful collection deletion returns true.
     */
    public function testDeleteCollectionReturnsTrueWhenDeleteSucceeds()
    {
        // Delete should pass the collection id as the only bound parameter.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('DELETE FROM `collections` WHERE `id` = ?'),
                $this->equalTo([9])
            )
            ->willReturn(true);

        $this->assertTrue(Collections::deleteCollection(9, $dbMock));
    }

    /**
     * Tests that the collection count is cast to an integer.
     */
    public function testCountReturnsIntegerCount()
    {
        // The count method should cast the database count field to integer.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with($this->equalTo('SELECT COUNT(*) AS cnt FROM collections'))
            ->willReturn(['cnt' => '8']);

        $this->assertSame(8, Collections::count($dbMock));
    }
}

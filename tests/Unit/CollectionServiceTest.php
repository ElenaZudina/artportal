<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/CollectionService.php';
require_once __DIR__ . '/../../models/Collections.php';
require_once __DIR__ . '/../../config/Database.php';

class CollectionServiceTest extends TestCase
{
    /**
     * Tests that collection creation rejects duplicate titles.
     */
    public function testCreateCollectionRejectsDuplicateTitle()
    {
        // Duplicate collection titles should stop creation before insert.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT id FROM collections WHERE TRIM(LOWER(title)) = TRIM(LOWER(?)) LIMIT 1'),
                $this->equalTo(['Featured'])
            )
            ->willReturn(['id' => 3]);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = CollectionService::createCollection([
            'title' => ' Featured ',
            'type' => 'keyword',
            'param' => ' blue ',
        ], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame('Collection already exists', $result['errorMessage']);
    }

    /**
     * Tests that collection creation reports database failures.
     */
    public function testCreateCollectionReturnsErrorWhenInsertFails()
    {
        // Valid unique collections should surface a failed insert operation.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(null);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->willReturn(false);
        $dbMock->expects($this->never())
            ->method('getLastInsertId');

        $result = CollectionService::createCollection([
            'title' => ' Featured ',
            'type' => 'keyword',
            'param' => ' blue ',
        ], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame('Database error while adding collection', $result['errorMessage']);
    }

    /**
     * Tests that collection creation trims and saves valid data.
     */
    public function testCreateCollectionReturnsSuccessWithNewId()
    {
        // Collection fields should be trimmed before insert and the new id should be returned.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(null);
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

        $result = CollectionService::createCollection([
            'title' => ' Featured ',
            'type' => ' keyword ',
            'param' => ' blue ',
        ], $dbMock);

        $this->assertTrue($result['success']);
        $this->assertNull($result['errorMessage']);
        $this->assertSame('42', $result['id']);
    }

    /**
     * Tests that collection updates reject duplicate titles.
     */
    public function testUpdateCollectionRejectsDuplicateTitle()
    {
        // Update duplicate checks should exclude the current collection id.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT id FROM collections WHERE TRIM(LOWER(title)) = TRIM(LOWER(?)) AND id <> ? LIMIT 1'),
                $this->equalTo(['Featured', 7])
            )
            ->willReturn(['id' => 8]);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = CollectionService::updateCollection(7, [
            'title' => ' Featured ',
            'type' => 'latest',
        ], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame('Collection already exists', $result['errorMessage']);
    }

    /**
     * Tests that collection updates report database failures.
     */
    public function testUpdateCollectionReturnsErrorWhenUpdateFails()
    {
        // Valid unique collections should surface a failed update operation.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(null);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->willReturn(false);

        $result = CollectionService::updateCollection(7, [
            'title' => ' Featured ',
            'type' => 'latest',
            'param' => '',
        ], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame('Database error while updating collection', $result['errorMessage']);
    }

    /**
     * Tests that collection updates trim and save valid data.
     */
    public function testUpdateCollectionReturnsSuccessWhenSaved()
    {
        // The update should bind title, type, param, and id in order.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(null);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('UPDATE `collections` SET `title` = ?, `type` = ?, `param` = ? WHERE `id` = ?'),
                $this->equalTo(['Featured', 'latest', '', 7])
            )
            ->willReturn(true);

        $result = CollectionService::updateCollection(7, [
            'title' => ' Featured ',
            'type' => ' latest ',
            'param' => ' ',
        ], $dbMock);

        $this->assertTrue($result['success']);
        $this->assertNull($result['errorMessage']);
    }

    /**
     * Tests that collection deletion reports database failures.
     */
    public function testDeleteCollectionReturnsErrorWhenDeleteFails()
    {
        // Confirmed deletion should surface a failed delete operation.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('DELETE FROM `collections` WHERE `id` = ?'),
                $this->equalTo([7])
            )
            ->willReturn(false);

        $result = CollectionService::deleteCollection(7, ['save' => '1'], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame('Database error while deleting collection', $result['errorMessage']);
    }

    /**
     * Tests that collection deletion returns success when the model deletes the row.
     */
    public function testDeleteCollectionReturnsSuccessWhenDeleted()
    {
        // Confirmed deletion should call the model and return success.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('DELETE FROM `collections` WHERE `id` = ?'),
                $this->equalTo([7])
            )
            ->willReturn(true);

        $result = CollectionService::deleteCollection(7, ['save' => '1'], $dbMock);

        $this->assertTrue($result['success']);
        $this->assertNull($result['errorMessage']);
    }
}

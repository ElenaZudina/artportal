<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/ExhibitionService.php';
require_once __DIR__ . '/../../models/Collections.php';
require_once __DIR__ . '/../../models/Exhibitions.php';
require_once __DIR__ . '/../../config/Database.php';

class ExhibitionServiceTest extends TestCase
{
    /**
     * Tests that exhibition creation rejects missing collections.
     */
    public function testCreateExhibitionRejectsMissingCollectionRecord()
    {
        // A positive collection id must still exist before date validation or saving.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT * FROM collections WHERE id = ?'),
                $this->equalTo([5])
            )
            ->willReturn(null);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = ExhibitionService::createExhibition([
            'title' => 'Spring Show',
            'collection_id' => 5,
        ], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame('Selected collection does not exist', $result['errorMessage']);
    }

    /**
     * Tests that exhibition creation requires both dates.
     */
    public function testCreateExhibitionRejectsMissingDates()
    {
        // Date validation runs after the linked collection is confirmed.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(['id' => 5]);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = ExhibitionService::createExhibition([
            'title' => 'Spring Show',
            'collection_id' => 5,
            'start_date' => '',
            'end_date' => '2026-04-30',
        ], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame('Start date and end date are required', $result['errorMessage']);
    }

    /**
     * Tests that exhibition creation rejects invalid date values.
     */
    public function testCreateExhibitionRejectsInvalidDates()
    {
        // Unparseable date strings should be rejected before duplicate checks.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(['id' => 5]);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = ExhibitionService::createExhibition([
            'title' => 'Spring Show',
            'collection_id' => 5,
            'start_date' => 'bad-date',
            'end_date' => '2026-04-30',
        ], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame('Please provide valid dates', $result['errorMessage']);
    }

    /**
     * Tests that exhibition creation rejects an inverted date range.
     */
    public function testCreateExhibitionRejectsStartDateAfterEndDate()
    {
        // The start date may not be later than the end date.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(['id' => 5]);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = ExhibitionService::createExhibition([
            'title' => 'Spring Show',
            'collection_id' => 5,
            'start_date' => '2026-05-10',
            'end_date' => '2026-05-01',
        ], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame('Start date cannot be later than end date', $result['errorMessage']);
    }

    /**
     * Tests that exhibition creation rejects duplicate titles.
     */
    public function testCreateExhibitionRejectsDuplicateTitle()
    {
        // Duplicate title lookup happens after date normalization can succeed.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(2))
            ->method('getOne')
            ->willReturnOnConsecutiveCalls(['id' => 5], ['id' => 9]);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = ExhibitionService::createExhibition([
            'title' => 'Spring Show',
            'collection_id' => 5,
            'start_date' => '2026-05-01',
            'end_date' => '2026-05-10',
        ], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame('Exhibition already exists', $result['errorMessage']);
    }

    /**
     * Tests that exhibition creation reports database failures.
     */
    public function testCreateExhibitionReturnsErrorWhenInsertFails()
    {
        // Valid data should surface a failed insert operation.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(2))
            ->method('getOne')
            ->willReturnOnConsecutiveCalls(['id' => 5], null);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->willReturn(false);

        $result = ExhibitionService::createExhibition([
            'title' => 'Spring Show',
            'description' => ' Seasonal exhibition ',
            'collection_id' => 5,
            'start_date' => '2026-05-01',
            'end_date' => '2026-05-10',
        ], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame('Database error while adding exhibition', $result['errorMessage']);
    }

    /**
     * Tests that exhibition creation normalizes and saves valid data.
     */
    public function testCreateExhibitionReturnsSuccessWhenSaved()
    {
        // Dates should be normalized before being passed to the model insert.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(2))
            ->method('getOne')
            ->willReturnOnConsecutiveCalls(['id' => 5], null);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('INSERT INTO `exhibitions` (`title`, `description`, `collection_id`, `start_date`, `end_date`) VALUES (?, ?, ?, ?, ?)'),
                $this->equalTo(['Spring Show', 'Seasonal exhibition', 5, '2026-05-01 00:00:00', '2026-05-10 00:00:00'])
            )
            ->willReturn(true);

        $result = ExhibitionService::createExhibition([
            'title' => ' Spring Show ',
            'description' => ' Seasonal exhibition ',
            'collection_id' => 5,
            'start_date' => '2026-05-01',
            'end_date' => '2026-05-10',
        ], $dbMock);

        $this->assertTrue($result['success']);
        $this->assertNull($result['errorMessage']);
    }

    /**
     * Tests that exhibition updates reject duplicate titles.
     */
    public function testUpdateExhibitionRejectsDuplicateTitle()
    {
        // Update duplicate checks must exclude the current exhibition id.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(2))
            ->method('getOne')
            ->willReturnOnConsecutiveCalls(['id' => 5], ['id' => 12]);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = ExhibitionService::updateExhibition(7, [
            'title' => 'Spring Show',
            'collection_id' => 5,
            'start_date' => '2026-05-01',
            'end_date' => '2026-05-10',
        ], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame('Exhibition already exists', $result['errorMessage']);
    }

    /**
     * Tests that exhibition updates report database failures.
     */
    public function testUpdateExhibitionReturnsErrorWhenUpdateFails()
    {
        // Valid update data should surface a failed update operation.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(2))
            ->method('getOne')
            ->willReturnOnConsecutiveCalls(['id' => 5], null);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->willReturn(false);

        $result = ExhibitionService::updateExhibition(7, [
            'title' => 'Updated Show',
            'description' => 'Updated description',
            'collection_id' => 5,
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-10',
        ], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame('Database error while updating exhibition', $result['errorMessage']);
    }

    /**
     * Tests that exhibition updates normalize and save valid data.
     */
    public function testUpdateExhibitionReturnsSuccessWhenSaved()
    {
        // Update should bind normalized fields followed by the exhibition id.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(2))
            ->method('getOne')
            ->willReturnOnConsecutiveCalls(['id' => 5], null);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('UPDATE `exhibitions` SET `title` = ?, `description` = ?, `collection_id` = ?, `start_date` = ?, `end_date` = ? WHERE `id` = ?'),
                $this->equalTo(['Updated Show', 'Updated description', 5, '2026-06-01 00:00:00', '2026-06-10 00:00:00', 7])
            )
            ->willReturn(true);

        $result = ExhibitionService::updateExhibition(7, [
            'title' => ' Updated Show ',
            'description' => ' Updated description ',
            'collection_id' => 5,
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-10',
        ], $dbMock);

        $this->assertTrue($result['success']);
        $this->assertNull($result['errorMessage']);
    }

    /**
     * Tests that exhibition deletion reports database failures.
     */
    public function testDeleteExhibitionReturnsErrorWhenDeleteFails()
    {
        // Confirmed deletion should surface a failed delete operation.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('DELETE FROM `exhibitions` WHERE `id` = ?'),
                $this->equalTo([7])
            )
            ->willReturn(false);

        $result = ExhibitionService::deleteExhibition(7, ['save' => '1'], $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame('Database error while deleting exhibition', $result['errorMessage']);
    }

    /**
     * Tests that exhibition deletion returns success when the model deletes the row.
     */
    public function testDeleteExhibitionReturnsSuccessWhenDeleted()
    {
        // Confirmed deletion should call the model and return a successful result.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('DELETE FROM `exhibitions` WHERE `id` = ?'),
                $this->equalTo([7])
            )
            ->willReturn(true);

        $result = ExhibitionService::deleteExhibition(7, ['save' => '1'], $dbMock);

        $this->assertTrue($result['success']);
        $this->assertNull($result['errorMessage']);
    }
}

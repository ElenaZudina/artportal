<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../models/Exhibitions.php';
require_once __DIR__ . '/../../config/Database.php';

class ExhibitionsTest extends TestCase
{
    /**
     * Tests that exhibition lookup uses a parameterized id query.
     */
    public function testGetExhibitionByIdUsesParameterizedQuery()
    {
        // Exhibition lookup should bind the id parameter instead of interpolating it.
        $exhibition = ['id' => 4, 'title' => 'Spring Exhibition'];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT * FROM exhibitions WHERE id = ?'),
                $this->equalTo([4])
            )
            ->willReturn($exhibition);

        $this->assertSame($exhibition, Exhibitions::getExhibitionByID(4, $dbMock));
    }

    /**
     * Tests that all exhibitions are requested newest first.
     */
    public function testGetAllExhibitionsReturnsRowsFromDatabase()
    {
        // Public exhibition listing should be ordered by descending id.
        $rows = [
            ['id' => 2, 'title' => 'New Show'],
            ['id' => 1, 'title' => 'Old Show'],
        ];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with($this->equalTo('SELECT * FROM exhibitions ORDER BY id DESC'))
            ->willReturn($rows);

        $this->assertSame($rows, Exhibitions::getAllExhibitions($dbMock));
    }

    /**
     * Tests that the current exhibition query uses the active date window.
     */
    public function testGetCurrentExhibitionUsesDateWindowQuery()
    {
        // Current exhibition lookup should use NOW() against start and end dates.
        $exhibition = ['id' => 3, 'title' => 'Current Show'];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with($this->equalTo('SELECT * FROM exhibitions WHERE start_date <= NOW() AND end_date >= NOW() ORDER BY start_date DESC LIMIT 1'))
            ->willReturn($exhibition);

        $this->assertSame($exhibition, Exhibitions::getCurrentExhibition($dbMock));
    }

    /**
     * Tests that admin exhibition listing includes collection titles.
     */
    public function testGetExhibitionsListUsesCollectionJoin()
    {
        // Admin list should left join collections to show the collection title.
        $rows = [
            ['id' => 1, 'title' => 'Show', 'collection_title' => 'Featured'],
        ];

        $expectedSql = "SELECT e.id, e.title, e.description, e.collection_id, c.title AS collection_title, e.start_date, e.end_date
                FROM exhibitions e
                LEFT JOIN collections c ON c.id = e.collection_id
                ORDER BY e.id DESC";

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with($this->equalTo($expectedSql))
            ->willReturn($rows);

        $this->assertSame($rows, Exhibitions::getExhibitionsList($dbMock));
    }

    /**
     * Tests that successful exhibition insertion returns true.
     */
    public function testCreateReturnsTrueWhenInsertSucceeds()
    {
        // Create should bind title, description, collection id, start date, and end date.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('INSERT INTO `exhibitions` (`title`, `description`, `collection_id`, `start_date`, `end_date`) VALUES (?, ?, ?, ?, ?)'),
                $this->equalTo(['Show', 'Description', 2, '2026-01-01 00:00:00', '2026-01-31 23:59:59'])
            )
            ->willReturn(true);

        $this->assertTrue(Exhibitions::create('Show', 'Description', 2, '2026-01-01 00:00:00', '2026-01-31 23:59:59', $dbMock));
    }

    /**
     * Tests that failed exhibition insertion returns false.
     */
    public function testCreateReturnsFalseWhenInsertFails()
    {
        // Failed inserts should be normalized to boolean false.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->willReturn(false);

        $this->assertFalse(Exhibitions::create('Show', 'Description', 2, '2026-01-01', '2026-01-31', $dbMock));
    }

    /**
     * Tests that an existing exhibition title is reported as taken.
     */
    public function testExistsByTitleReturnsTrueWhenRowExists()
    {
        // Any row from the normalized title lookup means the title is taken.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT id FROM exhibitions WHERE TRIM(LOWER(title)) = TRIM(LOWER(?)) LIMIT 1'),
                $this->equalTo(['Show'])
            )
            ->willReturn(['id' => 1]);

        $this->assertTrue(Exhibitions::existsByTitle('Show', $dbMock));
    }

    /**
     * Tests that a missing exhibition title is reported as available.
     */
    public function testExistsByTitleReturnsFalseWhenNoRowExists()
    {
        // Null lookup results should be converted to false.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(null);

        $this->assertFalse(Exhibitions::existsByTitle('Missing', $dbMock));
    }

    /**
     * Tests that duplicate checks exclude the current exhibition id.
     */
    public function testExistsByTitleExceptIdUsesExcludedId()
    {
        // Duplicate checks during update must exclude the current exhibition id.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT id FROM exhibitions WHERE TRIM(LOWER(title)) = TRIM(LOWER(?)) AND id <> ? LIMIT 1'),
                $this->equalTo(['Show', 5])
            )
            ->willReturn(['id' => 6]);

        $this->assertTrue(Exhibitions::existsByTitleExceptId('Show', 5, $dbMock));
    }

    /**
     * Tests that successful exhibition updates return true.
     */
    public function testUpdateExhibitionReturnsTrueWhenUpdateSucceeds()
    {
        // Update should bind all editable fields followed by the exhibition id.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('UPDATE `exhibitions` SET `title` = ?, `description` = ?, `collection_id` = ?, `start_date` = ?, `end_date` = ? WHERE `id` = ?'),
                $this->equalTo(['Updated', 'New text', 4, '2026-02-01', '2026-02-28', 9])
            )
            ->willReturn(true);

        $this->assertTrue(Exhibitions::updateExhibition(9, 'Updated', 'New text', 4, '2026-02-01', '2026-02-28', $dbMock));
    }

    /**
     * Tests that failed exhibition updates return false.
     */
    public function testUpdateExhibitionReturnsFalseWhenUpdateFails()
    {
        // Failed updates should be normalized to boolean false.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->willReturn(false);

        $this->assertFalse(Exhibitions::updateExhibition(9, 'Updated', 'New text', 4, '2026-02-01', '2026-02-28', $dbMock));
    }

    /**
     * Tests that successful exhibition deletion returns true.
     */
    public function testDeleteExhibitionReturnsTrueWhenDeleteSucceeds()
    {
        // Delete should pass the exhibition id as the only bound parameter.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('DELETE FROM `exhibitions` WHERE `id` = ?'),
                $this->equalTo([10])
            )
            ->willReturn(true);

        $this->assertTrue(Exhibitions::deleteExhibition(10, $dbMock));
    }

    /**
     * Tests that the exhibition count is cast to an integer.
     */
    public function testCountReturnsIntegerCount()
    {
        // The count method should cast the database count field to integer.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with($this->equalTo('SELECT COUNT(*) AS cnt FROM exhibitions'))
            ->willReturn(['cnt' => '6']);

        $this->assertSame(6, Exhibitions::count($dbMock));
    }
}

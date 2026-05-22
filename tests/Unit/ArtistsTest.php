<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../models/Artists.php';
require_once __DIR__ . '/../../config/Database.php';

class ArtistsTest extends TestCase
{
    /**
     * Tests that the latest artists query returns approved artists ordered by newest id.
     */
    public function testGetLast10ArtistsReturnsRowsFromDatabase()
    {
        // The homepage list should request the latest approved artists only.
        $rows = [
            ['id' => 10, 'name' => 'First Artist'],
            ['id' => 9, 'name' => 'Second Artist'],
        ];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with($this->equalTo("SELECT * FROM artists WHERE status = 'approved' ORDER BY id DESC LIMIT 10"))
            ->willReturn($rows);

        $this->assertSame($rows, Artists::getLast10Artists($dbMock));
    }

    /**
     * Tests that the full public artist list returns approved artists ordered by newest id.
     */
    public function testGetAllArtistsReturnsRowsFromDatabase()
    {
        // Public artist listing should exclude non-approved artist profiles.
        $rows = [
            ['id' => 2, 'status' => 'approved'],
            ['id' => 1, 'status' => 'approved'],
        ];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with($this->equalTo("SELECT * FROM artists WHERE status = 'approved' ORDER BY id DESC"))
            ->willReturn($rows);

        $this->assertSame($rows, Artists::getAllArtists($dbMock));
    }

    /**
     * Tests that the approved artist count is cast to an integer.
     */
    public function testGetAllArtistsCountReturnsIntegerCount()
    {
        // Count values from PDO are often strings, so the model should cast them.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with($this->equalTo("SELECT COUNT(*) AS total FROM artists WHERE status = 'approved'"))
            ->willReturn(['total' => '14']);

        $this->assertSame(14, Artists::getAllArtistsCount($dbMock));
    }

    /**
     * Tests that paginated artist listing casts limit and offset before building SQL.
     */
    public function testGetAllArtistsPaginatedCastsLimitAndOffset()
    {
        // The model should cast pagination values to integers before appending them to SQL.
        $rows = [['id' => 7]];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with($this->equalTo("SELECT * FROM artists WHERE status = 'approved' ORDER BY id DESC LIMIT 5 OFFSET 10"))
            ->willReturn($rows);

        $this->assertSame($rows, Artists::getAllArtistsPaginated('5abc', '10abc', $dbMock));
    }

    /**
     * Tests that empty artist search count falls back to the plain approved count.
     */
    public function testGetSearchArtistsCountUsesPlainCountForEmptySearch()
    {
        // Whitespace-only search should behave like no search.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with($this->equalTo("SELECT COUNT(*) AS total FROM artists WHERE status = 'approved'"))
            ->willReturn(['total' => '3']);

        $this->assertSame(3, Artists::getSearchArtistsCount('   ', $dbMock));
    }

    /**
     * Tests that non-empty artist search count binds the LIKE value for each searched column.
     */
    public function testGetSearchArtistsCountBindsLikeParameters()
    {
        // Search should match name, location, or bio using the same wildcard value.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->callback(function ($query) {
                    return strpos($query, 'FROM artists') !== false
                        && substr_count($query, 'LIKE ?') === 3
                        && strpos($query, "status = 'approved'") !== false;
                }),
                $this->equalTo(['%Monet%', '%Monet%', '%Monet%'])
            )
            ->willReturn(['total' => '2']);

        $this->assertSame(2, Artists::getSearchArtistsCount(' Monet ', $dbMock));
    }

    /**
     * Tests that empty paginated search delegates to the regular paginated query.
     */
    public function testGetSearchArtistsPaginatedUsesRegularPaginationForEmptySearch()
    {
        // Empty search should reuse the normal approved artists pagination query.
        $rows = [['id' => 4]];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with($this->equalTo("SELECT * FROM artists WHERE status = 'approved' ORDER BY id DESC LIMIT 6 OFFSET 12"))
            ->willReturn($rows);

        $this->assertSame($rows, Artists::getSearchArtistsPaginated('', 6, 12, $dbMock));
    }

    /**
     * Tests that non-empty paginated search binds LIKE parameters and pagination.
     */
    public function testGetSearchArtistsPaginatedBindsLikeParameters()
    {
        // Search pagination should keep the LIKE values parameterized.
        $rows = [['id' => 8]];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with(
                $this->callback(function ($query) {
                    return strpos($query, 'FROM artists') !== false
                        && substr_count($query, 'LIKE ?') === 3
                        && strpos($query, 'LIMIT 5 OFFSET 15') !== false;
                }),
                $this->equalTo(['%Paris%', '%Paris%', '%Paris%'])
            )
            ->willReturn($rows);

        $this->assertSame($rows, Artists::getSearchArtistsPaginated('Paris', 5, 15, $dbMock));
    }

    /**
     * Tests that public artist lookup requires approved status and binds the id.
     */
    public function testGetPublicArtistByIdUsesApprovedStatusAndIdParameter()
    {
        // Public lookup should not return pending or rejected artist profiles.
        $artist = ['id' => 11, 'status' => 'approved'];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->callback(function ($query) {
                    return strpos($query, 'WHERE artists.id = ?') !== false
                        && strpos($query, "status = 'approved'") !== false;
                }),
                $this->equalTo([11])
            )
            ->willReturn($artist);

        $this->assertSame($artist, Artists::getPublicArtistByID(11, $dbMock));
    }

    /**
     * Tests that internal artist lookup binds the id without filtering status.
     */
    public function testGetArtistByIdUsesIdParameter()
    {
        // Admin/internal lookup should fetch an artist by id regardless of moderation status.
        $artist = ['id' => 12, 'status' => 'pending'];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->callback(function ($query) {
                    return strpos($query, 'WHERE artists.id = ?') !== false;
                }),
                $this->equalTo([12])
            )
            ->willReturn($artist);

        $this->assertSame($artist, Artists::getArtistByID(12, $dbMock));
    }

    /**
     * Tests that pending artists are returned in moderation order.
     */
    public function testGetPendingArtistsReturnsRowsFromDatabase()
    {
        // Moderation list should show pending artists ordered by creation date and id.
        $rows = [['id' => 3, 'status' => 'pending']];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with($this->equalTo("SELECT * FROM artists WHERE status = 'pending' ORDER BY created_at DESC, id DESC"))
            ->willReturn($rows);

        $this->assertSame($rows, Artists::getPendingArtists($dbMock));
    }

    /**
     * Tests that approving an artist updates both artist status and user role.
     */
    public function testApproveArtistUpdatesArtistAndUserWhenArtistExists()
    {
        // Approval should first load the artist, then promote the related user to artist.
        $calls = [];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with($this->callback(function ($query) {
                return strpos($query, 'WHERE artists.id = ?') !== false;
            }), $this->equalTo([5]))
            ->willReturn(['id' => 5, 'user_id' => 20]);
        $dbMock->expects($this->exactly(2))
            ->method('executeRun')
            ->willReturnCallback(function ($query, $params) use (&$calls) {
                $calls[] = [$query, $params];
                return true;
            });

        $this->assertTrue(Artists::approveArtist(5, $dbMock));
        $this->assertSame("UPDATE artists SET status = 'approved', updated_at = NOW() WHERE id = ?", $calls[0][0]);
        $this->assertSame([5], $calls[0][1]);
        $this->assertSame("UPDATE users SET role = 'artist' WHERE id = ?", $calls[1][0]);
        $this->assertSame([20], $calls[1][1]);
    }

    /**
     * Tests that approving a missing artist returns false without updates.
     */
    public function testApproveArtistReturnsFalseWhenArtistMissing()
    {
        // Missing artist records should stop approval before any update query runs.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(false);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $this->assertFalse(Artists::approveArtist(999, $dbMock));
    }

    /**
     * Tests that rejecting an artist updates both artist status and user role.
     */
    public function testRejectArtistUpdatesArtistAndUserWhenArtistExists()
    {
        // Rejection should mark the profile rejected and return the user to a normal role.
        $calls = [];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(['id' => 6, 'user_id' => 21]);
        $dbMock->expects($this->exactly(2))
            ->method('executeRun')
            ->willReturnCallback(function ($query, $params) use (&$calls) {
                $calls[] = [$query, $params];
                return true;
            });

        $this->assertTrue(Artists::rejectArtist(6, $dbMock));
        $this->assertSame("UPDATE artists SET status = 'rejected', updated_at = NOW() WHERE id = ?", $calls[0][0]);
        $this->assertSame([6], $calls[0][1]);
        $this->assertSame("UPDATE users SET role = 'user' WHERE id = ?", $calls[1][0]);
        $this->assertSame([21], $calls[1][1]);
    }

    /**
     * Tests that artist profile lookup binds the user id.
     */
    public function testGetArtistByUserIdUsesUserIdParameter()
    {
        // Dashboard profile lookup should search by the owner user id.
        $artist = ['id' => 2, 'user_id' => 10];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT * FROM artists WHERE user_id = ?'),
                $this->equalTo([10])
            )
            ->willReturn($artist);

        $this->assertSame($artist, Artists::getArtistByUserId(10, $dbMock));
    }

    /**
     * Tests that inserting an artist profile binds validated form data.
     */
    public function testInsertArtistProfileUsesCleanDataParameters()
    {
        // Insert should map clean data fields to the artist profile columns in order.
        $cleanData = $this->artistData();

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->callback(function ($query) {
                    return strpos($query, 'INSERT INTO artists') !== false
                        && strpos($query, 'VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())') !== false;
                }),
                $this->equalTo([
                    'Claude Monet',
                    'Paris',
                    '1840-11-14',
                    'Painter bio',
                    'monet.jpg',
                    'pending',
                    33,
                ])
            )
            ->willReturn(true);

        $this->assertTrue(Artists::insertArtistProfile($cleanData, $dbMock));
    }

    /**
     * Tests that updating an artist profile binds validated form data and owner id.
     */
    public function testUpdateArtistProfileUsesCleanDataParameters()
    {
        // Update should use the clean profile fields and target the profile by user id.
        $cleanData = $this->artistData();

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->callback(function ($query) {
                    return strpos($query, 'UPDATE artists') !== false
                        && strpos($query, 'WHERE user_id = ?') !== false;
                }),
                $this->equalTo([
                    'Claude Monet',
                    'Paris',
                    '1840-11-14',
                    'Painter bio',
                    'monet.jpg',
                    'pending',
                    33,
                ])
            )
            ->willReturn(true);

        $this->assertTrue(Artists::updateArtistProfile($cleanData, 33, $dbMock));
    }

    /**
     * Tests that pending artist count is cast to an integer.
     */
    public function testCountPendingReturnsIntegerCount()
    {
        // Pending moderation count should normalize database count values to integer.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with($this->equalTo("SELECT COUNT(*) AS cnt FROM artists WHERE status = 'pending'"))
            ->willReturn(['cnt' => '6']);

        $this->assertSame(6, Artists::countPending($dbMock));
    }

    private function artistData()
    {
        return [
            'name' => 'Claude Monet',
            'location' => 'Paris',
            'birth_date' => '1840-11-14',
            'bio' => 'Painter bio',
            'picture' => 'monet.jpg',
            'status' => 'pending',
            'user_id' => 33,
        ];
    }
}

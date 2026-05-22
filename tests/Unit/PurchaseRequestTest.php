<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../models/PurchaseRequest.php';
require_once __DIR__ . '/../../config/Database.php';

class PurchaseRequestTest extends TestCase
{
    /**
     * Tests that create rejects missing user or painting ids.
     */
    public function testCreateRejectsMissingIds()
    {
        // Null ids should return before database access.
        $result = PurchaseRequest::create(null, 5);

        $this->assertFalse($result['success']);
        $this->assertSame('Missing user or painting id', $result['message']);
    }

    /**
     * Tests that create rejects non-positive ids.
     */
    public function testCreateRejectsInvalidIds()
    {
        // Non-positive ids should be rejected after casting to integers.
        $result = PurchaseRequest::create(0, -4);

        $this->assertFalse($result['success']);
        $this->assertSame('Invalid user or painting id', $result['message']);
    }

    /**
     * Tests that successful create inserts a request and returns the new id.
     */
    public function testCreateReturnsSuccessWhenInsertSucceeds()
    {
        // A successful insert should return the last inserted request id.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('INSERT INTO `purchase_requests` (user_id, painting_id) VALUES (?, ?)'),
                $this->equalTo([2, 9])
            )
            ->willReturn(true);
        $dbMock->expects($this->once())
            ->method('getLastInsertId')
            ->willReturn('33');

        $result = PurchaseRequest::create(2, 9, $dbMock);

        $this->assertTrue($result['success']);
        $this->assertSame('Request sent successfully', $result['message']);
        $this->assertSame('33', $result['id']);
    }

    /**
     * Tests that failed create returns an error response.
     */
    public function testCreateReturnsErrorWhenInsertFails()
    {
        // Failed inserts should not request the last insert id.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->willReturn(false);
        $dbMock->expects($this->never())
            ->method('getLastInsertId');

        $result = PurchaseRequest::create(2, 9, $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame('Failed to create request', $result['message']);
    }

    /**
     * Tests that latest request time returns an integer timestamp.
     */
    public function testGetLastRequestTimeReturnsTimestamp()
    {
        // Timestamp strings from SQL should be cast to integers.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->logicalAnd(
                    $this->stringContains('SELECT UNIX_TIMESTAMP(created_at) as created_timestamp FROM `purchase_requests`'),
                    $this->stringContains('WHERE user_id = ? AND painting_id = ?'),
                    $this->stringContains('ORDER BY created_at DESC'),
                    $this->stringContains('LIMIT 1')
                ),
                $this->equalTo([2, 9])
            )
            ->willReturn(['created_timestamp' => '1710000000']);

        $this->assertSame(1710000000, PurchaseRequest::getLastRequestTime(2, 9, $dbMock));
    }

    /**
     * Tests that latest request time returns null when no row exists.
     */
    public function testGetLastRequestTimeReturnsNullWhenMissing()
    {
        // Missing rows should return null instead of zero.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(null);

        $this->assertNull(PurchaseRequest::getLastRequestTime(2, 9, $dbMock));
    }

    /**
     * Tests that getRequestById fetches the joined request details.
     */
    public function testGetRequestByIdUsesJoinedQuery()
    {
        // Request detail lookup should include buyer, painting, artist, and artist email data.
        $row = ['id' => 12, 'user_email' => 'buyer@example.com', 'artist_email' => 'artist@example.com'];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->stringContains('JOIN `paintings` p ON pr.painting_id = p.id'),
                $this->equalTo([12])
            )
            ->willReturn($row);

        $this->assertSame($row, PurchaseRequest::getRequestById(12, $dbMock));
    }

    /**
     * Tests that artist request listing uses artist id with limit and offset.
     */
    public function testGetArtistRequestsUsesLimitOffsetAndArtistId()
    {
        // Limit and offset are cast to integers before being appended to SQL.
        $rows = [['id' => 1], ['id' => 2]];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with(
                $this->logicalAnd(
                    $this->stringContains('WHERE p.artist_id = ?'),
                    $this->stringContains('LIMIT 5 OFFSET 10')
                ),
                $this->equalTo([7])
            )
            ->willReturn($rows);

        $this->assertSame($rows, PurchaseRequest::getArtistRequests(7, 5, 10, $dbMock));
    }

    /**
     * Tests that user request listing uses user id with limit and offset.
     */
    public function testGetUserRequestsUsesLimitOffsetAndUserId()
    {
        // User request listing includes painting and artist display data.
        $rows = [['id' => 3, 'painting_title' => 'Blue']];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getAll')
            ->with(
                $this->logicalAnd(
                    $this->stringContains('WHERE pr.user_id = ?'),
                    $this->stringContains('LIMIT 4 OFFSET 8')
                ),
                $this->equalTo([6])
            )
            ->willReturn($rows);

        $this->assertSame($rows, PurchaseRequest::getUserRequests(6, 4, 8, $dbMock));
    }

    /**
     * Tests that user request count is cast to an integer.
     */
    public function testGetUserRequestsCountReturnsIntegerCount()
    {
        // Count query should bind the user id and cast the cnt field.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT COUNT(*) AS cnt FROM `purchase_requests` WHERE user_id = ?'),
                $this->equalTo([6])
            )
            ->willReturn(['cnt' => '14']);

        $this->assertSame(14, PurchaseRequest::getUserRequestsCount(6, $dbMock));
    }
}

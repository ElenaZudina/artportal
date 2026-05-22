<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/PaintingService.php';
require_once __DIR__ . '/../../models/Artists.php';
require_once __DIR__ . '/../../models/Categories.php';
require_once __DIR__ . '/../../models/Paintings.php';
require_once __DIR__ . '/../../config/Database.php';

class PaintingServicePublicTest extends TestCase
{
    private function validPaintingData(): array
    {
        return [
            'title' => ' New Painting ',
            'description' => ' Painting description ',
            'image' => ' legacy.jpg ',
            'year_created' => '2024',
            'category_id' => 2,
            'medium' => ' Oil ',
            'dimensions' => ' 40x50 ',
            'price' => '125.50',
        ];
    }

    /**
     * Tests that painting creation requires an artist profile.
     */
    public function testCreatePaintingReturnsErrorWhenArtistProfileMissing()
    {
        // A missing artist profile stops creation before validation or database writes.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT * FROM artists WHERE user_id = ?'),
                $this->equalTo([10])
            )
            ->willReturn(null);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = PaintingService::createPainting($this->validPaintingData(), [], 10, $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame(['Artist profile not found'], $result['errors']);
    }

    /**
     * Tests that painting creation requires an image value.
     */
    public function testCreatePaintingReturnsErrorWhenImageMissing()
    {
        // Valid form fields still require either an uploaded, existing, or legacy image.
        $data = $this->validPaintingData();
        $data['image'] = '';

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(2))
            ->method('getOne')
            ->willReturnOnConsecutiveCalls(['id' => 4], ['id' => 2]);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = PaintingService::createPainting($data, [], 10, $dbMock);

        $this->assertFalse($result['success']);
        $this->assertContains('Image is required', $result['errors']);
    }

    /**
     * Tests that painting creation returns validation errors for invalid form data.
     */
    public function testCreatePaintingReturnsValidationErrorsForInvalidFields()
    {
        // Invalid form data should stop before image resolution or painting insert.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(1))
            ->method('getOne')
            ->willReturn(['id' => 4]);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = PaintingService::createPainting([
            'title' => '',
            'description' => '',
            'image' => 'legacy.jpg',
            'year_created' => '20xx',
            'category_id' => 0,
            'medium' => '',
            'dimensions' => '',
            'price' => '-1',
        ], [], 10, $dbMock);

        $this->assertFalse($result['success']);
        $this->assertContains('Title is required', $result['errors']);
        $this->assertContains('Description is required', $result['errors']);
        $this->assertContains('Year must be a 4-digit value', $result['errors']);
        $this->assertContains('Category is required', $result['errors']);
        $this->assertContains('Medium is required', $result['errors']);
        $this->assertContains('Dimensions are required', $result['errors']);
        $this->assertContains('Price must be a valid positive number', $result['errors']);
    }

    /**
     * Tests that painting creation reports insert failures.
     */
    public function testCreatePaintingReturnsErrorWhenInsertFails()
    {
        // A missing insert id should be treated as a database error.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(2))
            ->method('getOne')
            ->willReturnOnConsecutiveCalls(['id' => 4], ['id' => 2]);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->willReturn(false);
        $dbMock->expects($this->once())
            ->method('getLastInsertId')
            ->willReturn(0);

        $result = PaintingService::createPainting($this->validPaintingData(), [], 10, $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame(['Database error while adding painting'], $result['errors']);
    }

    /**
     * Tests that painting creation saves valid data and skips empty AI tags.
     */
    public function testCreatePaintingReturnsSuccessWhenSavedWithoutTags()
    {
        // Valid data is normalized, saved, and returned when AI produces no tags.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(2))
            ->method('getOne')
            ->willReturnOnConsecutiveCalls(['id' => 4], ['id' => 2]);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->anything(),
                $this->equalTo([
                    'New Painting',
                    'Painting description',
                    'legacy.jpg',
                    2024,
                    2,
                    4,
                    'Oil',
                    '40x50',
                    125.5,
                ])
            )
            ->willReturn(true);
        $dbMock->expects($this->once())
            ->method('getLastInsertId')
            ->willReturn(99);

        $visionMock = $this->createMock(VisionAIService::class);
        $visionMock->expects($this->once())
            ->method('detectLabels')
            ->willReturn([]);
        $visionMock->expects($this->once())
            ->method('buildTags')
            ->with($this->equalTo([]))
            ->willReturn([]);

        $result = PaintingService::createPainting($this->validPaintingData(), [], 10, $dbMock, $visionMock);

        $this->assertTrue($result['success']);
        $this->assertSame('New Painting', $result['data']['title']);
        $this->assertSame('legacy.jpg', $result['data']['image']);
        $this->assertSame(4, $result['data']['artist_id']);
    }

    /**
     * Tests that painting updates reject missing paintings.
     */
    public function testUpdatePaintingReturnsErrorWhenPaintingMissing()
    {
        // Updates require both an artist profile and an owned painting row.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(2))
            ->method('getOne')
            ->willReturnOnConsecutiveCalls(['id' => 4], null);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = PaintingService::updatePainting(8, $this->validPaintingData(), [], 10, $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame(['Painting not found'], $result['errors']);
    }

    /**
     * Tests that painting updates require an artist profile.
     */
    public function testUpdatePaintingReturnsErrorWhenArtistProfileMissing()
    {
        // Missing artist profile stops update before loading the painting.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(null);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = PaintingService::updatePainting(8, $this->validPaintingData(), [], 10, $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame(['Artist profile not found'], $result['errors']);
    }

    /**
     * Tests that painting updates reject paintings owned by another artist.
     */
    public function testUpdatePaintingRejectsPaintingFromAnotherArtist()
    {
        // The service must not update paintings owned by a different artist.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(2))
            ->method('getOne')
            ->willReturnOnConsecutiveCalls(['id' => 4], ['id' => 8, 'artist_id' => 99]);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = PaintingService::updatePainting(8, $this->validPaintingData(), [], 10, $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame(['Painting not found'], $result['errors']);
    }

    /**
     * Tests that painting updates save valid data without rebuilding tags when image is unchanged.
     */
    public function testUpdatePaintingReturnsSuccessWhenSavedWithoutImageChange()
    {
        // Keeping the same image avoids AI tag rebuilding and deletes no files.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(3))
            ->method('getOne')
            ->willReturnOnConsecutiveCalls(
                ['id' => 4],
                ['id' => 8, 'artist_id' => 4, 'image' => 'legacy.jpg'],
                ['id' => 2]
            );
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->anything(),
                $this->equalTo([
                    'New Painting',
                    'Painting description',
                    'legacy.jpg',
                    2024,
                    2,
                    'Oil',
                    '40x50',
                    125.5,
                    8,
                    4,
                ])
            )
            ->willReturn(true);

        $visionMock = $this->createMock(VisionAIService::class);
        $visionMock->expects($this->never())
            ->method('detectLabels');

        $result = PaintingService::updatePainting(8, $this->validPaintingData(), [], 10, $dbMock, $visionMock);

        $this->assertTrue($result['success']);
        $this->assertSame('legacy.jpg', $result['data']['image']);
    }

    /**
     * Tests that painting updates report database failures.
     */
    public function testUpdatePaintingReturnsErrorWhenUpdateFails()
    {
        // A failed model update should be reported without rebuilding tags.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(3))
            ->method('getOne')
            ->willReturnOnConsecutiveCalls(
                ['id' => 4],
                ['id' => 8, 'artist_id' => 4, 'image' => 'legacy.jpg'],
                ['id' => 2]
            );
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->willReturn(false);

        $visionMock = $this->createMock(VisionAIService::class);
        $visionMock->expects($this->never())
            ->method('detectLabels');

        $result = PaintingService::updatePainting(8, $this->validPaintingData(), [], 10, $dbMock, $visionMock);

        $this->assertFalse($result['success']);
        $this->assertSame(['Database error while updating painting'], $result['errors']);
    }

    /**
     * Tests that painting deletion requires an artist profile.
     */
    public function testDeletePaintingReturnsErrorWhenArtistProfileMissing()
    {
        // Missing artist profile stops deletion before loading the painting.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(null);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = PaintingService::deletePainting(8, 10, $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame(['Artist profile not found'], $result['errors']);
    }

    /**
     * Tests that painting deletion reports missing paintings.
     */
    public function testDeletePaintingReturnsErrorWhenPaintingMissing()
    {
        // Deletion requires an owned painting row before the delete query runs.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(2))
            ->method('getOne')
            ->willReturnOnConsecutiveCalls(['id' => 4], null);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = PaintingService::deletePainting(8, 10, $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame(['Painting not found'], $result['errors']);
    }

    /**
     * Tests that painting deletion rejects paintings owned by another artist.
     */
    public function testDeletePaintingRejectsPaintingFromAnotherArtist()
    {
        // The service must not delete paintings owned by a different artist.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(2))
            ->method('getOne')
            ->willReturnOnConsecutiveCalls(['id' => 4], ['id' => 8, 'artist_id' => 99]);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = PaintingService::deletePainting(8, 10, $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame(['Painting not found'], $result['errors']);
    }

    /**
     * Tests that painting deletion reports database failures.
     */
    public function testDeletePaintingReturnsErrorWhenDeleteFails()
    {
        // A failed delete should return a database error.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(2))
            ->method('getOne')
            ->willReturnOnConsecutiveCalls(['id' => 4], ['id' => 8, 'artist_id' => 4, 'image' => 'legacy.jpg']);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->willReturn(false);

        $result = PaintingService::deletePainting(8, 10, $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame(['Database error while deleting painting'], $result['errors']);
    }

    /**
     * Tests that painting deletion returns success when the model deletes the row.
     */
    public function testDeletePaintingReturnsSuccessWhenDeleted()
    {
        // A successful owned delete should return an empty error list.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(2))
            ->method('getOne')
            ->willReturnOnConsecutiveCalls(['id' => 4], ['id' => 8, 'artist_id' => 4, 'image' => 'legacy.jpg']);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('DELETE FROM paintings WHERE id = ? AND artist_id = ?'),
                $this->equalTo([8, 4])
            )
            ->willReturn(true);

        $result = PaintingService::deletePainting(8, 10, $dbMock);

        $this->assertTrue($result['success']);
        $this->assertSame([], $result['errors']);
    }
}

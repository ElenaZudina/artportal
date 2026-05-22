<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/ArtistProfileService.php';
require_once __DIR__ . '/../../models/Artists.php';
require_once __DIR__ . '/../../config/Database.php';

class ArtistProfileServicePublicTest extends TestCase
{
    private function validProfileData(): array
    {
        return [
            'name' => ' Marina Chen ',
            'location' => ' Berlin ',
            'birth_date' => '1990-05-10',
            'bio' => ' Painter bio ',
            'picture' => ' artist.jpg ',
        ];
    }

    /**
     * Tests that profile creation rejects users that already have an artist profile.
     */
    public function testCreateProfileRejectsExistingArtistProfile()
    {
        // Existing artist profiles should block duplicate profile creation.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT * FROM artists WHERE user_id = ?'),
                $this->equalTo([10])
            )
            ->willReturn(['id' => 3, 'user_id' => 10]);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = ArtistProfileService::createProfile($this->validProfileData(), [], 10, $dbMock);

        $this->assertFalse($result['success']);
        $this->assertContains('Artist profile already exists for this user', $result['errors']);
    }

    /**
     * Tests that profile creation reports database failures.
     */
    public function testCreateProfileReturnsErrorWhenInsertFails()
    {
        // Valid data should surface a failed insert operation.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(null);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->willReturn(false);

        $result = ArtistProfileService::createProfile($this->validProfileData(), [], 10, $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame(['Database error: Unable to create artist profile'], $result['errors']);
    }

    /**
     * Tests that profile creation normalizes and saves valid data.
     */
    public function testCreateProfileReturnsSuccessWhenSaved()
    {
        // Valid profile data is trimmed and nullable fields are normalized before saving.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(null);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->anything(),
                $this->equalTo([
                    'Marina Chen',
                    'Berlin',
                    '1990-05-10',
                    'Painter bio',
                    'artist.jpg',
                    'pending',
                    10,
                ])
            )
            ->willReturn(true);

        $result = ArtistProfileService::createProfile($this->validProfileData(), [], 10, $dbMock);

        $this->assertTrue($result['success']);
        $this->assertSame('Marina Chen', $result['data']['name']);
        $this->assertSame('Berlin', $result['data']['location']);
        $this->assertSame('pending', $result['data']['status']);
        $this->assertSame(10, $result['data']['user_id']);
    }

    /**
     * Tests that profile updates reject missing artist profiles.
     */
    public function testUpdateProfileReturnsErrorWhenProfileIsMissing()
    {
        // Updates require an existing artist profile for the user.
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

        $result = ArtistProfileService::updateProfile($this->validProfileData(), [], 10, $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame(['Artist profile not found'], $result['errors']);
    }

    /**
     * Tests that profile updates preserve existing picture and status when saved.
     */
    public function testUpdateProfileReturnsSuccessWhenSaved()
    {
        // Existing picture and moderation status are kept when no new upload is provided.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn([
                'id' => 3,
                'user_id' => 10,
                'picture' => 'existing.jpg',
                'status' => 'approved',
            ]);
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->anything(),
                $this->equalTo([
                    'Marina Chen',
                    'Berlin',
                    '1990-05-10',
                    'Painter bio',
                    'existing.jpg',
                    'approved',
                    10,
                ])
            )
            ->willReturn(true);

        $result = ArtistProfileService::updateProfile($this->validProfileData(), [], 10, $dbMock);

        $this->assertTrue($result['success']);
        $this->assertSame('existing.jpg', $result['data']['picture']);
        $this->assertSame('approved', $result['data']['status']);
    }
}

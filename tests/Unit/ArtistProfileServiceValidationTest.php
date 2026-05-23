<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/ArtistProfileService.php';
require_once __DIR__ . '/../../models/Artists.php';
require_once __DIR__ . '/../../config/Database.php';

class ArtistProfileServiceValidationTest extends TestCase
{
    private ReflectionMethod $resolvePictureValue;

    /**
     * Prepares reflection access to private artist profile upload resolution.
     */
    protected function setUp(): void
    {
        // Reflection gives direct access to upload resolution without changing service visibility.
        $ref = new ReflectionClass(ArtistProfileService::class);
        $this->resolvePictureValue = $ref->getMethod('resolvePictureValue');
        $this->resolvePictureValue->setAccessible(true);
    }

    /**
     * Tests that profile creation rejects empty form data.
     */
    public function testCreateProfileReturnsErrorWhenNoDataProvided()
    {
        // Empty input returns before artist model/database access.
        $result = ArtistProfileService::createProfile([], [], 10);

        $this->assertFalse($result['success']);
        $this->assertSame(['No data provided'], $result['errors']);
    }

    /**
     * Tests that profile update rejects empty form data.
     */
    public function testUpdateProfileReturnsErrorWhenNoDataProvided()
    {
        // Empty input returns before loading an existing artist profile.
        $result = ArtistProfileService::updateProfile([], [], 10);

        $this->assertFalse($result['success']);
        $this->assertSame(['No data provided'], $result['errors']);
    }

    /**
     * Tests that profile creation returns validation errors for invalid fields.
     */
    public function testCreateProfileReturnsValidationErrorsForInvalidFields()
    {
        // Invalid form values should stop before inserting an artist profile.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(null);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = ArtistProfileService::createProfile([
            'name' => '',
            'location' => '',
            'birth_date' => '10-05-1990',
            'bio' => str_repeat('a', 65536),
            'picture' => '',
        ], [], 10, $dbMock);

        $this->assertFalse($result['success']);
        $this->assertContains('Name is required', $result['errors']);
        $this->assertContains('Location is required', $result['errors']);
        $this->assertContains('Birth date must be in YYYY-MM-DD format', $result['errors']);
        $this->assertContains('Bio is too long', $result['errors']);
    }

    /**
     * Tests that profile creation rejects values longer than database limits.
     */
    public function testCreateProfileReturnsValidationErrorsForTooLongNameAndLocation()
    {
        // Length validation should report field-specific errors before insert.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(null);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = ArtistProfileService::createProfile([
            'name' => str_repeat('a', 256),
            'location' => str_repeat('b', 101),
            'birth_date' => '',
            'bio' => '',
            'picture' => '',
        ], [], 10, $dbMock);

        $this->assertFalse($result['success']);
        $this->assertContains('Name is too long', $result['errors']);
        $this->assertContains('Location is too long', $result['errors']);
    }

    /**
     * Tests that profile creation rejects too long legacy picture filenames.
     */
    public function testCreateProfileReturnsValidationErrorForTooLongPictureFilename()
    {
        // Legacy picture names are still bounded before saving to the database.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(null);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = ArtistProfileService::createProfile([
            'name' => 'Marina Chen',
            'location' => 'Berlin',
            'birth_date' => '',
            'bio' => '',
            'picture' => str_repeat('p', 256),
        ], [], 10, $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame(['Picture filename is too long'], $result['errors']);
    }

    /**
     * Tests that profile updates return validation errors for invalid fields.
     */
    public function testUpdateProfileReturnsValidationErrorsForInvalidFields()
    {
        // Existing profile is loaded first, then invalid input prevents update.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn([
                'id' => 3,
                'user_id' => 10,
                'picture' => 'existing.jpg',
                'status' => 'approved',
            ]);
        $dbMock->expects($this->never())
            ->method('executeRun');

        $result = ArtistProfileService::updateProfile([
            'name' => '',
            'location' => '',
            'birth_date' => '1990/05/10',
            'bio' => str_repeat('a', 65536),
            'picture' => '',
        ], [], 10, $dbMock);

        $this->assertFalse($result['success']);
        $this->assertContains('Name is required', $result['errors']);
        $this->assertContains('Location is required', $result['errors']);
        $this->assertContains('Birth date must be in YYYY-MM-DD format', $result['errors']);
        $this->assertContains('Bio is too long', $result['errors']);
    }

    /**
     * Tests that profile updates report database failures.
     */
    public function testUpdateProfileReturnsErrorWhenUpdateFails()
    {
        // Valid update data should surface a failed database update operation.
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
            ->willReturn(false);

        $result = ArtistProfileService::updateProfile([
            'name' => 'Marina Chen',
            'location' => 'Berlin',
            'birth_date' => '1990-05-10',
            'bio' => 'Painter bio',
            'picture' => '',
        ], [], 10, $dbMock);

        $this->assertFalse($result['success']);
        $this->assertSame(['Database error: Unable to update artist profile'], $result['errors']);
    }

    /**
     * Tests that existing artist picture is reused when no file is uploaded.
     */
    public function testResolvePictureValueKeepsExistingPictureWhenNoUpload()
    {
        // Existing picture is reused when no new file is uploaded.
        $errors = [];

        $picture = $this->resolvePictureValue->invokeArgs(null, [
            [],
            [],
            'artist.jpg',
            &$errors,
        ]);

        $this->assertSame('artist.jpg', $picture);
        $this->assertSame([], $errors);
    }

    /**
     * Tests that legacy picture field is used when no upload or existing picture is present.
     */
    public function testResolvePictureValueUsesLegacyPictureWhenNoUploadOrExistingPicture()
    {
        // Legacy picture field remains supported for form data without file uploads.
        $errors = [];

        $picture = $this->resolvePictureValue->invokeArgs(null, [
            ['picture' => ' legacy-artist.jpg '],
            [],
            null,
            &$errors,
        ]);

        $this->assertSame('legacy-artist.jpg', $picture);
        $this->assertSame([], $errors);
    }

    /**
     * Tests that oversized profile picture uploads are rejected.
     */
    public function testResolvePictureValueRejectsOversizedUpload()
    {
        // Size validation runs before HTTP-upload validation, making the branch deterministic in unit tests.
        $errors = [];

        $picture = $this->resolvePictureValue->invokeArgs(null, [
            [],
            [
                'picture_file' => [
                    'error' => UPLOAD_ERR_OK,
                    'tmp_name' => __FILE__,
                    'name' => 'artist.jpg',
                    'size' => 5242881,
                ],
            ],
            'existing.jpg',
            &$errors,
        ]);

        $this->assertSame('existing.jpg', $picture);
        $this->assertSame(['Uploaded picture must not exceed 5 MB'], $errors);
    }
}

<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/ArtistProfileService.php';

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
}

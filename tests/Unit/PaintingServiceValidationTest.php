<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/PaintingService.php';
require_once __DIR__ . '/../../models/Categories.php';
require_once __DIR__ . '/../../config/Database.php';

class PaintingServiceValidationTest extends TestCase
{
    private ReflectionMethod $validateCommonData;
    private ReflectionMethod $resolveImageValue;
    private ReflectionMethod $rebuildTagsForPainting;

    /**
     * Prepares reflection access to private painting validation helpers.
     */
    protected function setUp(): void
    {
        // Reflection lets these tests target private validation helpers without production refactoring.
        $ref = new ReflectionClass(PaintingService::class);
        $this->validateCommonData = $ref->getMethod('validateCommonData');
        $this->validateCommonData->setAccessible(true);
        $this->resolveImageValue = $ref->getMethod('resolveImageValue');
        $this->resolveImageValue->setAccessible(true);
        $this->rebuildTagsForPainting = $ref->getMethod('rebuildTagsForPainting');
        $this->rebuildTagsForPainting->setAccessible(true);
    }

    /**
     * Tests that common painting validation collects required field errors.
     */
    public function testValidateCommonDataCollectsRequiredFieldErrors()
    {
        // Category id stays zero so no database-backed category lookup is reached.
        $normalized = [];
        $errors = [];

        $this->validateCommonData->invokeArgs(null, [
            [
                'title' => '',
                'description' => '',
                'year_created' => '20xx',
                'category_id' => 0,
                'medium' => '',
                'dimensions' => '',
                'price' => '-1',
            ],
            &$normalized,
            &$errors,
        ]);

        $this->assertContains('Title is required', $errors);
        $this->assertContains('Description is required', $errors);
        $this->assertContains('Year must be a 4-digit value', $errors);
        $this->assertContains('Category is required', $errors);
        $this->assertContains('Medium is required', $errors);
        $this->assertContains('Dimensions are required', $errors);
        $this->assertContains('Price must be a valid positive number', $errors);
    }

    /**
     * Tests that common painting validation normalizes scalar form fields.
     */
    public function testValidateCommonDataNormalizesScalarFields()
    {
        // Normalization can be tested while still avoiding database lookup through category_id zero.
        $normalized = [];
        $errors = [];

        $this->validateCommonData->invokeArgs(null, [
            [
                'title' => '  Large Title  ',
                'description' => '  Description  ',
                'year_created' => '2024',
                'category_id' => 0,
                'medium' => '  Oil  ',
                'dimensions' => '  10x20  ',
                'price' => '  99.50  ',
            ],
            &$normalized,
            &$errors,
        ]);

        $this->assertSame('Large Title', $normalized['title']);
        $this->assertSame('Description', $normalized['description']);
        $this->assertSame('2024', $normalized['year_created']);
        $this->assertSame('Oil', $normalized['medium']);
        $this->assertSame('10x20', $normalized['dimensions']);
        $this->assertSame('99.50', $normalized['price']);
    }

    /**
     * Tests that existing painting image is reused when no file is uploaded.
     */
    public function testResolveImageValueKeepsExistingImageWhenNoUpload()
    {
        // Existing image wins when no new upload is provided.
        $errors = [];
        $fileHash = null;

        $image = $this->resolveImageValue->invokeArgs(null, [
            [],
            [],
            'existing.jpg',
            &$errors,
            &$fileHash,
        ]);

        $this->assertSame('existing.jpg', $image);
        $this->assertSame([], $errors);
        $this->assertNull($fileHash);
    }

    /**
     * Tests that legacy image field is used when no upload or existing image is present.
     */
    public function testResolveImageValueUsesLegacyImageWhenNoUploadOrExistingImage()
    {
        // Legacy image field keeps old form behavior testable without uploaded files.
        $errors = [];
        $fileHash = null;

        $image = $this->resolveImageValue->invokeArgs(null, [
            ['image' => ' legacy.jpg '],
            [],
            null,
            &$errors,
            &$fileHash,
        ]);

        $this->assertSame('legacy.jpg', $image);
        $this->assertSame([], $errors);
        $this->assertNull($fileHash);
    }

    /**
     * Tests that invalid upload metadata is rejected before saving a file.
     */
    public function testResolveImageValueRejectsInvalidUploadedFile()
    {
        // CLI tests cannot create a real HTTP upload, so this covers the safe invalid-upload branch.
        $errors = [];
        $fileHash = null;

        $image = $this->resolveImageValue->invokeArgs(null, [
            [],
            [
                'image_file' => [
                    'error' => UPLOAD_ERR_OK,
                    'tmp_name' => __FILE__,
                    'name' => 'painting.jpg',
                    'size' => 123,
                ],
            ],
            'existing.jpg',
            &$errors,
            &$fileHash,
        ]);

        $this->assertSame('existing.jpg', $image);
        $this->assertSame(['Uploaded image is invalid'], $errors);
        $this->assertNull($fileHash);
    }

    /**
     * Tests that oversized painting image uploads are rejected.
     */
    public function testResolveImageValueRejectsOversizedUpload()
    {
        // Size validation runs before HTTP-upload validation, making the branch deterministic in unit tests.
        $errors = [];
        $fileHash = null;

        $image = $this->resolveImageValue->invokeArgs(null, [
            [],
            [
                'image_file' => [
                    'error' => UPLOAD_ERR_OK,
                    'tmp_name' => __FILE__,
                    'name' => 'painting.jpg',
                    'size' => 5242881,
                ],
            ],
            'existing.jpg',
            &$errors,
            &$fileHash,
        ]);

        $this->assertSame('existing.jpg', $image);
        $this->assertSame(['Uploaded image must not exceed 5 MB'], $errors);
        $this->assertNull($fileHash);
    }

    /**
     * Tests that overlong scalar fields are rejected by common validation.
     */
    public function testValidateCommonDataCollectsLengthErrors()
    {
        // Long title, medium, and dimensions should produce length-specific errors.
        $normalized = [];
        $errors = [];

        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->once())
            ->method('getOne')
            ->willReturn(['id' => 2]);

        $this->validateCommonData->invokeArgs(null, [
            [
                'title' => str_repeat('t', 256),
                'description' => 'Description',
                'year_created' => '2024',
                'category_id' => 2,
                'medium' => str_repeat('m', 256),
                'dimensions' => str_repeat('d', 101),
                'price' => '10',
            ],
            &$normalized,
            &$errors,
            $dbMock,
        ]);

        $this->assertContains('Title is too long', $errors);
        $this->assertContains('Medium is too long', $errors);
        $this->assertContains('Dimensions are too long', $errors);
    }

    /**
     * Tests that AI tag rebuilding detaches old tags and attaches new ones.
     */
    public function testRebuildTagsForPaintingAttachesDetectedTags()
    {
        // Reflection targets tag rebuilding without requiring a real uploaded image.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->exactly(1))
            ->method('getOne')
            ->with(
                $this->equalTo('SELECT id FROM tags WHERE name = ?'),
                $this->equalTo(['blue'])
            )
            ->willReturn(null);
        $dbMock->expects($this->exactly(3))
            ->method('executeRun')
            ->willReturn(true);
        $dbMock->expects($this->once())
            ->method('getLastInsertId')
            ->willReturn(12);

        $visionMock = $this->createMock(VisionAIService::class);
        $visionMock->expects($this->once())
            ->method('detectLabels')
            ->with($this->equalTo('/tmp/painting.jpg'))
            ->willReturn(['labels' => []]);
        $visionMock->expects($this->once())
            ->method('buildTags')
            ->with($this->equalTo(['labels' => []]))
            ->willReturn(['blue']);

        $this->rebuildTagsForPainting->invoke(null, 8, '/tmp/painting.jpg', $visionMock, $dbMock);
    }

    /**
     * Tests that AI tag rebuilding stops after detaching when no tags are detected.
     */
    public function testRebuildTagsForPaintingStopsWhenNoTagsDetected()
    {
        // Empty AI tags should detach old links and skip tag creation.
        $dbMock = $this->createMock(Database::class);
        $dbMock->expects($this->never())
            ->method('getOne');
        $dbMock->expects($this->once())
            ->method('executeRun')
            ->with(
                $this->equalTo('DELETE FROM painting_tags WHERE painting_id = ?'),
                $this->equalTo([8])
            )
            ->willReturn(true);

        $visionMock = $this->createMock(VisionAIService::class);
        $visionMock->expects($this->once())
            ->method('detectLabels')
            ->willReturn([]);
        $visionMock->expects($this->once())
            ->method('buildTags')
            ->with($this->equalTo([]))
            ->willReturn([]);

        $this->rebuildTagsForPainting->invoke(null, 8, '/tmp/painting.jpg', $visionMock, $dbMock);
    }
}

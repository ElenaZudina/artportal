<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/PaintingService.php';

class PaintingServiceValidationTest extends TestCase
{
    private ReflectionMethod $validateCommonData;
    private ReflectionMethod $resolveImageValue;

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
}

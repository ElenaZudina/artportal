<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/VisionAIService.php';

class VisionAIServiceTest extends TestCase
{
    public function testBuildTagsFiltersAndSplitsLabelAnnotations()
    {
        $response = [
            'labelAnnotations' => [
                ['description' => 'Red Apple', 'score' => 0.8],
                ['description' => 'the Art', 'score' => 0.9],
                ['description' => 'ok', 'score' => 0.9],
                ['description' => 'Acrylic Painting', 'score' => 0.76],
                ['description' => 'blur', 'score' => 0.7],
            ],
            'webDetection' => []
        ];

        $ref = new ReflectionClass(VisionAIService::class);
        $svc = $ref->newInstanceWithoutConstructor();

        $tags = $svc->buildTags($response);

        // Expected: 'red', 'apple', 'acrylic', 'painting' (lowercased, >=3 chars), 'blur' may be < threshold if score < 0.75
        $this->assertContains('red', $tags);
        $this->assertContains('apple', $tags);
        // 'the' and 'art' are in ignore list -> not present
        $this->assertNotContains('the', $tags);
        $this->assertNotContains('art', $tags);
        // 'ok' is too short -> not present
        $this->assertNotContains('ok', $tags);
    }

    public function testBuildTagsMergesWebAndLabelEntitiesUnique()
    {
        $response = [
            'labelAnnotations' => [
                ['description' => 'Sunset Beach', 'score' => 0.95],
            ],
            'webDetection' => [
                'webEntities' => [
                    ['description' => 'Beach Sunset', 'score' => 0.6],
                    ['description' => 'Landscape', 'score' => 0.7],
                    ['description' => 'art', 'score' => 0.9]
                ]
            ]
        ];

        $ref = new ReflectionClass(VisionAIService::class);
        $svc = $ref->newInstanceWithoutConstructor();

        $tags = $svc->buildTags($response);

        // Expect unique tags: sunset, beach, landscape (art ignored)
        $this->assertEqualsCanonicalizing(['sunset', 'beach', 'landscape'], $tags);
    }
}

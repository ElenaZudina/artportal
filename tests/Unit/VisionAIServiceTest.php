<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../services/VisionAIService.php';

class VisionAIServiceTest extends TestCase
{
    /**
     * Tests that Vision labels are split, normalized, and filtered.
     */
    public function testBuildTagsFiltersAndSplitsLabelAnnotations()
    {
        // Labels are lowercased, split into words, and filtered by score and ignore list.
        $response = [
            'labelAnnotations' => [
                ['description' => 'Red Apple', 'score' => 0.8],
                ['description' => 'the Art', 'score' => 0.9],
                ['description' => 'ok', 'score' => 0.9],
                ['description' => 'Acrylic Painting', 'score' => 0.76],
                ['description' => 'blur', 'score' => 0.7],
                ['description' => 'Artwork Image Technique', 'score' => 0.95],
            ],
            'webDetection' => []
        ];

        $ref = new ReflectionClass(VisionAIService::class);
        // The constructor needs an API key, so this test instantiates without running it.
        $svc = $ref->newInstanceWithoutConstructor();

        $tags = $svc->buildTags($response);

        // Expected words are lowercased, split, and filtered by score and ignore list.
        $this->assertContains('red', $tags);
        $this->assertContains('apple', $tags);
        $this->assertContains('blur', $tags);
        $this->assertNotContains('the', $tags);
        $this->assertNotContains('art', $tags);
        $this->assertNotContains('acrylic', $tags);
        $this->assertNotContains('painting', $tags);
        $this->assertNotContains('artwork', $tags);
        $this->assertNotContains('image', $tags);
        $this->assertNotContains('technique', $tags);
        // 'ok' is too short -> not present
        $this->assertNotContains('ok', $tags);
    }

    /**
     * Tests that Vision labels and web entities are merged into unique tags.
     */
    public function testBuildTagsMergesWebAndLabelEntitiesUnique()
    {
        // Web entities and labels are merged into one unique tag list.
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
            ],
            'localizedObjectAnnotations' => [
                ['name' => 'Person', 'score' => 0.82],
                ['name' => 'Castle Tower', 'score' => 0.74],
                ['name' => 'Low Confidence Object', 'score' => 0.3],
            ]
        ];

        $ref = new ReflectionClass(VisionAIService::class);
        // The tested method is pure, so no API key or network call is needed.
        $svc = $ref->newInstanceWithoutConstructor();

        $tags = $svc->buildTags($response);

        // Expect unique tags from labels, web entities, and localized objects.
        $this->assertEqualsCanonicalizing(['sunset', 'beach', 'landscape', 'person', 'castle', 'tower'], $tags);
    }
}

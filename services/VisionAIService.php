<?php
/**
 * Vision AI Service - integrates with Google Vision API
 * Analyzes images and detects content using machine learning
 */
class VisionAIService {

    private const LABEL_SCORE_THRESHOLD = 0.6;
    private const WEB_ENTITY_SCORE_THRESHOLD = 0.4;
    private const OBJECT_SCORE_THRESHOLD = 0.4;

    private string $apiKey;

    /**
     * Initialize the service with the Google Vision API key from the environment.
     */
    public function __construct() {
        $this->apiKey = $_ENV['GOOGLE_VISION_API_KEY'];
    }

    /**
     * Detect labels and web entities for an image using Google Vision API.
     * @param string $imagePath Absolute or relative image path
     * @return array Vision API response data for the image
     */
    public function detectLabels(string $imagePath): array {

        $imageData = base64_encode(file_get_contents($imagePath));

        $url = "https://vision.googleapis.com/v1/images:annotate?key=" . $this->apiKey;

        $request = [
            "requests" => [[
                "image" => [
                    "content" => $imageData
                ],
                "features" => [[
                    "type" => "LABEL_DETECTION",
                    "maxResults" => 10
                ],
                ["type" => "WEB_DETECTION", "maxResults" => 10],
                ["type" => "OBJECT_LOCALIZATION", "maxResults" => 10]]
            ]]
        ];

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($request)
        ]);

        $response = curl_exec($ch);

        curl_close($ch);

        $result = json_decode($response, true);

        return $result['responses'][0] ?? [];
    }

    /**
     * Build searchable tag names from Vision API labels and web entities.
     * Filters low-confidence results and common stop words.
     * @param array $response Vision API response data
     * @return array Unique normalized tag names
     */
    public function buildTags(array $response): array
{
    $labels = $response['labelAnnotations'] ?? [];
    $web = $response['webDetection']['webEntities'] ?? [];
    $objects = $response['localizedObjectAnnotations'] ?? [];

    $ignore = [
        'art',
        'paint',
        'arts',
        'acrylic',
        'watercolor',
        'painting',
        'artwork',
        'image',
        'visual',
        'technique',
        'illustration',
        'drawing',
        'modern',
        'institute',
        'chicago',
        'the',
        'a',
        'an',
        'and',
        'or',
        'for',
        'of',
        'in',
        'on',
        'with',
        'to',
        'is',
        'at',
    ];

    $tags = [];

    // Process label annotations.
    foreach ($labels as $item) {
        $tagString = strtolower($item['description'] ?? '');

        if (!$tagString) continue;
        if (($item['score'] ?? 0) < self::LABEL_SCORE_THRESHOLD) continue;
        $words = explode(' ', $tagString);

        foreach ($words as $word) {
            $word = trim($word);

            if (!$word) continue;
            if (strlen($word) < 3) continue;
            if (in_array($word, $ignore)) continue;


        $tags[] = $word;
    }
    }

    // Process web entities.
    foreach ($web as $item) {
        $tagString = strtolower($item['description'] ?? '');

        if (!$tagString) continue;
        if (($item['score'] ?? 0) < self::WEB_ENTITY_SCORE_THRESHOLD) continue;
         $words = explode(' ', $tagString);

        foreach ($words as $word) {
            $word = trim($word);

            if (!$word) continue;
            if (strlen($word) < 3) continue;
            if (in_array($word, $ignore)) continue;

        $tags[] = $word;
    }
    }

    // Process localized objects.
    foreach ($objects as $item) {
        $tagString = strtolower($item['name'] ?? '');

        if (!$tagString) continue;
        if (($item['score'] ?? 0) < self::OBJECT_SCORE_THRESHOLD) continue;
        $words = explode(' ', $tagString);

        foreach ($words as $word) {
            $word = trim($word);

            if (!$word) continue;
            if (strlen($word) < 3) continue;
            if (in_array($word, $ignore)) continue;

        $tags[] = $word;
    }
    }

    return array_values(array_unique($tags));
}
}

<?php
class VisionAIService {

    private string $apiKey;

    public function __construct() {
        $this->apiKey = $_ENV['GOOGLE_VISION_API_KEY'];
    }

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
                ["type" => "WEB_DETECTION", "maxResults" => 10]]
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

    public function buildTags(array $response): array
{
    $labels = $response['labelAnnotations'] ?? [];
    $web = $response['webDetection']['webEntities'] ?? [];

    $ignore = [
        'art',
        'paint',
        // 'visual',
        'arts',
        'acrylic',
        'paint',
        'watercolor',
        'painting',
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

    // LABELS
    foreach ($labels as $item) {
        $tagString = strtolower($item['description'] ?? '');

        if (!$tagString) continue;
        if (($item['score'] ?? 0) < 0.75) continue;

        $words = explode(' ', $tagString);

        foreach ($words as $word) {
            $word = trim($word);

            if (!$word) continue;
            if (strlen($word) < 3) continue;
            if (in_array($word, $ignore)) continue;


        $tags[] = $word;
    }
    }

    // WEB
    foreach ($web as $item) {
        $tagString = strtolower($item['description'] ?? '');

        if (!$tagString) continue;
        if (($item['score'] ?? 0) < 0.5) continue;

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
<?php

require_once __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$imagePath = __DIR__ . '/../images/test.jpg';
$apiKey = $_ENV['GOOGLE_VISION_API_KEY'];

$imageData = base64_encode(file_get_contents($imagePath));

$url = "https://vision.googleapis.com/v1/images:annotate?key=" . $apiKey;

$request = [
    "requests" => [
        [
            "image" => [
                "content" => $imageData
            ],
            "features" => [
                [
                    "type" => "LABEL_DETECTION",
                    "maxResults" => 10
                ]
            ]
        ]
    ]
];

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));

$response = curl_exec($ch);
curl_close($ch);

echo "<pre>";
print_r(json_decode($response, true));
echo "</pre>";
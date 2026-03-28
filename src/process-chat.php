<?php
// src/process-chat.php
session_start();
require_once 'config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['response' => 'Please login to chat with me.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$user_message = $input['message'] ?? '';

if (empty($user_message)) {
    echo json_encode(['response' => 'I didn\'t catch that. Could you repeat?']);
    exit;
}

// Get API Key from environment
$api_key = getenv('GEMINI_API_KEY') ?: ($_ENV['GEMINI_API_KEY'] ?? null);

if (!$api_key || empty($api_key) || $api_key === 'your_gemini_api_key_here') {
    echo json_encode(['response' => 'GEPO AI is currently resting. (Error: API Key not found in .env)']);
    exit;
}

// System Identity
$system_identity = "You are GEPO, the friendly AI assistant for UCLM GearLoop (UCLM Campus Marketplace). Your goal is to help students with academic resources. Always be helpful and professional and identify as GEPO.";

// Applying the "Solution Found": Use v1beta with gemini-flash-latest
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=" . trim($api_key);

$data = [
    "contents" => [
        [
            "parts" => [
                ["text" => $system_identity . "\n\nUser Question: " . $user_message]
            ]
        ]
    ],
    "generationConfig" => [
        "temperature" => 1,
        "maxOutputTokens" => 800,
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200) {
    $result = json_decode($response, true);
    $ai_response = $result['candidates'][0]['content']['parts'][0]['text'] ?? "I'm not sure how to answer that right now.";
    echo json_encode(['response' => $ai_response]);
} else {
    $error_data = json_decode($response, true);
    $detailed_message = $error_data['error']['message'] ?? 'No detailed message provided by Google.';
    echo json_encode(['response' => "GEPO AI Error ($http_code): " . $detailed_message]);
}
?>

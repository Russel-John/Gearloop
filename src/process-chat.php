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

// Fetch available items to "train" the AI on current data
$stmt = $pdo->prepare("SELECT title, description, category, item_condition, price, department, tag FROM items WHERE status = 'Available' ORDER BY created_at DESC LIMIT 30");
$stmt->execute();
$available_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$items_context = "LIST OF AVAILABLE ITEMS ON GEARLOOP:\n";
if (empty($available_items)) {
    $items_context .= "(No items are currently listed in the marketplace.)";
} else {
    foreach ($available_items as $item) {
        $price_info = ($item['tag'] == 'For Swap') ? "For Swap only" : "Price: PHP " . number_format($item['price'], 2);
        $items_context .= "- ITEM: {$item['title']}\n  Category: {$item['category']} | Dept: {$item['department']} | Condition: {$item['item_condition']}\n  Type: {$item['tag']} | {$price_info}\n  Description: {$item['description']}\n\n";
    }
}

// System Identity / Training Data
$system_identity = "You are GEPO, the friendly AI assistant for UCLM GearLoop (UCLM Campus Marketplace). Your goal is to help students with academic resources. 

CRITICAL KNOWLEDGE:
$items_context

YOUR INSTRUCTIONS:
1. If a user describes an item they are looking for (even if they don't know the name), scan the 'LIST OF AVAILABLE ITEMS' above and suggest the best matches.
2. Provide specific details like the Item Name, Category, and Price/Type from the list.
3. If no item matches their description, politely inform them and suggest they list a 'Request' or check again later.
4. Keep responses helpful, professional, and UCLM-focused.
5. Identify as GEPO always.";

// Applying the "Solution Found": Use v1beta with gemini-flash-latest
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=" . trim($api_key);

$data = [
    "system_instruction" => [
        "parts" => [
            ["text" => $system_identity]
        ]
    ],
    "contents" => [
        [
            "parts" => [
                ["text" => $user_message]
            ]
        ]
    ],
    "generationConfig" => [
        "temperature" => 0.7, // Lowered for more precise matching
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

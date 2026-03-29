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

// Fetch current user details to personalize recommendations
$stmt_user = $pdo->prepare("SELECT username, department, full_name FROM users WHERE id = ?");
$stmt_user->execute([$_SESSION['user_id']]);
$user_info = $stmt_user->fetch();

$user_display_name = $user_info['full_name'] ?: $user_info['username'];
$user_dept = $user_info['department'];

// Fetch available items to "train" the AI on current data
$stmt = $pdo->prepare("SELECT title, description, category, item_condition, price, department, tag FROM items WHERE status = 'Available' ORDER BY created_at DESC LIMIT 30");
$stmt->execute();
$available_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$items_context = "CURRENT MARKETPLACE INVENTORY:\n";
if (empty($available_items)) {
    $items_context .= "(No items are currently listed.)";
} else {
    foreach ($available_items as $item) {
        $price_info = ($item['tag'] == 'For Swap') ? "For Swap only" : "Price: PHP " . number_format($item['price'], 2);
        $items_context .= "- {$item['title']} | Dept: {$item['department']} | Category: {$item['category']} | Condition: {$item['item_condition']} | {$price_info}\n  Description: {$item['description']}\n\n";
    }
}

// System Identity
$system_identity = "You are GEPO, the friendly AI assistant for UCLM GearLoop. 

USER CONTEXT:
- Name: $user_display_name
- Department: $user_dept

YOUR EXCLUSIVE KNOWLEDGE BASE:
$items_context

YOUR RULES:
1. You only have knowledge of the items listed in the 'CURRENT MARKETPLACE INVENTORY' section above. 
2. ONLY recommend or list specific items if the user explicitly asks for recommendations, searches for a product, or inquires about what's available.
3. If the user's message is a general greeting, a question about how GearLoop works, or unrelated to searching/buying, do NOT list any items. Instead, engage in friendly conversation about UCLM GearLoop or SDG 12 (Responsible Consumption).
4. When a user DOES ask for recommendations or searches for items, ALWAYS prioritize recommending items that belong to the user's department ($user_dept) first and explicitly mention that you found items from their department.
5. Describe any matches in plain text naturally. Do NOT use special tags like [ITEM:ID].
6. If a specific item requested isn't in the inventory, politely say it's not currently available and suggest they check back later.
7. Always identify as GEPO and be supportive of SDG 12.";

// Applying the "Solution Found": Use v1beta with gemini-flash-latest
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=" . trim($api_key);

$data = [
    "system_instruction" => [
        "parts" => [["text" => $system_identity]]
    ],
    "contents" => [
        [
            "parts" => [["text" => $user_message]]
        ]
    ],
    "generationConfig" => [
        "temperature" => 0.7,
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

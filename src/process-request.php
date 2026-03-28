<?php
// src/process-request.php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

// Support both GET (for Buy) and POST (for Trade with offer)
$item_id = $_REQUEST['id'] ?? null;
$type = $_REQUEST['type'] ?? null;
$trade_offer = $_POST['trade_offer'] ?? null;
$buyer_id = $_SESSION['user_id'];

if (!$item_id || !$type) {
    header("Location: dashboard.php");
    exit;
}

// Fetch item details to get seller_id
$stmt = $pdo->prepare("SELECT user_id FROM items WHERE id = ?");
$stmt->execute([$item_id]);
$item = $stmt->fetch();

if (!$item || $item['user_id'] == $buyer_id) {
    header("Location: dashboard.php");
    exit;
}

$seller_id = $item['user_id'];

try {
    // Check if a pending request already exists for this item/buyer
    $stmt_check = $pdo->prepare("SELECT id FROM transactions WHERE item_id = ? AND buyer_id = ? AND status = 'Pending'");
    $stmt_check->execute([$item_id, $buyer_id]);
    
    if (!$stmt_check->fetch()) {
        // Create new transaction request
        $stmt_insert = $pdo->prepare("INSERT INTO transactions (item_id, seller_id, buyer_id, type, trade_offer, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
        $stmt_insert->execute([$item_id, $seller_id, $buyer_id, $type, $trade_offer]);
    }

    header("Location: transactions.php?requested=1");
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

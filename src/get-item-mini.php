<?php
// src/get-item-mini.php
session_start();
require_once 'config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$stmt = $pdo->prepare("SELECT id, title, price, image_path, tag, item_condition FROM items WHERE id = ?");
$stmt->execute([$_GET['id']]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if ($item) {
    echo json_encode($item);
} else {
    echo json_encode(['error' => 'Item not found']);
}
?>

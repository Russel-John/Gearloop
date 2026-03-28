<?php
// src/process-list-item.php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'] ?? '';
    $category = $_POST['category'] ?? 'Other';
    $condition = $_POST['condition'] ?? 'A';
    $tag = $_POST['tag'] ?? 'For Sale';
    $price = $_POST['price'] ?? 0.00;
    $department = $_POST['department'] ?? 'General';
    $description = $_POST['description'] ?? '';

    try {
        $stmt = $pdo->prepare("INSERT INTO items (user_id, title, category, item_condition, tag, price, department, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $title, $category, $condition, $tag, $price, $department, $description]);
        
        // Success: Redirect to dashboard with success message
        header("Location: dashboard.php?listed=success");
        exit;
    } catch (\PDOException $e) {
        die("Error: Could not save listing. " . $e->getMessage());
    }
}
?>

<?php
// src/process-meetup.php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    header("Location: transactions.php");
    exit;
}

$transaction_id = $_POST['transaction_id'];
$location = $_POST['meetup_location'];
$time = $_POST['meetup_time'];
$user_id = $_SESSION['user_id'];

try {
    // Verify that the user is part of this transaction
    $stmt = $pdo->prepare("SELECT id FROM transactions WHERE id = ? AND (seller_id = ? OR buyer_id = ?)");
    $stmt->execute([$transaction_id, $user_id, $user_id]);
    
    if ($stmt->fetch()) {
        $stmt_update = $pdo->prepare("UPDATE transactions SET meetup_location = ?, meetup_time = ? WHERE id = ?");
        $stmt_update->execute([$location, $time, $transaction_id]);
        header("Location: transactions.php?meetup_updated=1");
    } else {
        header("Location: transactions.php?error=unauthorized");
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<?php
// src/process-transaction-action.php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id']) || !isset($_GET['action'])) {
    header("Location: transactions.php");
    exit;
}

$transaction_id = $_GET['id'];
$action = $_GET['action'];
$user_id = $_SESSION['user_id'];

try {
    // Fetch transaction to verify ownership
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = ?");
    $stmt->execute([$transaction_id]);
    $transaction = $stmt->fetch();

    if (!$transaction) {
        header("Location: transactions.php");
        exit;
    }

    if ($action === 'accept' && $transaction['seller_id'] == $user_id) {
        $stmt_update = $pdo->prepare("UPDATE transactions SET status = 'Accepted' WHERE id = ?");
        $stmt_update->execute([$transaction_id]);
        $msg = "accepted";
    } 
    elseif ($action === 'reject' && $transaction['seller_id'] == $user_id) {
        $stmt_update = $pdo->prepare("UPDATE transactions SET status = 'Cancelled' WHERE id = ?");
        $stmt_update->execute([$transaction_id]);
        $msg = "rejected";
    }
    elseif ($action === 'cancel' && $transaction['buyer_id'] == $user_id) {
        $stmt_update = $pdo->prepare("UPDATE transactions SET status = 'Cancelled' WHERE id = ?");
        $stmt_update->execute([$transaction_id]);
        $msg = "cancelled";
    }
    else {
        // Unauthorized action
        header("Location: transactions.php?error=unauthorized");
        exit;
    }

    header("Location: transactions.php?status=" . $msg);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

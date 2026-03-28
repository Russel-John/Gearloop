<?php
// src/process-login.php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_id = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Check both username and student_id
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR student_id = ?");
    $stmt->execute([$username_or_id, $username_or_id]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['department'] = $user['department'];
        header("Location: dashboard.php");
        exit;
    } else {
        header("Location: index.php?error=1");
        exit;
    }
}
?>

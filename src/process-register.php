<?php
// src/process-register.php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $student_id = trim($_POST['student_id'] ?? '');
    $department = $_POST['department'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Basic Validation
    if (empty($username) || empty($student_id) || empty($department) || empty($password) || empty($confirm_password)) {
        header("Location: register.php?error=empty");
        exit;
    }

    if ($password !== $confirm_password) {
        header("Location: register.php?error=password_mismatch");
        exit;
    }

    try {
        // Check if username or student_id already exists
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR student_id = ?");
        $checkStmt->execute([$username, $student_id]);
        
        if ($checkStmt->rowCount() > 0) {
            header("Location: register.php?error=username_taken");
            exit;
        }

        // Hash password and insert user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $insertStmt = $pdo->prepare("INSERT INTO users (username, student_id, department, password, role) VALUES (?, ?, ?, ?, 'student')");
        $insertStmt->execute([$username, $student_id, $department, $hashed_password]);

        // Registration successful: Redirect to login with success message
        header("Location: index.php?registered=1");
        exit;

    } catch (\PDOException $e) {
        die("Error: Could not register. " . $e->getMessage());
    }
}
?>

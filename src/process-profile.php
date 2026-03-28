<?php
// src/process-profile.php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$full_name = $_POST['full_name'] ?? '';
$department = $_POST['department'] ?? '';
$bio = $_POST['bio'] ?? '';

// Fetch current user data to get existing profile picture
$stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$profile_picture = $user['profile_picture'];

// Handle Image Upload
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'public/uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
    $new_filename = md5(uniqid() . time()) . '.' . $file_extension;
    $target_path = $upload_dir . $new_filename;

    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_path)) {
        // Delete old picture if it exists and is not the same
        if ($profile_picture && file_exists($profile_picture)) {
            unlink($profile_picture);
        }
        $profile_picture = $target_path;
    }
}

try {
    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, department = ?, bio = ?, profile_picture = ? WHERE id = ?");
    $stmt->execute([$full_name, $department, $bio, $profile_picture, $user_id]);
    
    header("Location: profile.php?success=1");
} catch (PDOException $e) {
    header("Location: profile.php?error=1");
}
exit;

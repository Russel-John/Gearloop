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

    // Image Upload Logic
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Sanitize file name
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

        // Allowed extensions
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'webp');

        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Directory where uploaded images will be saved
            $uploadFileDir = './public/uploads/';
            $dest_path = $uploadFileDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $image_path = 'public/uploads/' . $newFileName;
            }
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO items (user_id, title, category, item_condition, tag, price, department, description, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $title, $category, $condition, $tag, $price, $department, $description, $image_path]);
        
        // Success: Redirect to dashboard with success message
        header("Location: dashboard.php?listed=success");
        exit;
    } catch (\PDOException $e) {
        die("Error: Could not save listing. " . $e->getMessage());
    }
}
?>

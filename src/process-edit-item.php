<?php
// src/process-edit-item.php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$item_id = $_POST['id'];
$title = $_POST['title'];
$category = $_POST['category'];
$condition = $_POST['condition'];
$tag = $_POST['tag'];
$price = $_POST['price'] ?: 0.00;
$department = $_POST['department'];
$description = $_POST['description'];

// Security check: verify ownership
$stmt = $pdo->prepare("SELECT image_path FROM items WHERE id = ? AND user_id = ?");
$stmt->execute([$item_id, $user_id]);
$item = $stmt->fetch();

if (!$item) {
    die("Unauthorized access.");
}

$image_path = $item['image_path'];

// Handle image update if a new one is uploaded
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'public/uploads/';
    $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $new_filename = md5(uniqid() . time()) . '.' . $file_extension;
    $target_path = $upload_dir . $new_filename;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
        // Delete old image if it exists
        if ($image_path && file_exists($image_path)) {
            unlink($image_path);
        }
        $image_path = $target_path;
    }
}

try {
    $stmt = $pdo->prepare("UPDATE items SET 
        title = ?, 
        category = ?, 
        item_condition = ?, 
        tag = ?, 
        price = ?, 
        department = ?, 
        description = ?, 
        image_path = ? 
        WHERE id = ? AND user_id = ?");
    
    $stmt->execute([
        $title, 
        $category, 
        $condition, 
        $tag, 
        $price, 
        $department, 
        $description, 
        $image_path, 
        $item_id, 
        $user_id
    ]);

    header("Location: dashboard.php?updated=1");
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<?php
// src/dashboard.php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Fetch items
$stmt = $pdo->query("SELECT items.*, users.username FROM items JOIN users ON items.user_id = users.id ORDER BY items.created_at DESC");
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCLM GearLoop - Marketplace</title>
    <link rel="stylesheet" href="public/css/styles.css">
</head>
<body>
    <header>
        <h1>UCLM GearLoop</h1>
        <nav>
            <a href="dashboard.php">Marketplace</a>
            <a href="list-item.php">List an Item</a>
            <a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
        </nav>
    </header>

    <div class="container">
        <div class="flex-between mb-2">
            <h2>Academic Resource Marketplace</h2>
        </div>

        <?php if (empty($items)): ?>
            <div class="form-card text-center p-3">
                <p>No items listed yet. Be the first to share your resources!</p>
                <a href="list-item.php" class="btn mt-1">List an Item Now</a>
            </div>
        <?php else: ?>
            <div class="item-grid">
                <?php foreach ($items as $item): ?>
                    <div class="item-card">
                        <?php if ($item['image_path']): ?>
                            <div class="item-img-container">
                                <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="item-img">
                            </div>
                        <?php else: ?>
                            <div class="item-placeholder">
                                [No Image]
                            </div>
                        <?php endif; ?>
                        <div class="item-info">
                            <span class="condition-badge">Condition: <?php echo htmlspecialchars($item['item_condition']); ?></span>
                            <?php 
                                $tagClass = '';
                                if ($item['tag'] === 'For Sale') $tagClass = 'tag-sale';
                                elseif ($item['tag'] === 'For Swap') $tagClass = 'tag-swap';
                                else $tagClass = 'tag-both';
                            ?>
                            <span class="tag <?php echo $tagClass; ?>"><?php echo htmlspecialchars($item['tag']); ?></span>
                            <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                            <p class="item-info-text">
                                Dept: <?php echo htmlspecialchars($item['department']); ?><br>
                                Seller: <?php echo htmlspecialchars($item['username']); ?>
                            </p>
                            <div class="flex-between">
                                <span class="price">
                                    <?php echo ($item['tag'] !== 'For Swap') ? '₱' . number_format($item['price'], 2) : 'Trade Only'; ?>
                                </span>
                                <button class="btn btn-secondary btn-sm">View Details</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

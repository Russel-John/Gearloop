<?php
// src/my-listings.php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch only user's items
$stmt = $pdo->prepare("SELECT items.*, users.username FROM items JOIN users ON items.user_id = users.id WHERE items.user_id = ? ORDER BY items.created_at DESC");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll();

// Fetch current user data for navigation
$stmt_user = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
$stmt_user->execute([$user_id]);
$current_user = $stmt_user->fetch();

// Count pending incoming requests for notification badge
$stmt_notif = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE seller_id = ? AND status = 'Pending'");
$stmt_notif->execute([$user_id]);
$pending_count = $stmt_notif->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCLM GearLoop - My Listings</title>
    <link rel="stylesheet" href="public/css/styles.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <h1><i class="fas fa-cycle-loop"></i> UCLM GearLoop</h1>
        <nav>
            <a href="dashboard.php"><i class="fas fa-shop"></i> Marketplace</a>
            <a href="transactions.php" class="nav-link">
                <i class="fas fa-exchange-alt"></i> Transactions
                <?php if ($pending_count > 0): ?>
                    <span class="badge"><?php echo $pending_count; ?></span>
                <?php endif; ?>
            </a>
            <a href="profile.php">
                <?php if ($current_user['profile_picture']): ?>
                    <img src="<?php echo htmlspecialchars($current_user['profile_picture']); ?>" alt="" class="profile-img-nav">
                <?php else: ?>
                    <i class="fas fa-user-circle"></i>
                <?php endif; ?>
                Profile
            </a>
            <a href="logout.php" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
        </nav>
    </header>

    <div class="container">
        <div class="flex-between mb-2">
            <div>
                <h2 style="font-weight: 800; font-size: 1.8rem; color: var(--primary-color);">My Listings</h2>
                <p class="text-muted">Manage the items you have shared with the community.</p>
            </div>
            <div class="flex-gap">
                <a href="dashboard.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Marketplace</a>
                <a href="list-item.php" class="btn"><i class="fas fa-plus"></i> Post New Listing</a>
            </div>
        </div>

        <?php if (empty($items)): ?>
            <div class="form-card text-center p-3">
                <i class="fas fa-box-open fa-3x mb-1" style="color: #dee2e6;"></i>
                <p class="text-muted">You haven't listed any items yet.</p>
                <a href="list-item.php" class="btn mt-1">List Your First Item</a>
            </div>
        <?php else: ?>
            <div class="item-grid">
                <?php foreach ($items as $item): ?>
                    <div class="item-card">
                        <div class="item-img-container">
                            <?php if ($item['image_path']): ?>
                                <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="item-img">
                            <?php else: ?>
                                <div class="item-placeholder">
                                    <i class="fas fa-image fa-2x"></i>
                                    <span class="text-xs mt-1">No Image</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="item-info">
                            <div style="margin-bottom: 0.5rem;">
                                <span class="tag tag-<?php echo strtolower(explode(' ', $item['tag'])[1] ?? $item['tag']); ?>">
                                    <?php echo htmlspecialchars($item['tag']); ?>
                                </span>
                                <span class="condition-badge">Grade <?php echo htmlspecialchars($item['item_condition']); ?></span>
                            </div>

                            <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                            
                            <div class="item-info-text">
                                <span><i class="fas fa-building-columns"></i> <?php echo htmlspecialchars($item['department']); ?></span>
                                <span><i class="fas fa-calendar-alt"></i> Listed on: <?php echo date('M d, Y', strtotime($item['created_at'])); ?></span>
                            </div>

                            <div class="card-actions">
                                <span class="price">
                                    <?php echo ($item['tag'] !== 'For Swap') ? '₱' . number_format($item['price'], 2) : 'Trade Only'; ?>
                                </span>
                                <div class="flex-gap">
                                    <a href="edit-item.php?id=<?php echo $item['id']; ?>" class="btn btn-sm" title="Edit Listing">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <!-- Potentially add a Delete button here in the future -->
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

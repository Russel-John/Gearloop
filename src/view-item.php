<?php
// src/view-item.php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$item_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch item details with seller info
$stmt = $pdo->prepare("SELECT items.*, users.username, users.profile_picture as seller_avatar 
                       FROM items 
                       JOIN users ON items.user_id = users.id 
                       WHERE items.id = ?");
$stmt->execute([$item_id]);
$item = $stmt->fetch();

if (!$item) {
    header("Location: dashboard.php");
    exit;
}

// Fetch current user data for navigation
$stmt_user = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
$stmt_user->execute([$user_id]);
$current_user = $stmt_user->fetch();

// Notification count
$stmt_notif = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE seller_id = ? AND status = 'Pending'");
$stmt_notif->execute([$user_id]);
$pending_count = $stmt_notif->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCLM GearLoop - <?php echo htmlspecialchars($item['title']); ?></title>
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
        <div class="mb-2">
            <a href="dashboard.php" class="text-muted no-decoration"><i class="fas fa-arrow-left"></i> Back to Marketplace</a>
        </div>

        <div class="flex-gap">
            <!-- Item Image Section -->
            <div class="flex-1">
                <div class="form-card" style="padding: 0; overflow: hidden;">
                    <?php if ($item['image_path']): ?>
                        <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" style="width: 100%; height: auto; display: block;">
                    <?php else: ?>
                        <div class="item-placeholder" style="height: 400px;">
                            <i class="fas fa-image fa-4x"></i>
                            <span class="mt-1">No Image Available</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Item Details Section -->
            <div class="flex-1">
                <div class="form-card">
                    <div class="mb-1">
                        <span class="tag tag-<?php echo strtolower(explode(' ', $item['tag'])[1] ?? $item['tag']); ?>">
                            <?php echo htmlspecialchars($item['tag']); ?>
                        </span>
                        <span class="condition-badge">Grade <?php echo htmlspecialchars($item['item_condition']); ?></span>
                    </div>

                    <h2 class="page-header" style="font-size: 2rem; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($item['title']); ?></h2>
                    <p class="price" style="font-size: 1.8rem; margin-bottom: 1.5rem;">
                        <?php echo ($item['tag'] !== 'For Swap') ? '₱' . number_format($item['price'], 2) : 'Trade Only'; ?>
                    </p>

                    <div class="item-info-text mb-2" style="font-size: 1rem; gap: 0.75rem;">
                        <span><i class="fas fa-building-columns"></i> <strong>Department:</strong> <?php echo htmlspecialchars($item['department']); ?></span>
                        <span><i class="fas fa-tag"></i> <strong>Category:</strong> <?php echo htmlspecialchars($item['category']); ?></span>
                        <span><i class="fas fa-calendar-alt"></i> <strong>Posted on:</strong> <?php echo date('M d, Y', strtotime($item['created_at'])); ?></span>
                    </div>

                    <div class="mb-2">
                        <h4 class="mb-1">Description</h4>
                        <p class="text-muted text-line-1-6">
                            <?php echo nl2br(htmlspecialchars($item['description'] ?: 'No description provided.')); ?>
                        </p>
                    </div>

                    <hr style="border: 0; border-top: 1px solid #eee; margin: 2rem 0;">

                    <!-- Seller Info -->
                    <div class="flex-between mb-2">
                        <div class="flex-gap" style="align-items: center;">
                            <?php if ($item['seller_avatar']): ?>
                                <img src="<?php echo htmlspecialchars($item['seller_avatar']); ?>" class="profile-img-nav" style="width: 45px; height: 45px;">
                            <?php else: ?>
                                <i class="fas fa-user-circle fa-2x text-muted"></i>
                            <?php endif; ?>
                            <div>
                                <p class="bold" style="margin: 0;">Listed by <?php echo htmlspecialchars($item['username']); ?></p>
                                <p class="text-xs" style="margin: 0;">Verified UCLM Student</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex-gap">
                        <?php if ($item['user_id'] == $_SESSION['user_id']): ?>
                            <a href="edit-item.php?id=<?php echo $item['id']; ?>" class="btn w-100">Edit My Listing</a>
                        <?php else: ?>
                            <?php if ($item['tag'] === 'Both'): ?>
                                <a href="process-request.php?id=<?php echo $item['id']; ?>&type=Buy" class="btn flex-1" style="background-color: var(--accent-color);">Request Buy</a>
                                <a href="request-trade.php?id=<?php echo $item['id']; ?>" class="btn btn-secondary flex-1">Request Trade</a>
                            <?php elseif ($item['tag'] === 'For Sale'): ?>
                                <a href="process-request.php?id=<?php echo $item['id']; ?>&type=Buy" class="btn w-100" style="background-color: var(--accent-color);">Request Buy Now</a>
                            <?php else: ?>
                                <a href="request-trade.php?id=<?php echo $item['id']; ?>" class="btn btn-secondary w-100">Request Trade Now</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

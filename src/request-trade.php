<?php
// src/request-trade.php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$item_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch item details
$stmt = $pdo->prepare("SELECT items.*, users.username FROM items JOIN users ON items.user_id = users.id WHERE items.id = ?");
$stmt->execute([$item_id]);
$item = $stmt->fetch();

if (!$item || $item['user_id'] == $user_id) {
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
    <title>UCLM GearLoop - Request Trade</title>
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

    <div class="container" style="max-width: 600px;">
        <div class="form-card">
            <h2><i class="fas fa-handshake"></i> Propose a Trade</h2>
            <p class="text-muted mb-2">You are requesting to trade for: <strong><?php echo htmlspecialchars($item['title']); ?></strong></p>
            
            <div class="mb-2 p-1" style="background: #f8f9fa; border-radius: 8px; display: flex; gap: 1rem; align-items: center;">
                <?php if ($item['image_path']): ?>
                    <img src="<?php echo htmlspecialchars($item['image_path']); ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                <?php endif; ?>
                <div>
                    <span class="text-small text-muted">Seller: <?php echo htmlspecialchars($item['username']); ?></span>
                </div>
            </div>

            <form action="process-request.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                <input type="hidden" name="type" value="Trade">
                
                <div class="form-group">
                    <label for="trade_offer">What are you offering in exchange?</label>
                    <textarea id="trade_offer" name="trade_offer" rows="4" required 
                              placeholder="Describe the item you want to trade (e.g., 'I have a Calculus 1 book in Good condition' or 'My PE Uniform size XL')"></textarea>
                </div>

                <div class="flex-gap mt-2">
                    <button type="submit" class="btn flex-1">Send Trade Request</button>
                    <a href="dashboard.php" class="btn btn-secondary flex-1 text-center no-decoration">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php
// src/transactions.php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Fetch current user data for navigation
$stmt_user = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
$stmt_user->execute([$_SESSION['user_id']]);
$current_user = $stmt_user->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCLM GearLoop - Transactions</title>
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
            <a href="transactions.php"><i class="fas fa-exchange-alt"></i> Transactions</a>
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
                <h2 style="font-weight: 800; font-size: 1.8rem; color: var(--primary-color);">My Transactions</h2>
                <p class="text-muted">Coordinate meeting places and complete your swaps or sales.</p>
            </div>
            <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-shop"></i> Back to Marketplace</a>
        </div>

        <div class="form-card text-center p-3">
            <i class="fas fa-comments fa-3x mb-1" style="color: #dee2e6;"></i>
            <p class="text-muted">No active transactions found.</p>
            <p class="text-small mb-1">When you or someone else initiates a swap/buy, the conversation will appear here.</p>
            <a href="dashboard.php" class="btn mt-1">Explore Marketplace</a>
        </div>
    </div>
</body>
</html>

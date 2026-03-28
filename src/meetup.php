<?php
// src/meetup.php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: transactions.php");
    exit;
}

$transaction_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch transaction details
$stmt = $pdo->prepare("SELECT t.*, i.title, u_seller.username as seller_name, u_buyer.username as buyer_name 
                       FROM transactions t 
                       JOIN items i ON t.item_id = i.id 
                       JOIN users u_seller ON t.seller_id = u_seller.id 
                       JOIN users u_buyer ON t.buyer_id = u_buyer.id 
                       WHERE t.id = ? AND (t.seller_id = ? OR t.buyer_id = ?)");
$stmt->execute([$transaction_id, $user_id, $user_id]);
$transaction = $stmt->fetch();

if (!$transaction || $transaction['status'] !== 'Accepted') {
    header("Location: transactions.php");
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
    <title>UCLM GearLoop - Coordinate Meetup</title>
    <link rel="stylesheet" href="public/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            <h2><i class="fas fa-map-marker-alt"></i> Coordinate Meetup</h2>
            <p class="text-muted mb-2">Item: <strong><?php echo htmlspecialchars($transaction['title']); ?></strong></p>
            
            <form action="process-meetup.php" method="POST">
                <input type="hidden" name="transaction_id" value="<?php echo $transaction['id']; ?>">
                
                <div class="form-group">
                    <label for="meetup_location">Choose Campus Building</label>
                    <select id="meetup_location" name="meetup_location" required>
                        <option value="">Select a location</option>
                        <option value="Basic Ed" <?php echo $transaction['meetup_location'] == 'Basic Ed' ? 'selected' : ''; ?>>Basic Ed Building</option>
                        <option value="CBE" <?php echo $transaction['meetup_location'] == 'CBE' ? 'selected' : ''; ?>>CBE Building</option>
                        <option value="Annex" <?php echo $transaction['meetup_location'] == 'Annex' ? 'selected' : ''; ?>>Annex Building</option>
                        <option value="Old Annex" <?php echo $transaction['meetup_location'] == 'Old Annex' ? 'selected' : ''; ?>>Old Annex Building</option>
                        <option value="Maritime" <?php echo $transaction['meetup_location'] == 'Maritime' ? 'selected' : ''; ?>>Maritime Building</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="meetup_time">Preferred Date & Time</label>
                    <input type="datetime-local" id="meetup_time" name="meetup_time" 
                           value="<?php echo $transaction['meetup_time'] ? date('Y-m-d\TH:i', strtotime($transaction['meetup_time'])) : ''; ?>" required>
                </div>

                <div class="flex-gap mt-2">
                    <button type="submit" class="btn flex-1">Save Details</button>
                    <a href="transactions.php" class="btn btn-secondary flex-1 text-center no-decoration">Back</a>
                </div>
            </form>
            
            <p class="text-xs text-muted mt-2">
                <i class="fas fa-info-circle"></i> Both seller and buyer can update these details until the transaction is marked as complete.
            </p>
        </div>
    </div>
</body>
</html>

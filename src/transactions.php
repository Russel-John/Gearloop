<?php
// src/transactions.php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch current user data for navigation
$stmt_user = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
$stmt_user->execute([$user_id]);
$current_user = $stmt_user->fetch();

// Count pending incoming requests for notification badge
$stmt_notif = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE seller_id = ? AND status = 'Pending'");
$stmt_notif->execute([$user_id]);
$pending_count = $stmt_notif->fetchColumn();

// Fetch Incoming Requests (where user is the seller) - Filter out Cancelled/Rejected for the main active list
$stmt_in = $pdo->prepare("SELECT t.*, i.title, i.image_path, u.username as requester 
                         FROM transactions t 
                         JOIN items i ON t.item_id = i.id 
                         JOIN users u ON t.buyer_id = u.id 
                         WHERE t.seller_id = ? AND t.status != 'Cancelled'
                         ORDER BY t.created_at DESC");
$stmt_in->execute([$user_id]);
$incoming = $stmt_in->fetchAll();

// Fetch Outgoing Requests (where user is the buyer)
$stmt_out = $pdo->prepare("SELECT t.*, i.title, i.image_path, u.username as seller 
                          FROM transactions t 
                          JOIN items i ON t.item_id = i.id 
                          JOIN users u ON t.seller_id = u.id 
                          WHERE t.buyer_id = ? AND t.status != 'Cancelled'
                          ORDER BY t.created_at DESC");
$stmt_out->execute([$user_id]);
$outgoing = $stmt_out->fetchAll();
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
                <h2 style="font-weight: 800; font-size: 1.8rem; color: var(--primary-color);">My Transactions</h2>
                <p class="text-muted">Manage your trade and buy requests.</p>
            </div>
        </div>

        <?php if (isset($_GET['requested'])): ?>
            <div class="success-message"><i class="fas fa-check-circle"></i> Request sent successfully!</div>
        <?php endif; ?>
        <?php if (isset($_GET['status'])): ?>
            <div class="success-message">
                <i class="fas fa-info-circle"></i> Transaction <?php echo htmlspecialchars($_GET['status']); ?>!
            </div>
        <?php endif; ?>

        <!-- Incoming Requests Section -->
        <h3 class="mb-1"><i class="fas fa-inbox"></i> Incoming Requests</h3>
        <?php if (empty($incoming)): ?>
            <div class="form-card text-center p-3 mb-2">
                <p class="text-muted">No active incoming requests.</p>
            </div>
        <?php else: ?>
            <div class="item-grid mb-2">
                <?php foreach ($incoming as $req): ?>
                    <div class="item-card">
                        <div class="item-info">
                            <span class="tag <?php echo $req['type'] == 'Buy' ? 'tag-sale' : 'tag-swap'; ?>">
                                <?php echo $req['type']; ?> Request
                            </span>
                            <h3><?php echo htmlspecialchars($req['title']); ?></h3>
                            <p class="text-small">From: <strong><?php echo htmlspecialchars($req['requester']); ?></strong></p>
                            
                            <?php if ($req['type'] == 'Trade' && $req['trade_offer']): ?>
                                <div class="mt-1 mb-1 p-1 text-small" style="background: #f1f3f5; border-radius: 8px; border-left: 3px solid var(--secondary-color);">
                                    <strong>Offer:</strong> <?php echo nl2br(htmlspecialchars($req['trade_offer'])); ?>
                                </div>
                            <?php endif; ?>

                            <p class="text-xs text-muted mb-1">Status: <?php echo $req['status']; ?></p>
                            
                            <div class="flex-gap mt-auto">
                                <?php if ($req['status'] == 'Pending'): ?>
                                    <a href="process-transaction-action.php?id=<?php echo $req['id']; ?>&action=accept" class="btn btn-sm">Accept</a>
                                    <a href="process-transaction-action.php?id=<?php echo $req['id']; ?>&action=reject" class="btn btn-sm btn-outline">Reject</a>
                                <?php elseif ($req['status'] == 'Accepted'): ?>
                                    <a href="meetup.php?id=<?php echo $req['id']; ?>" class="btn btn-sm btn-secondary">Coordinate Meetup</a>
                                    <a href="process-transaction-action.php?id=<?php echo $req['id']; ?>&action=reject" class="btn btn-sm btn-outline" onclick="return confirm('Are you sure you want to cancel this accepted transaction?')">Cancel</a>
                                <?php endif; ?>
                            </div>
                            <?php if ($req['meetup_location']): ?>
                                <div class="mt-1 p-1 text-xs" style="background: #fff9db; border-radius: 4px;">
                                    <i class="fas fa-map-marker-alt"></i> <strong>Meetup:</strong> <?php echo htmlspecialchars($req['meetup_location']); ?><br>
                                    <i class="fas fa-clock"></i> <?php echo date('M d, h:i A', strtotime($req['meetup_time'])); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Outgoing Requests Section -->
        <h3 class="mb-1 mt-2"><i class="fas fa-paper-plane"></i> Sent Requests</h3>
        <?php if (empty($outgoing)): ?>
            <div class="form-card text-center p-3">
                <p class="text-muted">You haven't sent any active requests.</p>
            </div>
        <?php else: ?>
            <div class="item-grid">
                <?php foreach ($outgoing as $req): ?>
                    <div class="item-card">
                        <div class="item-info">
                            <span class="tag <?php echo $req['type'] == 'Buy' ? 'tag-sale' : 'tag-swap'; ?>">
                                <?php echo $req['type']; ?> Request
                            </span>
                            <h3><?php echo htmlspecialchars($req['title']); ?></h3>
                            <p class="text-small">Seller: <strong><?php echo htmlspecialchars($req['seller']); ?></strong></p>
                            <p class="text-xs text-muted mb-1">Status: <?php echo $req['status']; ?></p>
                            
                            <div class="flex-gap mt-auto">
                                <?php if ($req['status'] == 'Pending'): ?>
                                    <a href="process-transaction-action.php?id=<?php echo $req['id']; ?>&action=cancel" class="btn btn-sm btn-outline">Cancel Request</a>
                                <?php elseif ($req['status'] == 'Accepted'): ?>
                                    <a href="meetup.php?id=<?php echo $req['id']; ?>" class="btn btn-sm btn-secondary">Coordinate Meetup</a>
                                    <a href="process-transaction-action.php?id=<?php echo $req['id']; ?>&action=cancel" class="btn btn-sm btn-outline" onclick="return confirm('Are you sure you want to cancel this accepted transaction?')">Cancel</a>
                                <?php endif; ?>
                            </div>
                            <?php if ($req['meetup_location']): ?>
                                <div class="mt-1 p-1 text-xs" style="background: #fff9db; border-radius: 4px;">
                                    <i class="fas fa-map-marker-alt"></i> <strong>Meetup:</strong> <?php echo htmlspecialchars($req['meetup_location']); ?><br>
                                    <i class="fas fa-clock"></i> <?php echo date('M d, h:i A', strtotime($req['meetup_time'])); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

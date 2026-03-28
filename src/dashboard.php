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

// Fetch current user data for navigation
$stmt_user = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
$stmt_user->execute([$_SESSION['user_id']]);
$current_user = $stmt_user->fetch();

// Count pending incoming requests for notification badge
$stmt_notif = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE seller_id = ? AND status = 'Pending'");
$stmt_notif->execute([$_SESSION['user_id']]);
$pending_count = $stmt_notif->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCLM GearLoop - Marketplace</title>
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
                <h2 style="font-weight: 800; font-size: 1.8rem; color: var(--primary-color);">Academic Marketplace</h2>
                <p class="text-muted"><i class="fas fa-leaf"></i> SDG 12: Responsible Consumption & Production</p>
            </div>
            <div class="flex-gap">
                <a href="my-listings.php" class="btn btn-outline"><i class="fas fa-list"></i> My Listings</a>
                <a href="list-item.php" class="btn"><i class="fas fa-plus"></i> Post New Listing</a>
            </div>
        </div>

        <?php if (isset($_GET['updated'])): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> Listing updated successfully!
            </div>
        <?php endif; ?>

        <?php if (empty($items)): ?>
            <div class="form-card text-center p-3">
                <i class="fas fa-box-open fa-3x mb-1" style="color: #dee2e6;"></i>
                <p class="text-muted">No items listed yet. Be the first to share your resources!</p>
                <a href="list-item.php" class="btn mt-1">List an Item Now</a>
            </div>
        <?php else: ?>
            <div class="item-grid">
                <?php foreach ($items as $item): ?>
                    <div class="item-card">
                        <a href="view-item.php?id=<?php echo $item['id']; ?>" class="no-decoration">
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
                        </a>
                        
                        <div class="item-info">
                            <div style="margin-bottom: 0.5rem;">
                                <span class="tag tag-<?php echo strtolower(explode(' ', $item['tag'])[1] ?? $item['tag']); ?>">
                                    <?php echo htmlspecialchars($item['tag']); ?>
                                </span>
                                <span class="condition-badge">Grade <?php echo htmlspecialchars($item['item_condition']); ?></span>
                            </div>

                            <a href="view-item.php?id=<?php echo $item['id']; ?>" class="no-decoration">
                                <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                            </a>
                            
                            <div class="item-info-text">
                                <span><i class="fas fa-building-columns"></i> <?php echo htmlspecialchars($item['department']); ?></span>
                                <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($item['username']); ?></span>
                            </div>

                            <div class="card-actions">
                                <span class="price">
                                    <?php echo ($item['tag'] !== 'For Swap') ? '₱' . number_format($item['price'], 2) : 'Trade Only'; ?>
                                </span>
                                <div class="flex-gap">
                                    <?php if ($item['user_id'] == $_SESSION['user_id']): ?>
                                        <span class="text-xs text-muted">Your Listing</span>
                                    <?php else: ?>
                                        <a href="view-item.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-secondary">View Details</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- AI Chatbot Floating Button -->
    <div class="chatbot-float" id="chatbot-toggle" title="Ask GEPO AI">
        <img src="public/images/GEPOAI.png" alt="GEPO AI">
    </div>

    <!-- Chat Window -->
    <div class="chat-window" id="chat-window">
        <div class="chat-header">
            <h4><i class="fas fa-robot"></i> GEPO AI</h4>
            <button id="close-chat" style="background: none; border: none; color: white; cursor: pointer;"><i class="fas fa-times"></i></button>
        </div>
        <div class="chat-messages" id="chat-messages">
            <div class="message ai">
                Hello! I am GEPO, your UCLM GearLoop assistant. How can I help you today?
            </div>
        </div>
        <div class="chat-input-area">
            <input type="text" id="chat-input" placeholder="Type your message...">
            <button id="send-chat"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <script>
        const chatWindow = document.getElementById('chat-window');
        const chatToggle = document.getElementById('chatbot-toggle');
        const closeChat = document.getElementById('close-chat');
        const sendBtn = document.getElementById('send-chat');
        const chatInput = document.getElementById('chat-input');
        const chatMessages = document.getElementById('chat-messages');

        chatToggle.addEventListener('click', () => {
            chatWindow.style.display = chatWindow.style.display === 'flex' ? 'none' : 'flex';
        });

        closeChat.addEventListener('click', () => {
            chatWindow.style.display = 'none';
        });

        async function sendMessage() {
            const message = chatInput.value.trim();
            if (!message) return;

            // Add user message
            addMessage(message, 'user');
            chatInput.value = '';

            // Add loading placeholder
            const loadingId = 'loading-' + Date.now();
            addMessage('...', 'ai', loadingId);

            try {
                const response = await fetch('process-chat.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: message })
                });
                const data = await response.json();
                
                document.getElementById(loadingId).innerText = data.response;
            } catch (error) {
                document.getElementById(loadingId).innerText = "Sorry, I'm having trouble connecting right now.";
            }
        }

        function addMessage(text, sender, id = null) {
            const div = document.createElement('div');
            div.className = 'message ' + sender;
            if (id) div.id = id;
            div.innerText = text;
            chatMessages.appendChild(div);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        sendBtn.addEventListener('click', sendMessage);
        chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendMessage();
        });
    </script>
</body>
</html>

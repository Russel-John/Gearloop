<?php
// src/edit-item.php
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

// Fetch the item and ensure the user owns it
$stmt = $pdo->prepare("SELECT * FROM items WHERE id = ? AND user_id = ?");
$stmt->execute([$item_id, $user_id]);
$item = $stmt->fetch();

if (!$item) {
    header("Location: dashboard.php");
    exit;
}

// Fetch current user data for navigation
$stmt_user = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
$stmt_user->execute([$user_id]);
$current_user = $stmt_user->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCLM GearLoop - Edit Item</title>
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
            <a href="my-cart.php"><i class="fas fa-shopping-cart"></i> My Cart</a>
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
        <div class="form-card list-item-card">
            <h2>Edit Your Listing</h2>
            <p class="text-muted mb-2">Update the details for "<?php echo htmlspecialchars($item['title']); ?>".</p>

            <form action="process-edit-item.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                
                <div class="form-group">
                    <label for="title">Item Name/Title</label>
                    <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($item['title']); ?>">
                </div>

                <div class="form-group">
                    <label for="image">Item Image (Leave empty to keep current image)</label>
                    <?php if ($item['image_path']): ?>
                        <div class="mb-1">
                            <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="Current image" style="width: 100px; height: auto; border-radius: 4px; border: 1px solid #ddd;">
                        </div>
                    <?php endif; ?>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>

                <div class="flex-gap">
                    <div class="form-group flex-1">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>
                            <option value="Uniform" <?php echo $item['category'] == 'Uniform' ? 'selected' : ''; ?>>Uniform</option>
                            <option value="Book" <?php echo $item['category'] == 'Book' ? 'selected' : ''; ?>>Book</option>
                            <option value="Equipment" <?php echo $item['category'] == 'Equipment' ? 'selected' : ''; ?>>Equipment</option>
                            <option value="Other" <?php echo $item['category'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="form-group flex-1">
                        <label for="condition">Condition Scale</label>
                        <select id="condition" name="condition" required>
                            <option value="A" <?php echo $item['item_condition'] == 'A' ? 'selected' : ''; ?>>Grade A (Mint/New)</option>
                            <option value="B" <?php echo $item['item_condition'] == 'B' ? 'selected' : ''; ?>>Grade B (Good)</option>
                            <option value="C" <?php echo $item['item_condition'] == 'C' ? 'selected' : ''; ?>>Grade C (Fair/Used)</option>
                            <option value="D" <?php echo $item['item_condition'] == 'D' ? 'selected' : ''; ?>>Grade D (Damaged)</option>
                        </select>
                    </div>
                </div>

                <div class="flex-gap">
                    <div class="form-group flex-1">
                        <label for="tag">Listing Type</label>
                        <select id="tag" name="tag" required>
                            <option value="For Sale" <?php echo $item['tag'] == 'For Sale' ? 'selected' : ''; ?>>For Sale</option>
                            <option value="For Swap" <?php echo $item['tag'] == 'For Swap' ? 'selected' : ''; ?>>For Swap</option>
                            <option value="Both" <?php echo $item['tag'] == 'Both' ? 'selected' : ''; ?>>Both</option>
                        </select>
                    </div>
                    <div class="form-group flex-1">
                        <label for="price">Price (₱)</label>
                        <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($item['price']); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="department">Target Department</label>
                    <select id="department" name="department" required>
                        <option value="General" <?php echo $item['department'] == 'General' ? 'selected' : ''; ?>>General/Any</option>
                        <option value="Maritime" <?php echo $item['department'] == 'Maritime' ? 'selected' : ''; ?>>Maritime</option>
                        <option value="Criminology" <?php echo $item['department'] == 'Criminology' ? 'selected' : ''; ?>>Criminology</option>
                        <option value="Nursing" <?php echo $item['department'] == 'Nursing' ? 'selected' : ''; ?>>Nursing</option>
                        <option value="Engineering" <?php echo $item['department'] == 'Engineering' ? 'selected' : ''; ?>>Engineering</option>
                        <option value="Education" <?php echo $item['department'] == 'Education' ? 'selected' : ''; ?>>Education</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description (Optional)</label>
                    <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($item['description']); ?></textarea>
                </div>

                <div class="flex-gap mt-2">
                    <button type="submit" class="btn flex-1">Update Listing</button>
                    <a href="dashboard.php" class="btn btn-secondary flex-1 text-center no-decoration">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

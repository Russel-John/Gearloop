<?php
// src/list-item.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCLM GearLoop - List an Item</title>
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
        <div class="form-card" style="max-width: 600px; margin: 0 auto;">
            <h2>List an Academic Resource</h2>
            <p style="color: #666; margin-bottom: 2rem;">Post your items to help fellow UCLM students and reduce waste.</p>

            <form action="process-list-item.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Item Name/Title</label>
                    <input type="text" id="title" name="title" required placeholder="e.g. Nursing Scrub Suit, Engineering Book">
                </div>

                <div class="form-group">
                    <label for="image">Item Image</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>

                <div style="display: flex; gap: 1rem;">
                    <div class="form-group" style="flex: 1;">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>
                            <option value="Uniform">Uniform</option>
                            <option value="Book">Book</option>
                            <option value="Equipment">Equipment</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="condition">Condition Scale</label>
                        <select id="condition" name="condition" required>
                            <option value="A">Grade A (Mint/New)</option>
                            <option value="B">Grade B (Good)</option>
                            <option value="C">Grade C (Fair/Used)</option>
                            <option value="D">Grade D (Damaged)</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <div class="form-group" style="flex: 1;">
                        <label for="tag">Listing Type</label>
                        <select id="tag" name="tag" required>
                            <option value="For Sale">For Sale</option>
                            <option value="For Swap">For Swap</option>
                            <option value="Both">Both</option>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="price">Price (₱)</label>
                        <input type="number" id="price" name="price" step="0.01" value="0.00">
                    </div>
                </div>

                <div class="form-group">
                    <label for="department">Target Department</label>
                    <select id="department" name="department" required>
                        <option value="General">General/Any</option>
                        <option value="Maritime">Maritime</option>
                        <option value="Criminology">Criminology</option>
                        <option value="Nursing">Nursing</option>
                        <option value="Engineering">Engineering</option>
                        <option value="Education">Education</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description (Optional)</label>
                    <textarea id="description" name="description" rows="4" placeholder="Mention size, edition, or any defects..."></textarea>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn" style="flex: 1;">Post Listing</button>
                    <a href="dashboard.php" class="btn btn-secondary" style="flex: 1; text-align: center; text-decoration: none;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

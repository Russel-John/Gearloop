<?php
// src/profile.php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: logout.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCLM GearLoop - My Profile</title>
    <link rel="stylesheet" href="public/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Cropper.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
</head>
<body>
    <header>
        <h1><i class="fas fa-cycle-loop"></i> UCLM GearLoop</h1>
        <nav>
            <a href="dashboard.php"><i class="fas fa-shop"></i> Marketplace</a>
            <a href="transactions.php"><i class="fas fa-exchange-alt"></i> Transactions</a>
            <a href="profile.php">
                <?php if ($user['profile_picture']): ?>
                    <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="" class="profile-img-nav">
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
            <h2>My Profile</h2>
            <a href="dashboard.php" class="btn btn-secondary btn-sm no-decoration">Back to Marketplace</a>
        </div>

        <div class="flex-gap">
            <!-- Profile Display -->
            <div class="form-card profile-card flex-1">
                <div class="profile-info">
                    <?php if ($user['profile_picture']): ?>
                        <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-img-large">
                    <?php else: ?>
                        <div class="profile-img-large" style="background: #ddd; display: inline-flex; align-items: center; justify-content: center; color: #999; font-size: 3rem;">
                            ?
                        </div>
                    <?php endif; ?>
                    
                    <h2><?php echo htmlspecialchars($user['full_name'] ?: $user['username']); ?></h2>
                    <p class="text-muted"><?php echo htmlspecialchars($user['department']); ?> Department</p>
                    <p class="text-small">Student ID: <?php echo htmlspecialchars($user['student_id']); ?></p>
                    
                    <?php if ($user['bio']): ?>
                        <div class="mt-1 p-3" style="background: #f9f9f9; border-radius: 8px; text-align: left;">
                            <p><strong>Bio:</strong></p>
                            <p><?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Edit Profile Form -->
            <div class="form-card flex-1">
                <h3>Edit Profile</h3>
                <?php if (isset($_GET['success'])): ?>
                    <div class="success-message">Profile updated successfully!</div>
                <?php endif; ?>
                <?php if (isset($_GET['error'])): ?>
                    <div class="error-message">Error updating profile. Please try again.</div>
                <?php endif; ?>

                <form id="profile-form" action="process-profile.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="profile_picture">Profile Picture (1:1 Aspect Ratio Recommended)</label>
                        <input type="file" id="profile_picture_input" accept="image/*">
                        <!-- Hidden input to store cropped image data -->
                        <input type="hidden" name="cropped_image" id="cropped_image_data">
                    </div>

                    <!-- Cropper Preview Area -->
                    <div class="cropper-container-wrapper" id="cropper-wrapper">
                        <p class="text-small mb-1">Drag to crop your picture:</p>
                        <div>
                            <img id="cropper-image">
                        </div>
                        <button type="button" id="crop-button" class="btn btn-secondary btn-sm mt-1">Confirm Crop</button>
                    </div>

                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" placeholder="Enter your full name">
                    </div>

                    <div class="form-group">
                        <label for="department">Department</label>
                        <select id="department" name="department" required>
                            <option value="Maritime" <?php echo $user['department'] == 'Maritime' ? 'selected' : ''; ?>>Maritime</option>
                            <option value="Criminology" <?php echo $user['department'] == 'Criminology' ? 'selected' : ''; ?>>Criminology</option>
                            <option value="Nursing" <?php echo $user['department'] == 'Nursing' ? 'selected' : ''; ?>>Nursing</option>
                            <option value="Engineering" <?php echo $user['department'] == 'Engineering' ? 'selected' : ''; ?>>Engineering</option>
                            <option value="Education" <?php echo $user['department'] == 'Education' ? 'selected' : ''; ?>>Education</option>
                            <option value="CCS" <?php echo $user['department'] == 'CCS' ? 'selected' : ''; ?>>Computer Studies (CCS)</option>
                            <option value="CBA" <?php echo $user['department'] == 'CBA' ? 'selected' : ''; ?>>Business & Accountancy (CBA)</option>
                            <option value="Customs" <?php echo $user['department'] == 'Customs' ? 'selected' : ''; ?>>Customs Administration</option>
                            <option value="CTHM" <?php echo $user['department'] == 'CTHM' ? 'selected' : ''; ?>>Hospitality & Tourism (CTHM)</option>
                            <option value="ArtsSciences" <?php echo $user['department'] == 'ArtsSciences' ? 'selected' : ''; ?>>Arts & Sciences</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" rows="4" placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['bio']); ?></textarea>
                    </div>

                    <button type="submit" class="btn w-100">Update Profile</button>
                </form>
            </div>
        </div>
    </div>

    <!-- External JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script src="public/js/profile.js"></script>
</body>
</html>

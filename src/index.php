<?php
// src/index.php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCLM GearLoop - Login</title>
    <link rel="stylesheet" href="public/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <h1><i class="fas fa-cycle-loop"></i> UCLM GearLoop</h1>
    </header>
    <div class="container login-container">
        <div class="form-card">
            <div class="form-header">
                <h2>GearLoop Login</h2>
                <p class="text-muted text-small">Access the secure campus marketplace.</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> Invalid username or password.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['registered'])): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> Registration successful! Please login.
                </div>
            <?php endif; ?>

            <form action="process-login.php" method="POST">
                <div class="form-group">
                    <label for="username">Username or Student ID</label>
                    <input type="text" id="username" name="username" required placeholder="e.g. gear_student or 2023-XXXXX">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn w-100">Login to GearLoop</button>
            </form>
            <p class="mt-2 text-center text-small">
                Don't have an account? <a href="register.php" class="primary-link">Register here</a>
            </p>
            <p class="mt-1 text-center text-xs">
                University of Cebu Lapu-Lapu and Mandaue
            </p>
        </div>
    </div>
</body>
</html>

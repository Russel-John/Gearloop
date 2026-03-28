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
</head>
<body>
    <header>
        <h1>UCLM GearLoop</h1>
    </header>
    <div class="container" style="max-width: 400px; margin-top: 100px;">
        <div class="form-card">
            <h2>Verified Student Login</h2>
            <p style="color: #666; font-size: 0.9rem; margin-bottom: 1.5rem;">Access the secure campus marketplace.</p>
            
            <?php if (isset($_GET['error'])): ?>
                <div style="color: red; margin-bottom: 1rem; font-size: 0.9rem;">
                    Invalid username or password.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['registered'])): ?>
                <div style="color: green; margin-bottom: 1rem; font-size: 0.9rem;">
                    Registration successful! Please login below.
                </div>
            <?php endif; ?>

            <form action="process-login.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required placeholder="student123">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="********">
                </div>
                <button type="submit" class="btn" style="width: 100%;">Login</button>
            </form>
            <p style="margin-top: 1.5rem; text-align: center; font-size: 0.9rem;">
                Don't have an account? <a href="register.php" style="color: var(--primary-color); font-weight: bold;">Register here</a>
            </p>
            <p style="margin-top: 1rem; text-align: center; font-size: 0.8rem; color: #999;">
                Sample Creds: student123 / password
            </p>
        </div>
    </div>
</body>
</html>

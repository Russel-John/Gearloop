<?php
// src/register.php
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
    <title>UCLM GearLoop - Register</title>
    <link rel="stylesheet" href="public/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <h1><i class="fas fa-cycle-loop"></i> UCLM GearLoop</h1>
    </header>
    <div class="container auth-container register-container">
        <div class="form-card">
            <h2>Create an Account</h2>
            <p class="text-muted text-small mb-1-5">Join the campus marketplace for academic resources.</p>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php 
                        if ($_GET['error'] == 'empty') echo "Please fill in all fields.";
                        elseif ($_GET['error'] == 'password_mismatch') echo "Passwords do not match.";
                        elseif ($_GET['error'] == 'username_taken') echo "Username or Student ID already exists.";
                        else echo "An error occurred. Please try again.";
                    ?>
                </div>
            <?php endif; ?>

            <form action="process-register.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required placeholder="e.g. gear_student">
                </div>
                
                <div class="form-group">
                    <label for="student_id">UCLM Student ID</label>
                    <input type="text" id="student_id" name="student_id" required placeholder="2023-XXXXX">
                </div>

                <div class="form-group">
                    <label for="department">Department</label>
                    <select id="department" name="department" required>
                        <option value="">Select Department</option>
                        <option value="Maritime">Maritime</option>
                        <option value="Criminology">Criminology</option>
                        <option value="Nursing">Nursing</option>
                        <option value="Engineering">Engineering</option>
                        <option value="Education">Education</option>
                        <option value="CCS">Computer Studies (CCS)</option>
                        <option value="CBA">Business & Accountancy (CBA)</option>
                        <option value="Customs">Customs Administration</option>
                        <option value="CTHM">Hospitality & Tourism (CTHM)</option>
                        <option value="ArtsSciences">Arts & Sciences</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="********">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="********">
                </div>

                <button type="submit" class="btn w-100">Register</button>
            </form>
            
            <p class="mt-1-5 text-center text-small">
                Already have an account? <a href="index.php" class="primary-link">Login here</a>
            </p>
        </div>
    </div>
</body>
</html>

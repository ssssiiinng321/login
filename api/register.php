<?php
require 'session.php';
if (isset($_SESSION['user_id'])) {
    // header("Location: dashboard.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - YourPurpose POS</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-page">
    <canvas id="canvas1"></canvas>
    
    <div class="auth-container-wrapper">
        <!-- Form Side (Left) -->
        <div class="form-side">
            <h1 class="auth-title text-center">Create Account</h1>
            <p class="auth-subtitle text-center">Get started with YourPurpose POS</p>

            <?php if(isset($_GET['error'])): ?>
                <div style="color: #ef4444; margin-bottom: 1rem; text-align: center;">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <form action="auth.php" method="POST">
                <input type="hidden" name="action" value="register">
                
                <div class="modern-form-group">
                    <i class="fas fa-user input-icon-left"></i>
                    <input type="text" name="username" class="modern-input" placeholder="Username" required>
                </div>

                <div class="modern-form-group">
                    <i class="fas fa-envelope input-icon-left"></i>
                    <input type="email" name="email" class="modern-input" placeholder="Email Address" required>
                </div>

                <div class="modern-form-group">
                    <i class="fas fa-lock input-icon-left"></i>
                    <input type="password" name="password" class="modern-input" placeholder="Password" required>
                </div>

                <button type="submit" class="btn-primary-pill">SIGN UP</button>
            </form>
        </div>

<!-- Night Side (Right) -->
        <div class="night-side">
            <div class="cloud-shape"></div>
            <div class="night-content">
                <h2>Already have an account?</h2>
                <p>Log in to access your dashboard.</p>
                <a href="index.php" class="btn-outline-pill">Sign in</a>
            </div>
        </div>
    </div>

    <div class="pos-footer">
        Powered by <span>YourPurpose POS System</span> &bull; Efficient Sales Management
    </div>

    <script src="animation.js"></script>
</body>
</html>

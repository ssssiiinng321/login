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
    <title>Login - YourPurpose POS</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-page">
    <canvas id="canvas1"></canvas>
    
    <div class="auth-container-wrapper">
<!-- Night Side (Left) -->
        <div class="night-side">
            <div class="cloud-shape"></div>
            <div class="night-content">
                <h2>Don't have an account?</h2>
                <p>Join us today and streamline your sales.</p>
                <a href="register.php" class="btn-outline-pill">Sign up</a>
            </div>
        </div>

        <!-- Form Side (Right) -->
        <div class="form-side">
            <h1 class="auth-title text-center">WELCOME</h1>
            <p class="auth-subtitle text-center">Sign in to your POS account</p>

            <?php if(isset($_GET['error'])): ?>
                <div style="color: #ef4444; margin-bottom: 1rem; text-align: center;">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            <?php if(isset($_GET['success'])): ?>
                <div style="color: #10b981; margin-bottom: 1rem; text-align: center;">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>

            <form action="auth.php" method="POST">
                <input type="hidden" name="action" value="login">
                
                <div class="modern-form-group">
                    <i class="fas fa-user input-icon-left"></i>
                    <input type="email" name="email" class="modern-input" placeholder="Email Address" required>
                </div>

                <div class="modern-form-group">
                    <i class="fas fa-lock input-icon-left"></i>
                    <input type="password" name="password" id="password" class="modern-input" placeholder="Password" required>
                </div>

                <button type="submit" class="btn-primary-pill">LOGIN</button>
            </form>

            <div class="text-center" style="margin-top: 1.5rem;">
                <a href="#" class="text-link text-sm">Forgot your Password?</a>
            </div>
        </div>
    </div>

    <div class="pos-footer">
        Powered by <span>YourPurpose POS System</span> &bull; Efficient Sales Management
    </div>

    <script src="../animation.js"></script>
</body>
</html>

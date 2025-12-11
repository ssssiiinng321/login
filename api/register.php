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
    <title>Register - YourPurpose</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="shape shape-1">&#x2B22;</div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>

    <nav>
        <div class="logo">
            <i class="fas fa-cube"></i> yourpurpose
        </div>
        <ul class="nav-links">
            <li><a href="#">Docs</a></li>
            <li><a href="#">Changelog</a></li>
        </ul>
        <a href="#" class="btn-purchase"><i class="fas fa-shopping-cart"></i> Purchase now</a>
    </nav>

    <main>
        <div class="hero-text">
            <h1>Join our community today.</h1>
            <p class="quote">"The journey of a thousand miles begins with one step."</p>
            <span class="author">— Lao Tzu</span>
        </div>

        <div class="auth-card">
            <h2>Sign Up</h2>
            <p class="auth-subtitle">Create your account to get started.</p>

            <?php if(isset($_GET['error'])): ?>
                <div style="color: #ef4444; margin-bottom: 1rem; font-size: 0.9rem;">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <form action="auth.php" method="POST">
                <input type="hidden" name="action" value="register">
                
                <div class="form-group">
                    <label>Username</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" name="username" class="form-input" placeholder="johndoe" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="email" class="form-input" placeholder="name@example.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-key input-icon"></i>
                        <input type="password" name="password" class="form-input" placeholder="Create a password" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Sign Up ➜</button>
            </form>

            <div class="register-link">
                Already have an account? <a href="index.php">Sign in</a>
            </div>
        </div>
    </main>

</body>
</html>

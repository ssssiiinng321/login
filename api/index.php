<?php
session_start();
if (isset($_SESSION['user_id'])) {
    // header("Location: dashboard.php"); // Redirect if already logged in (optional implementation)
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - YourPurpose</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Background Shapes -->
    <div class="shape shape-1"><i class="fas fa-hexagon"></i> &#x2B22;</div> <!-- Hexagon entity -->
    <div class="shape shape-2"><i class="fas fa-circle"></i></div>
    <div class="shape shape-3"><i class="fas fa-square"></i></div>

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
            <h1>Keep your face always toward the sunshine - and shadows will fall behind you.</h1>
            <p class="quote">— John Sullivan</p>
        </div>

        <div class="auth-card">
            <h2>Login</h2>
            <p class="auth-subtitle">Sign in to your account to continue.</p>

            <?php if(isset($_GET['error'])): ?>
                <div style="color: #ef4444; margin-bottom: 1rem; font-size: 0.9rem;">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            <?php if(isset($_GET['success'])): ?>
                <div style="color: #10b981; margin-bottom: 1rem; font-size: 0.9rem;">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>

            <form action="auth.php" method="POST">
                <input type="hidden" name="action" value="login">
                
                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user input-icon"></i>
                        <input type="email" name="email" class="form-input" placeholder="name@example.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <div style="display: flex; justify-content: space-between;">
                        <label>Password</label>
                        <a href="#" class="forgot-password">Last password?</a> <!-- Sic from image "Last password?" or "Lost password?" usually -->
                    </div>
                    <div class="input-wrapper">
                        <i class="fas fa-key input-icon"></i>
                        <input type="password" name="password" id="password" class="form-input" placeholder="Password" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword()"></i>
                    </div>
                </div>

                <button type="submit" class="btn-submit">Sign in ➜</button>
            </form>

            <div class="register-link">
                Not registered? <a href="register.php">Create account</a>
            </div>
        </div>
    </main>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = document.querySelector('.toggle-password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>

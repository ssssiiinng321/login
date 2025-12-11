<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$username = $_SESSION['username'] ?? 'Guest';
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - YourPurpose</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .dashboard-container {
            max-width: 800px;
            margin: 100px auto;
            text-align: center;
            background: rgba(255, 255, 255, 0.05);
            padding: 3rem;
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        .welcome-title {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #fff 0%, #a5b4fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body>

    <nav>
        <div class="logo">
            <i class="fas fa-cube"></i> yourpurpose
        </div>
        <a href="index.php" class="btn-purchase"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>

    <main>
        <div class="dashboard-container">
            <?php if ($is_logged_in): ?>
                <div style="font-size: 4rem; color: #10b981; margin-bottom: 1rem;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1 class="welcome-title">Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
                <p style="color: var(--text-secondary); font-size: 1.1rem; margin-bottom: 2rem;">
                    You have successfully logged into the system. This is your personal dashboard.
                </p>
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <button class="btn-submit" style="width: auto; padding: 0.8rem 2rem;">Explore Features</button>
                    <button class="btn-purchase" style="background: rgba(255,255,255,0.1);">Settings</button>
                </div>
            <?php else: ?>
                <div style="font-size: 4rem; color: #f59e0b; margin-bottom: 1rem;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h1 class="welcome-title">Session Not Found</h1>
                <p style="color: var(--text-secondary); margin-bottom: 2rem;">
                    It seems you are not logged in, or your session has expired.
                </p>
                <a href="index.php" class="btn-submit" style="display: inline-block; width: auto; text-decoration: none;">Go to Login</a>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>

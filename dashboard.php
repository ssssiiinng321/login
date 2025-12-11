<?php
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
    <title>Dashboard - YourPurpose</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <nav>
        <div class="logo">
            <i class="fas fa-cube"></i> yourpurpose
        </div>
        <ul class="nav-links">
            <li><a href="#">Docs</a></li>
            <li><a href="#">Changelog</a></li>
        </ul>
        <a href="logout.php" class="btn-purchase"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>

    <main class="dashboard-main" style="flex-direction: column; align-items: center; text-align: center; gap: 1rem;">
        <h1 style="font-size: 3rem;">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p style="color: var(--text-secondary); max-width: 600px;">
            You have successfully logged in. This is your dashboard where you can manage your account and view your data.
        </p>
        <div style="margin-top: 2rem; padding: 2rem; background: var(--card-bg); border-radius: 1rem; border: 1px solid rgba(255,255,255,0.1);">
            <h3>Dashboard Content</h3>
            <p style="color: var(--text-secondary); margin-top: 1rem;">
                This is a placeholder for your application content.
            </p>
        </div>
    </main>

</body>
</html>

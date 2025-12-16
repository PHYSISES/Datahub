<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <style>
        /* ----- NAVBAR STYLING ----- */
        .navbar {
            width: 100%;
            background-color: #1e3a8a;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-family: Times New Roman, sans-serif;
        }

        .nav-left {
            display: flex;
            align-items: center;
        }

        .nav-left img {
            height: 60px; /* Logo size */
            display: block;
        }

        .nav-links {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            padding: 8px 12px;
            border-radius: 5px;
            transition: 0.2s;
        }

        .nav-links a:hover {
            background-color: #3b82f6; /* Lighter blue */
        }

        .logout-btn {
            background-color: #ef4444;
            padding: 8px 12px;
            border-radius: 5px;
        }

        .logout-btn:hover {
            background-color: #dc2626;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="nav-left">
            <!-- Logo -->
            <img src="charter_project.png" alt="CharterProject Logo">
        </div>

        <div class="nav-links">
            <a href="index.php">Home</a>
			<a href="about_us.php">About Us</a>

            <?php if (!isset($_SESSION['username'])): ?>
                <!-- Shown only if NOT logged in -->
                <a href="feedback.php">Customer Service</a>
                <a href="login.php">Login</a>
				
            <?php else: ?>
                <!-- Shown only when logged in -->
                <a href="admin_messages.php">Customer Messages</a>
				<a class="logout-btn" href="logout.php">Logout</a>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>

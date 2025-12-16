<?php
include 'db_connection.php';
session_start();

$message = '';

if (isset($_POST['login'])) {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password']; // Plain password user types in

    // Prepared statement: check if username + email exist
    $query = $conn->prepare("SELECT * FROM admin WHERE username = ? AND email = ?");
    $query->bind_param("ss", $username, $email);
    $query->execute();
    $result = $query->get_result();

    if ($result && $result->num_rows > 0) {

        $row = $result->fetch_assoc();

        // Verify the password against the hashed password in DB
        if (password_verify($password, $row['password'])) {

            // Login successful: store session data
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email'];

            header("Location: index.php");
            exit();
        } else {
            $message = "Incorrect password."; // password doesn't match hash
        }
    } else {
        $message = "Username and email do not match any account.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - CharterProject</title>
    <link rel="stylesheet" href="login.css">
</head>
<body class="login-page">

<div class="login-container">

    <img src="charter_project.png" alt="Logo" class="logo">

    <h2>Login</h2>

    <?php if($message != ''): ?>
        <div class="error-msg"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit" name="login">Log In</button>
    </form>
    <form action="index.php" method="get">
        <button type="submit" class="return-home-btn">Return Home</button>
    </form>

</div>

</body>
</html>

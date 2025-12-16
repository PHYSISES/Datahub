<?php
include 'db_connection.php';
session_start();

$message = "";

// Handle form submission
if (isset($_POST['submit'])) {
    $fullname = trim($_POST['fullname']);
    $contact_no = trim($_POST['contact_no']);
    $email = trim($_POST['email']);
    $comment = trim($_POST['comment']);

    // Ensure Contact No is numeric
    if (!preg_match("/^[0-9]+$/", $contact_no)) {
        $message = "❌ Contact number must contain numbers only.";
    } else {
        // Insert into database (columns match your table)
        $stmt = $conn->prepare("INSERT INTO feedback (name, contact_no, email, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $fullname, $contact_no, $email, $comment);

        if ($stmt->execute()) {
            // Redirect to home page after submission
            header("Location: index.php");
            exit();
        } else {
            $message = "❌ There was an error submitting your feedback. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Service - CharterProject</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('opisina.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .feedback-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
            color: #000;
        }

        /* Logo */
        .feedback-container img.logo {
            width: 140px;
            margin-bottom: 20px;
        }

        .feedback-container h1 {
            font-size: 26px;
            margin-bottom: 20px;
        }

        .feedback-container input, 
        .feedback-container textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 16px;
            box-sizing: border-box;
        }

        .feedback-container textarea {
            resize: vertical;
        }

        .feedback-container button {
            width: 100%;
            padding: 12px;
            background-color: #162f6f; /* dark Facebook-style blue */
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .feedback-container button:hover {
            background-color: #0f1f4f;
            transform: scale(1.02);
        }

        .return-home {
            display: block;
            margin-top: 15px;
            font-size: 14px;
            color: #162f6f;
            text-decoration: none;
        }

        .return-home:hover {
            text-decoration: underline;
        }

        .error-msg {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="feedback-container">
    <!-- Logo -->
    <img src="charter_project.png" alt="Logo" class="logo">

    <h1>Customer Service</h1>

    <?php if($message != ''): ?>
        <div class="error-msg"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="fullname" placeholder="Full Name" required>
        <input type="text" name="contact_no" placeholder="Contact No." required pattern="[0-9]+" title="Numbers only">
        <input type="email" name="email" placeholder="E-Mail" required>
        <textarea name="comment" rows="5" placeholder="Comment" required></textarea>
        <button type="submit" name="submit">Submit</button>
    </form>

    <a class="return-home" href="index.php">Return Home</a>
</div>

</body>
</html>

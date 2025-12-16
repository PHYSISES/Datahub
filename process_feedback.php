<?php
// Database connection
$servername = "localhost";
$username = "root"; 
$password = ""; 
$database = "charterproject"; 

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$full_name = $_POST['full_name'];
$contact_no = $_POST['contact_no'];
$email = $_POST['email'];
$message = $_POST['message'];

// Insert into database
$sql = "INSERT INTO feedback (full_name, contact_no, email, message)
        VALUES ('$full_name', '$contact_no', '$email', '$message')";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Feedback submitted successfully!'); window.location.href='feedback.php';</script>";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>

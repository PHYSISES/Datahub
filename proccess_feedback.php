<?php
include 'db_connection.php';

$name = $_POST['name'];
$email = $_POST['email'];
$message = $_POST['message'];

$stmt = $conn->prepare("INSERT INTO feedback (name, email, message) VALUES (?,?,?)");
$stmt->bind_param("sss", $name, $email, $message);
$stmt->execute();

header("Location: feedback.php?success=1");
exit();

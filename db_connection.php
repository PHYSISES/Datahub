<?php
// Database connection configuration
$servername = "localhost";
$username = "root";        
$password = "";           
$dbname = "charterproject_db";  

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("<h3 style='color:red; text-align:center; margin-top:50px;'>
         Database Connection Failed: " . $conn->connect_error . "
    </h3>");
}

$conn->set_charset("utf8mb4");
?>

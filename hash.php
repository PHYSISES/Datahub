<?php
// The password you want to hash
$password = "admin123";

// Generate the hash
$hash = password_hash($password, PASSWORD_DEFAULT);

// Output the hash
echo "Password: $password<br>";
echo "Hash: <textarea cols='100' rows='3'>$hash</textarea>";
?>

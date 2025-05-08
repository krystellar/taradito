<?php
include('db_connection.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$email = 'test@example.com';
$plainPassword = 'admin123';
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

$conn->execute_query(
    "INSERT INTO managerData (managerEmail, managerPass) VALUES (?, ?)",
    [$email, $hashedPassword]
);

echo "Test user created.";
?>

<?php
include('db_connection.php');

$email = 'admin15@gmail.com';
$plainPassword = 'admin123';

$hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

$conn->execute_query(
    "UPDATE userData SET userPass = ? WHERE userEmail = ?",
    [$hashedPassword, $email]
);

echo "Password updated successfully.";
?>

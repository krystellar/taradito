<?php
include('db_connection.php');

$email = 'admin14@gmail.com';
$plainPassword = 'admin123';

$hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

$conn->execute_query(
    "UPDATE managerData SET managerPass = ? WHERE managerEmail = ?",
    [$hashedPassword, $email]
);

echo "Password updated successfully.";
?>

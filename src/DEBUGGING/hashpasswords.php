<?php
include('db_connection.php');

$email = 'superadmin@gmail.com';
$plainPassword = 'superadmin';

$hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

$conn->execute_query(
    "UPDATE adminData SET adminPass = ? WHERE adminEmail = ?",
    [$hashedPassword, $email]
);

echo "Password updated successfully.";
?>

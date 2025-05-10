<?php
include('db_connection.php');

$email = 'johndoe@gmail.com';
$plainPassword = 'user123';

$hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

$conn->execute_query(
    "UPDATE userData SET userPass = ? WHERE userEmail = ?",
    [$hashedPassword, $email]
);

echo "Password updated successfully.";
?>

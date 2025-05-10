<?php
session_start();
include('db_connection.php');
if (!isset($_SESSION['userID'])) {
    echo "User not logged in.";
    exit;
}
$userID = $_SESSION['userID'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $userEmail = $_POST['userEmail'];

    $updateSql = "UPDATE userData SET firstName = ?, lastName = ?, userEmail = ? WHERE userID = ?";
    if ($stmt = $conn->prepare($updateSql)) {
        $stmt->bind_param("sssi", $firstName, $lastName, $userEmail, $userID);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Profile updated successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Failed to update the profile. Please try again.";
            $_SESSION['message_type'] = "error";
        }

        $stmt->close();
    } else {
        $_SESSION['message'] = "Error preparing the update query.";
        $_SESSION['message_type'] = "error";
    }

    header("Location: Dashboard.php");
    exit;
}
?>

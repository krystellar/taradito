<?php
    session_start();
    include('db_connection.php');

    if (!isset($_SESSION['managerID'])) {
        echo "Manager not logged in.";
        exit;
    }
    $managerID = $_SESSION['managerID'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_manager'])) {
        // get values from form
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $managerEmail = $_POST['managerEmail'];
        $managerAbout = $_POST['managerAbout'];

        // query to update the data
        $updateSql = "UPDATE managerData 
                      SET firstName = ?, lastName = ?, managerEmail = ?, managerAbout = ?
                      WHERE managerID = ?";
        $updateResult = $conn->execute_query($updateSql, [$firstName, $lastName, $managerEmail, $managerAbout, $managerID]);

        if ($updateResult) {
            $_SESSION['message'] = "Profile updated successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Failed to update the profile. Please try again.";
            $_SESSION['message_type'] = "error";
        }

        header("Location: DashboardAdmin.php");
        exit;
    }
?>

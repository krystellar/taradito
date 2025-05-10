<?php
    session_start();
    require 'db_connection.php';

    if (!isset($_SESSION['userID'], $_POST['venueID'])) {
        header("Location: Login_user.php");
        exit;
    }

    $userID = $_SESSION['userID'];
    $venueID = $_POST['venueID'];

    //check
    $stmt = $conn->prepare("SELECT 1 FROM userwishlist WHERE userID = ? AND venueID = ?");
    $stmt->bind_param("ii", $userID, $venueID);
    $stmt->execute();
    $exists = $stmt->get_result()->num_rows > 0;

    if ($exists) {
        $stmt = $conn->prepare("DELETE FROM userwishlist WHERE userID = ? AND venueID = ?");
    } else {
        $stmt = $conn->prepare("INSERT INTO userwishlist (userID, venueID) VALUES (?, ?)");
    }
    $stmt->bind_param("ii", $userID, $venueID);
    $stmt->execute();

    header("Location: " . $_SERVER['HTTP_REFERER']);
?>
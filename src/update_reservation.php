<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $reservationID = $_POST['reservationID'];
    $userID = $_POST['userID'];
    $startDate = $_POST['startDate'];
    $startTime = $_POST['startTime'];
    $endDate = $_POST['endDate'];
    $endTime = $_POST['endTime'];

    if (strtotime($endDate) < strtotime($startDate)) {
        die("End date cannot be earlier than the start date.");
    }

    if ($startDate === $endDate && strtotime($endTime) < strtotime($startTime)) {
        die("End time cannot be earlier than the start time on the same day.");
    }

    $stmt = $conn->prepare("UPDATE userreserved SET startDate = ?, startTime = ?, endDate = ?, endTime = ? WHERE reservationID = ? AND userID = ?");
    $stmt->bind_param("ssssii", $startDate, $startTime, $endDate, $endTime, $reservationID, $userID);

    if ($stmt->execute()) {
        $_SESSION['reservation_updated'] = true; 
        header("Location: Dashboard.php");
        exit;
    } else {
        echo "<script>
            alert('Error: " . $stmt->error . "');
            window.location.href = 'Dashboard.php';
        </script>";
    }

    $stmt->close();
    $conn->close();
}
?>

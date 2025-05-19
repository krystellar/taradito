<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $reservationID = $_POST['reservationID'];
    $userID = $_POST['userID'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $redirectURL = "dashboard.php";

    if (strtotime($endDate) < strtotime($startDate)) {
        echo "<script>
            alert('End time cannot be earlier than the start day.');
            window.location.href = '$redirectURL';
        </script>";
        exit;
    }

    if ($startDate === $endDate && strtotime($endTime) < strtotime($startTime)) {
        echo "<script>
            alert('End time cannot be earlier than the start time on the same day.');
            window.location.href = '$redirectURL';
        </script>";
        exit;
    }

    $stmt = $conn->prepare("UPDATE userreserved SET startDate = ?, endDate = ? WHERE reservationID = ? AND userID = ?");
    $stmt->bind_param("ssii", $startDate, $endDate, $reservationID, $userID);

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

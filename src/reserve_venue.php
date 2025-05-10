<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $venueID = $_POST['venueID'];
    $userID = $_POST['userID'];
    $startDate = $_POST['startDate'];
    $startTime = $_POST['startTime'];
    $endDate = $_POST['endDate'];
    $endTime = $_POST['endTime'];
    $statusID = 1;

    $redirectURL = "listing.php?id=" . urlencode($venueID);

    if (!$venueID || !$userID || !$startDate || !$startTime || !$endDate || !$endTime) {
        echo "<script>
            alert('Missing required fields.');
            window.location.href = '$redirectURL';
        </script>";
        exit;
    }

    if (strtotime($endDate) < strtotime($startDate)) {
        echo "<script>
            alert('End date cannot be earlier than the start date.');
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

    $stmt = $conn->prepare("
        INSERT INTO userreserved (userID, venueID, statusID, startDate, startTime, endDate, endTime)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iisssss", $userID, $venueID, $statusID, $startDate, $startTime, $endDate, $endTime);

    if ($stmt->execute()) {
        $_SESSION['reservation_success'] = true;
        header("Location: listing.php?id=$venueID");
        exit;
    } else {
        $error = $stmt->error;
        echo "<script>
            alert('Reservation failed: $error');
            window.location.href = '$redirectURL';
        </script>";
    }

    $stmt->close();
    $conn->close();
}
?>

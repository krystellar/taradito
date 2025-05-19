<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $facilityID = $_POST['facilityID'];
    $userID = $_POST['userID'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $statusID = 1;

    $venueID = null;
    if ($stmtVenue = $conn->prepare("SELECT venueID FROM venuefacilities WHERE facilityID = ?")) {
        $stmtVenue->bind_param("i", $facilityID);
        $stmtVenue->execute();
        $stmtVenue->bind_result($venueID);
        $stmtVenue->fetch();
        $stmtVenue->close();
    }

    $redirectURL = "listing.php?id=" . urlencode($venueID);

    if (!$facilityID || !$userID || !$startDate || !$endDate) {
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

    $stmt = $conn->prepare("
        INSERT INTO userreserved (userID, facilityID, statusID, startDate, endDate)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiiss", $userID, $facilityID, $statusID, $startDate, $endDate);

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

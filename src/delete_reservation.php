<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $reservationID = $_POST['reservationID'];

    $stmt = $conn->prepare("DELETE FROM userreserved WHERE reservationID = ?");
    $stmt->bind_param("i", $reservationID);

    if ($stmt->execute()) {
        $_SESSION['reservation_success'] = "Reservation deleted successfully.";
    } else {
        $_SESSION['reservation_error'] = "Error deleting reservation: " . $stmt->error;
    }

    header("Location: Dashboard.php");
    exit;
}
?>

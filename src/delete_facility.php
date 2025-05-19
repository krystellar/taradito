<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $venueID = $_POST['venueID'];
    $facilityID = $_POST['facilityID'];

    $stmt = $conn->prepare("DELETE FROM venuefacilities WHERE venueID = ? AND facilityID = ?");
    $stmt->bind_param("ii", $venueID, $facilityID);
    $stmt->execute();

    header("Location: venue_editing.php?id=" . $venueID);
    exit;
}
?>

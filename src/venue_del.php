<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['adminID'])) {
    header("Location: Login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['venueID'])) {
    $venueID = (int) $_POST['venueID'];

    // Step 1: Get managerID from managerVenue
    $stmt = $conn->prepare("SELECT managerID FROM managerVenue WHERE venueID = ?");
    $stmt->bind_param("i", $venueID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $managerID = $row['managerID'];

        // Step 2: Delete from managerVenue
        $delManagerVenue = $conn->prepare("DELETE FROM managerVenue WHERE venueID = ?");
        $delManagerVenue->bind_param("i", $venueID);
        $delManagerVenue->execute();

        // Step 3: Delete from venueData
        $delVenueData = $conn->prepare("DELETE FROM venueData WHERE venueID = ?");
        $delVenueData->bind_param("i", $venueID);
        if ($delVenueData->execute()) {
            header("Location: superadmin.php");
            exit;
        } else {
            echo "Error deleting venue from venueData: " . $conn->error;
        }
    } else {
        echo "No manager found for this venue or venue does not exist.";
    }
} else {
    echo "Invalid request.";
}
?>

<?php
    session_start();
    include('db_connection.php');

    if (!isset($_SESSION['managerID'])) {
        header("Location: Login.php");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['venueID'])) {
        $venueID = (int) $_POST['venueID'];
        $managerID = $_SESSION['managerID'];
        $checkOwnership = $conn->prepare("SELECT * FROM managerVenue WHERE venueID = ? AND managerID = ?");
        $checkOwnership->bind_param("ii", $venueID, $managerID);
        $checkOwnership->execute();
        $result = $checkOwnership->get_result();

        if ($result->num_rows > 0) {
            //delete from managervenue first
            $conn->query("DELETE FROM managerVenue WHERE venueID = $venueID");

            // then delete from venuedata
            if ($conn->query("DELETE FROM venueData WHERE venueID = $venueID")) {
                header("Location: DashboardAdmin.php?deleted=1");
                exit;
            } else {
                echo "Error deleting venue: " . $conn->error;
            }
        } else {
            echo "You are not authorized to delete this venue.";
        }
    } else {
        echo "Invalid request.";
    }
?>

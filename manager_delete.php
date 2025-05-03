<?php
    include('db_connection.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $venuID = $_POST['venueID'];
        $deleteQuery = "DELETE FROM venueData WHERE venueID = '$venueID'";

        try {
            $conn->query($deleteQuery);
            echo "<script>
                alert('Record with Venue Code $venueID deleted successfully.');
                window.location.href = 'index.php';
            </script>";
        } catch (mysqli_sql_exception $e) {
            echo "<script>
                alert('An error occurred while trying to delete the record: " . $e->getMessage() . "');
                window.location.href = 'index.php';
            </script>";
        }
    } else {
        header("Location: index.php");
        exit();
    }
?>

<?php
require 'db_connection.php';
session_start();

if (!isset($_SESSION['managerID'])) {
    header('HTTP/1.1 403 Forbidden');
    exit('Access denied.');
}

$managerID = $_SESSION['managerID'];

// 1. Fetch Monthly Reservations per Venue
$sql1 = "
    SELECT v.venueName, DATE_FORMAT(ur.reservationDate, '%Y-%m') AS month, COUNT(*) AS totalReservations
    FROM userReserved ur
    JOIN venueData v ON ur.venueID = v.venueID
    JOIN managerVenue mv ON v.venueID = mv.venueID
    WHERE mv.managerID = ?
    GROUP BY month, v.venueID
    ORDER BY month DESC
";

$stmt1 = $conn->prepare($sql1);
$stmt1->bind_param("i", $managerID);
$stmt1->execute();
$reservations = $stmt1->get_result()->fetch_all(MYSQLI_ASSOC);

// 2. Fetch Views and Likes per Venue
$sql2 = "
    SELECT v.venueName,
           COALESCE(COUNT(DISTINCT uh.historyID), 0) AS totalViews,
           COALESCE(COUNT(DISTINCT ul.userID), 0) AS totalLikes
    FROM venueData v
    JOIN managerVenue mv ON v.venueID = mv.venueID
    LEFT JOIN userhistory uh ON v.venueID = uh.venueID
    LEFT JOIN userliked ul ON v.venueID = ul.venueID
    WHERE mv.managerID = ?
    GROUP BY v.venueID
";

$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("i", $managerID);
$stmt2->execute();
$viewsLikes = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

// Set CSV headers
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="venue_analytics.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Write header for Monthly Reservations section
fputcsv($output, ['Monthly Reservations per Venue']);
fputcsv($output, ['Month', 'Venue Name', 'Total Reservations']);

foreach ($reservations as $row) {
    fputcsv($output, [$row['month'], $row['venueName'], $row['totalReservations']]);
}

fputcsv($output, []); // blank line between sections

// Write header for Views and Likes section
fputcsv($output, ['Venue Views and Likes']);
fputcsv($output, ['Venue Name', 'Total Views', 'Total Likes']);

foreach ($viewsLikes as $row) {
    fputcsv($output, [$row['venueName'], $row['totalViews'], $row['totalLikes']]);
}

fclose($output);
exit;

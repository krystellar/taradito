<?php
require 'db_connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$managerID = $_SESSION['managerID'] ?? null;

if (!$managerID) {
    echo "Access denied.";
    exit;
}

// 1. Monthly Reservations per Venue (FILTERED BY MANAGER)
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
$result1 = $stmt1->get_result()->fetch_all(MYSQLI_ASSOC);

// Grouping result by month
$monthlyReservations = [];
foreach ($result1 as $row) {
    $monthlyReservations[$row['month']][] = [
        'venueName' => $row['venueName'],
        'totalReservations' => $row['totalReservations']
    ];
}

// 2. Venue Views and Likes (FILTERED BY MANAGER)
$sql2 = "
    SELECT v.venueName,
           COALESCE(SUM(vv.viewCount), 0) AS totalViews,
           COALESCE(SUM(vl.likeCount), 0) AS totalLikes
    FROM venueData v
    JOIN managerVenue mv ON v.venueID = mv.venueID
    LEFT JOIN venue_views vv ON v.venueID = vv.venueID
    LEFT JOIN venue_likes vl ON v.venueID = vl.venueID
    WHERE mv.managerID = ?
    GROUP BY v.venueID
";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("i", $managerID);
$stmt2->execute();
$viewsAndLikes = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
?>

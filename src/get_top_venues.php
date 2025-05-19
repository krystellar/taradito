<?php
include('db_connection.php');
// Query to get top 5 venues with the most reservations
$query = "
    SELECT 
        v.venueName,
        COUNT(ur.reservationID) AS totalReservations
    FROM userreserved ur
    JOIN venueFacilities vf ON ur.facilityID = vf.facilityID
    JOIN venuedata v ON vf.venueID = v.venueID
    GROUP BY v.venueID
    ORDER BY totalReservations DESC
    LIMIT 5
";

$result = $conn->query($query);

if (!$result) {
    die("Query error: " . $conn->error);
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'venueName' => $row['venueName'],
        'totalReservations' => (int) $row['totalReservations']
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
?>

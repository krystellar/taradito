<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "taradito";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get top 5 venues with the most reservations
$query = "
    SELECT 
        v.venueName,
        COUNT(ur.reservationID) AS totalReservations
    FROM venuedata v
    JOIN userreserved ur ON v.venueID = ur.venueID
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

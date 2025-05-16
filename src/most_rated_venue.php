<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "taradito";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Updated query to use 'userratings' instead of 'userreserved'
$query = "
    SELECT 
        v.venueName,
        AVG(ur.rating) AS avgRating,
        COUNT(ur.rating) AS ratingCount
    FROM venuedata v
    JOIN userratings ur ON v.venueID = ur.venueID
    WHERE ur.rating IS NOT NULL
    GROUP BY v.venueID
    HAVING ratingCount > 0
    ORDER BY avgRating DESC
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
        'avgRating' => round($row['avgRating'], 2),
        'ratingCount' => (int)$row['ratingCount']
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
?>

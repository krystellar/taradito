<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "taradito";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get top 5 venues by total likes from userliked table
$query = "
    SELECT 
        v.venueName,
        COUNT(ul.venueID) AS totalLikes
    FROM venuedata v
    JOIN userliked ul ON v.venueID = ul.venueID
    GROUP BY v.venueID
    HAVING totalLikes > 0
    ORDER BY totalLikes DESC
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
        'totalLikes' => (int)$row['totalLikes']
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
?>

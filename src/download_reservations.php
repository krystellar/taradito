<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "taradito";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$managerID = $_SESSION['managerID'] ?? null;
$userID = $_SESSION['userID'] ?? null;

if (!$managerID && !$userID) {
    die("Unauthorized: No user or manager logged in.");
}

$selectClause = "SELECT 
    u.firstName, u.lastName,
    v.venueName, v.cityAddress,
    ur.startDate, ur.endDate,
    TIMESTAMPDIFF(DAY, ur.startDate, ur.endDate) AS nights,
    pr.priceRangeText, rs.statusText
    FROM userdata u
    JOIN userreserved ur ON u.userID = ur.userID
    JOIN venuedata v ON ur.venueID = v.venueID
    JOIN pricerange pr ON v.priceRangeID = pr.priceRangeID
    JOIN reservationstatus rs ON ur.statusID = rs.statusID";

if ($managerID) {
    $query = $selectClause . "
        JOIN managervenue mv ON v.venueID = mv.venueID
        WHERE mv.managerID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $managerID);
} elseif ($userID) {
    $query = $selectClause . " WHERE u.userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userID);
}

$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Query failed: " . $conn->error);
}

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="reservations.csv"');

$output = fopen('php://output', 'w');

fputcsv($output, [
    'First Name', 'Last Name', 'Venue', 'Location',
    'Start Date', 'End Date', 'Duration (nights)',
    'Price Range', 'Status'
]);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['firstName'],
        $row['lastName'],
        $row['venueName'],
        $row['cityAddress'],
        $row['startDate'],
        $row['endDate'],
        $row['nights'],
        $row['priceRangeText'],
        $row['statusText']
    ]);
}

fclose($output);
exit;

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Function</title>
</head>
<body>
    <form method="GET" action="">
        <input type="text" name="query" placeholder="Search for a venue..." required>
        <button type="submit">Search</button>
    </form>
</body>
</html>

<?php
include('db_connection.php');

if (isset($_GET['query'])) {
    $searchTerm = $conn->real_escape_string($_GET['query']);

    $sql = "SELECT * FROM venueData WHERE venueName LIKE '%$searchTerm%'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h3>Search Results:</h3>";
        while ($row = $result->fetch_assoc()) {
            echo "<p><strong>{$row['venueName']}</strong> - {$row['cityAddress']}</p>";
        }
    } else {
        echo "<p>No results found.</p>";
    }
}
?>

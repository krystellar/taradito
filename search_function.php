<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Function</title>
</head>
<body>
    <!-- Search Bar -->
    <div class="w-full bg-white py-4 shadow-sm">
        <div class="flex justify-center">
            <form method="GET" action="" class="form-container">


            <input 
                type="text" 
                name="query" 
                placeholder="Search for a venue..." 
                value="<?= htmlspecialchars($searchTerm) ?>"
                class="w-full px-3 py-1.5 text-sm text-gray-800 placeholder-gray-500 focus:outline-none" 
                required>
            
            <button type="submit" 
                    class="bg-[#ff385c] text-white rounded-full p-2 hover:bg-[#e03154] transition ml-2">
                <i class="fas fa-search text-sm"></i>
            </button>
            </form>
        </div>
    </div>
</body>
</html>

<?php
include('db_connection.php');

$searchTerm = isset($_GET['query']) ? $conn->real_escape_string($_GET['query']) : '';
if (isset($_GET['query'])) {
    $searchTerm = $conn->real_escape_string($_GET['query']);

    $sql = "SELECT v.*, p.priceRangeText, m.firstName, m.lastName
          FROM venueData v
          LEFT JOIN priceRange p ON v.priceRangeID = p.priceRangeID
          LEFT JOIN managerData m ON v.managerID = m.managerID";
    $wheres = [];
    // For the search terms
    if ($searchTerm !== '') {
        $wheres[] = "v.venueName LIKE '%" . $conn->real_escape_string($searchTerm) . "%'";
    }
}
?>

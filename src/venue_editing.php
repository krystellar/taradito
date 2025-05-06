<?php
include('db_connection.php');

$venueID = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT v.*, p.priceRangeText, m.firstName, m.lastName
        FROM venueData v
        LEFT JOIN priceRange p ON v.priceRangeID = p.priceRangeID
        LEFT JOIN managerData m ON v.managerID = m.managerID
        WHERE v.venueID = ?";

$result = $conn->execute_query($sql, [$venueID]);
$venue = $result->fetch_assoc();
$conn->close();

if (!$venue) {
    echo "<p class='text-center text-red-600 mt-10'>Venue not found.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit <?= htmlspecialchars($venue['venueName']) ?> | Design Stays</title>
    <link href="output.css" rel="stylesheet">
</head>
<body class="bg-gray-50">

<!-- Navbar -->
<header id="navbar" class="w-full sticky top-0 z-50 transition-colors duration-300 bg-[#dbeafe] shadow-sm">
  <nav class="max-w-[1320px] mx-auto flex items-center justify-between py-6 px-4 md:px-12">
    <a href="#" class="flex items-center gap-2 absolute left-10 pl-4">
      <img src="Images/Logo/LogoNav.png" alt="TaraDito Logo" class="h-[60px] w-auto" />
    </a>
    <ul class="flex gap-6 md:gap-8 flex-grow justify-center">
      <li><a href="#" class="text-lg font-medium text-gray-800 hover:text-white hover:bg-[#a0c4ff] py-2 px-4 rounded-full transition-all duration-300">Home</a></li>
      <li><a href="product.php" class="text-lg font-medium text-gray-800 hover:text-white hover:bg-[#a0c4ff] py-2 px-4 rounded-full transition-all duration-300">Venues</a></li>
      <li><a href="#" class="text-lg font-medium text-gray-800 hover:text-white hover:bg-[#a0c4ff] py-2 px-4 rounded-full transition-all duration-300">Explore</a></li>
      <li><a href="#" class="text-lg font-medium text-gray-800 hover:text-white hover:bg-[#a0c4ff] py-2 px-4 rounded-full transition-all duration-300">Contact</a></li>
    </ul>
  </nav>
</header>

<div class="max-w-6xl mx-auto p-6">
  <h1 class="text-3xl font-bold mb-4">Edit Venue: <?= htmlspecialchars($venue['venueName']) ?></h1>

  <!-- editing venue -->
  <form action="update_venue.php" method="POST" class="space-y-6">
    <input type="hidden" name="venueID" value="<?= htmlspecialchars($venue['venueID']) ?>">

    <!-- name -->
    <div>
      <label for="venueName" class="block font-medium text-gray-700">Venue Name</label>
      <input type="text" name="venueName" id="venueName" value="<?= htmlspecialchars($venue['venueName']) ?>" class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-400" required>
    </div>

    <!-- barangay and city address -->
    <div>
      <label for="barangayAddress" class="block font-medium text-gray-700">Barangay Address</label>
      <input type="text" name="barangayAddress" id="barangayAddress" value="<?= htmlspecialchars($venue['barangayAddress']) ?>" class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-400" required>
    </div>
    
    <div>
      <label for="cityAddress" class="block font-medium text-gray-700">City Address</label>
      <input type="text" name="cityAddress" id="cityAddress" value="<?= htmlspecialchars($venue['cityAddress']) ?>" class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-400" required>
    </div>

    <!-- venue description -->
    <div>
      <label for="venueDesc" class="block font-medium text-gray-700">Venue Description</label>
      <textarea name="venueDesc" id="venueDesc" rows="4" class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-400"><?= htmlspecialchars($venue['venueDesc']) ?></textarea>
    </div>

    <!-- img URLs -->
    <div>
      <label for="imgs" class="block font-medium text-gray-700">Main Image URL</label>
      <input type="url" name="imgs" id="imgs" value="<?= htmlspecialchars($venue['imgs']) ?>" class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-400" required>
    </div>

    <div>
      <label for="img2" class="block font-medium text-gray-700">Second Image URL</label>
      <input type="url" name="img2" id="img2" value="<?= htmlspecialchars($venue['img2']) ?>" class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-400">
    </div>

    <div>
      <label for="img3" class="block font-medium text-gray-700">Third Image URL</label>
      <input type="url" name="img3" id="img3" value="<?= htmlspecialchars($venue['img3']) ?>" class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-400">
    </div>

    <div>
      <label for="img4" class="block font-medium text-gray-700">Fourth Image URL</label>
      <input type="url" name="img4" id="img4" value="<?= htmlspecialchars($venue['img4']) ?>" class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-400">
    </div>

    <div>
      <label for="img5" class="block font-medium text-gray-700">Fifth Image URL</label>
      <input type="url" name="img5" id="img5" value="<?= htmlspecialchars($venue['img5']) ?>" class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-400">
    </div>

    <!-- amenities -->
    <div>
      <p class="font-medium text-gray-700">Select Amenities:</p>
      <div class="grid grid-cols-2 gap-4">
        <label class="flex items-center">
          <input type="checkbox" name="eventPlanner" <?= $venue['eventPlanner'] ? 'checked' : '' ?> class="form-checkbox h-5 w-5 text-blue-500">
          <span class="ml-2">Event Planner</span>
        </label>
        <label class="flex items-center">
          <input type="checkbox" name="equipRentals" <?= $venue['equipRentals'] ? 'checked' : '' ?> class="form-checkbox h-5 w-5 text-blue-500">
          <span class="ml-2">Equipment Rentals</span>
        </label>
        <label class="flex items-center">
          <input type="checkbox" name="decoServices" <?= $venue['decoServices'] ? 'checked' : '' ?> class="form-checkbox h-5 w-5 text-blue-500">
          <span class="ml-2">Decoration Services</span>
        </label>
        <label class="flex items-center">
          <input type="checkbox" name="onsiteStaff" <?= $venue['onsiteStaff'] ? 'checked' : '' ?> class="form-checkbox h-5 w-5 text-blue-500">
          <span class="ml-2">On-site Staff</span>
        </label>
        <label class="flex items-center">
          <input type="checkbox" name="techSupport" <?= $venue['techSupport'] ? 'checked' : '' ?> class="form-checkbox h-5 w-5 text-blue-500">
          <span class="ml-2">Tech Support</span>
        </label>
      </div>
    </div>

    <!-- Submit Button -->
    <div class="mt-6">
    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-all duration-300 w-full sm:w-auto"
        style="position: relative; z-index: 999;">
        Save Changes </button>

    </div>
  </form>
</div>

</body>
</html>

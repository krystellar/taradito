<?php
include('db_connection.php');

// 1. Get the venue ID from URL
$venueID = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. Fetch venue details
$sql = "SELECT v.*, p.priceRangeText, m.firstName, m.lastName
        FROM venueData v
        LEFT JOIN priceRange p ON v.priceRangeID = p.priceRangeID
        LEFT JOIN managerData m ON v.managerID = m.managerID
        WHERE v.venueID = ?";

// Execute query using prepared statement
$result = $conn->execute_query($sql, [$venueID]);
$venue = $result->fetch_assoc();
$conn->close();

// If no venue found, show message and stop
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
  <title><?= htmlspecialchars($venue['venueName']) ?> | Design Stays</title>
  <link href="output.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
</head>
<body class="bg-gray-50">

<!-- Navbar -->
<header id="navbar" class="w-full sticky top-0 z-50 transition-colors duration-300 bg-[#dbeafe] shadow-sm" >
  <nav class="max-w-[1320px] mx-auto flex items-center justify-between py-6 px-4 md:px-12">
    <!-- Logo on the left, using absolute positioning -->
    <a href="#" class="flex items-center gap-2 absolute left-10 pl-4">
      <img src="Images/Logo/LogoNav.png" alt="TaraDito Logo" class="h-[60px] w-auto" /> <!-- Increase logo size if needed -->
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
  <h1 id="villa-title" class="text-3xl font-bold mb-4"><?= htmlspecialchars($venue['venueName']) ?></h1>

  <!-- Image Gallery -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <img src="<?= htmlspecialchars($venue['imgs'] ?? 'https://source.unsplash.com/800x600/?villa')?>" alt="Main Image" class="rounded-lg w-full object-cover h-80" />
    <div class="grid grid-cols-2 gap-2">
      <?php if (!empty($venue['img2'])): ?><img src="<?= htmlspecialchars($venue['img2']) ?>" class="rounded-md" /><?php endif; ?>
      <?php if (!empty($venue['img3'])): ?><img src="<?= htmlspecialchars($venue['img3']) ?>" class="rounded-md" /><?php endif; ?>
      <?php if (!empty($venue['img4'])): ?><img src="<?= htmlspecialchars($venue['img4']) ?>" class="rounded-md" /><?php endif; ?>
      <?php if (!empty($venue['img5'])): ?><img src="<?= htmlspecialchars($venue['img5']) ?>" class="rounded-md" /><?php endif; ?>
    </div>
  </div>

  <p id="villa-location" class="text-gray-700 text-lg mb-2">
  <?= htmlspecialchars($venue['barangayAddress']) . ', ' . htmlspecialchars($venue['cityAddress']) ?>
  </p>
  <p id="villa-specs" class="text-sm mb-1">Capacity: <?= htmlspecialchars($venue['maxCapacity']) ?> guests</p>
  <p id="villa-rating" class="mb-2 text-yellow-600">⭐ <?= number_format($venue['rating'] ?? 0, 2) ?></p>
  <p id="villa-price" class="text-xl font-semibold text-pink-600">₱<?= htmlspecialchars($venue['priceRangeText']) ?></p>

  <!-- Villa Description -->
  <div class="mt-10 border-t pt-6">
    <h2 class="text-xl font-semibold mb-2">About this place</h2>
    <p class="text-gray-700 leading-relaxed">
      <?= htmlspecialchars($venue['venueDesc']) ?>
    </p>
  </div>

  <!-- Amenities -->
  <h2 class="text-xl font-semibold mb-2">Services & Amenities</h2>
  <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
    <?php if ($venue['eventPlanner']): ?><div><p class="font-medium">Event Planner</p></div><?php endif; ?>
    <?php if ($venue['equipRentals']): ?><div><p class="font-medium">Equipment Rentals</p></div><?php endif; ?>
    <?php if ($venue['decoServices']): ?><div><p class="font-medium">Decoration Services</p></div><?php endif; ?>
    <?php if ($venue['onsiteStaff']): ?><div><p class="font-medium">On-site Staff</p></div><?php endif; ?>
    <?php if ($venue['techSupport']): ?><div><p class="font-medium">Tech Support</p></div><?php endif; ?>
  </div>

  <!-- Map -->
  <section class="mt-10">
    <h2 class="text-2xl font-bold mb-4">Where you'll be</h2>
    <div id="map" style="height:400px;" class="w-full rounded-lg shadow-md"></div>
  </section>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const coords = [
        <?= floatval($venue['latitude'] ?? 0) ?>,
        <?= floatval($venue['longitude'] ?? 0) ?>
      ];
      const map = L.map('map').setView(coords, 14);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(map);
      L.marker(coords).addTo(map).bindPopup('<?= htmlspecialchars($venue['venueName']) ?>').openPopup();
    });
  </script>

</body>
</html>

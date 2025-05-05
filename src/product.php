<?php
  include('db_connection.php');

  // Search feature
  $searchTerm = isset($_GET['query']) ? $conn->real_escape_string($_GET['query']) : '';
  $selectedCategory = isset($_GET['category']) ? (array)$_GET['category'] : [];
  $validCategory = ['intimate','business','fun','casual'];
  // Filter feature 
  $cats = array_values(array_intersect($validCategory, $selectedCategory));

  // Editing SQL query to fetch venues with the category
  $sql = "SELECT v.*, p.priceRangeText, m.firstName, m.lastName
          FROM venueData v
          LEFT JOIN priceRange p ON v.priceRangeID = p.priceRangeID
          LEFT JOIN managerData m ON v.managerID = m.managerID";
  $wheres = [];
  // For the search terms
  if ($searchTerm !== '') {
      $wheres[] = "v.venueName LIKE '%" . $conn->real_escape_string($searchTerm) . "%'";
  }
  // For the selected categories (AND operation)
  if (count($cats) > 0) {
    foreach ($cats as $c) {
        $wheres[] = "v.$c = 1";
    }
  }
  if (count($wheres) > 0) {
    $sql .= ' WHERE ' . implode(' AND ', $wheres);
  }

  $sql .= ' ORDER BY v.venueID ASC';
  $result = $conn->query($sql);

  // For toggling category links
  function toggleCatLink(string $category): string {
      $qs = [];
      if (!empty($_GET['query'])) {
          $qs[] = 'query=' . urlencode($_GET['query']);
      }
      $cur = isset($_GET['category']) ? (array)$_GET['category'] : [];
      if (in_array($category, $cur, true)) {
          $new = array_diff($cur, [$category]);
      } else {
          $new = array_merge($cur, [$category]);
      }
      foreach ($new as $c) {
          $qs[] = 'category[]=' . urlencode($c);
      }
      return '?' . implode('&', $qs);
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Design Stays</title>
  <link href="output.css" rel="stylesheet">
  <link href="listing.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Roboto:wght@400;500&family=Nunito:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

	<script defer src="second.js"></script>
  <style>
 
</style>
  
</head>
<body class="bg-white text-gray-900 font-[Nunito, sans-serif]">

<!-- Navbar -->
<header class="w-full top-0 z-50 bg-white shadow-sm">
  <nav class="max-w-[1320px] mx-auto flex items-center justify-between py-4 px-4 md:px-8">
    
    <!-- Logo -->
    <a href="#" class="flex items-center gap-2">
		<img src="Images/Logo/LogoNav.png" alt="TaraDito Logo" style="height: 30px; width: auto;" />
    </a>

    <!-- Navigation Links -->
    <ul class="flex gap-4 md:gap-6 ml-auto items-center">
      <li><a href="#" class="text-sm font-medium text-gray-700 hover:text-black">Home</a></li>
      <li><a href="product.php" class="text-sm font-medium text-gray-700 hover:text-black">Venues</a></li>
      <li><a href="#" class="text-sm font-medium text-gray-700 hover:text-black">Explore</a></li>
      <li><a href="#" class="text-sm font-medium text-gray-700 hover:text-black">Contact</a></li>
    </ul>
  </nav>
</header>


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


<!-- Category Navigation -->
<nav class="category-nav">
  <div class="category-container">
    <?php foreach ($validCategory as $category): ?>
      <?php 
        $active = in_array($category, $cats, true); 
        $class = $active ? "category-item active {$category}" : "category-item {$category}";
      ?>
      <a href="<?= toggleCatLink($category) ?>" class="<?= $class ?>">
        <i class="fas fa-<?= $category === 'intimate' ? 'heart' : ($category === 'business' ? 'briefcase' : ($category === 'fun' ? 'smile' : 'tshirt')) ?>"></i>
        <?= ucfirst($category) ?>
      </a>
    <?php endforeach; ?>
  </div>
</nav>





      

  <!-- Listings Grid -->
  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
  <?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <?php $imageURL = !empty($row['imgs']) ? $row['imgs'] : ""; ?>
      
      <!-- Set h-full here -->
      <a href="listing.php?id=<?= $row['venueID'] ?>" class="block transition-transform hover:scale-105 h-full">
        <!-- Make sure this is also h-full and flex column -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden flex flex-col h-full">
          <img src="<?= $imageURL ?>" class="w-full h-48 object-cover" alt="Stay" />
          <div class="p-4 flex flex-col justify-between flex-grow">
            <div>
              <h3 class="font-semibold text-lg text-[#F28B82]"><?= htmlspecialchars($row['venueName']) ?></h3>
              <p class="text-sm text-gray-600"><?= htmlspecialchars($row['cityAddress']) ?></p>
              <p class="text-sm text-gray-600"><?= $row['availabilityDays'] ?: "Available Daily" ?></p>
              <p class="text-sm font-medium mt-1 text-[#1e40af]">₱<?= htmlspecialchars($row['priceRangeText'] ?? 'N/A') ?></p>
            </div>
            <div class="flex items-center justify-between mt-4">
              <span class="text-sm text-yellow-600">⭐ <?= number_format(rand(4.5, 5), 2) ?></span>
              <button class="text-pink-500 text-xl hover:text-red-500 transition">♡</button>
            </div>
          </div>
        </div>
      </a>

    <?php endwhile; ?>
  <?php else: ?>
    <p class="col-span-full text-center text-gray-500">No venues found.</p>
  <?php endif; ?>
  <?php $conn->close(); ?>
</main>


</body>
</html>

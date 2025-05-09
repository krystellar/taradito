<?php
  include('db_connection.php');

  // Search feature
  $searchTerm = isset($_GET['query']) ? $conn->real_escape_string($_GET['query']) : '';
  $selectedCategory = isset($_GET['category']) ? (array)$_GET['category'] : [];
  $validCategory = ['intimate','business','fun','casual'];
  // Filter feature 
  $cats = array_values(array_intersect($validCategory, $selectedCategory));

  // Editing SQL query to fetch venues with the category
  $sql = "SELECT v.*, p.priceRangeText
        FROM venueData v
        LEFT JOIN priceRange p ON v.priceRangeID = p.priceRangeID";

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
  <link href="prouduct.css" rel="stylesheet">
  <script defer src="second.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Roboto:wght@400;500&family=Nunito:wght@400;600&display=swap" rel="stylesheet">

	<script defer src="second.js"></script>
  <style>

</style>
  
</head>
<body class="bg-white text-gray-900 font-[Nunito, sans-serif]">

<!-- Navbar -->
<header class="w-full top-0 z-50 bg-white">
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
        class="rounded-full p-2 hover:bg-[#e03154] transition ml-2">
        <img src="Images/location.png" alt="Search" class="w-5 h-5 object-contain" />
      </button>

    </form>
  </div>
</div>

<!-- Faint Line -->
<hr class="border-t border-gray-200 mx-4 my-2">


<!-- Category Navigation -->
<nav class="category-nav bg-white overflow-hidden mb-0 pb-0 border-none shadow-none">
  <div class="container mx-auto px-4 border-none shadow-none m-0 p-0">
    <?php foreach ($validCategory as $category): ?>
      <?php 
        $active = in_array($category, $cats, true); 
        $typeClass = match ($category) {
          'intimate' => 'type--A',
          'business' => 'type--B',
          'fun' => 'type--C',
          'casual' => 'type--D',
          default => 'type--A',
        };
      ?>
      <a href="<?= toggleCatLink($category) ?>" class="button <?= $typeClass ?> <?= $active ? 'active' : '' ?>">
        <div class="button__line"></div>
        <div class="button__line"></div>
        <span class="button__text"><?= ucfirst($category) ?></span>
        <div class="button__drow1"></div>
        <div class="button__drow2"></div>
      </a>
    <?php endforeach; ?>
  </div>
</nav>
      

  <!-- Listings Grid -->
  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 font-sans">

  
  <?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <?php $imageURL = !empty($row['imgs']) ? $row['imgs'] : ""; ?>
      
      <a href="listing.php?id=<?= $row['venueID'] ?>" class="group h-full block transition-all duration-500 transform hover:-translate-y-2">
      <div class="relative h-full bg-white rounded-2xl border border-gray-200 shadow-md overflow-hidden flex flex-col transition-all duration-500 hover:[border-color:#7BAAF7] hover:shadow-[0_4px_20px_#7BAAF7,0_0_2px_#7BAAF7]">


          <!-- Image -->
          <img src="<?= $imageURL ?>" class="w-full h-48 object-cover z-20" alt="Stay" />

          <!-- Content -->
          <div class="p-4 flex flex-col justify-between flex-grow z-20 text-black">
            <div>
              <h3 class="font-semibold text-lg text-black group-hover:[color:#5A90D0] transition"><?= htmlspecialchars($row['venueName']) ?></h3>
              <p class="text-sm text-gray-600"><?= htmlspecialchars($row['cityAddress']) ?></p>
              <p class="text-sm text-gray-500"><?= $row['availabilityDays'] ?: "Available Daily" ?></p>
              <p class="text-sm font-semibold mt-2 text-blue-700 group-hover:text-blue-900 transition">Price: <?= htmlspecialchars($row['priceRangeText'] ?? 'N/A') ?></p>
            </div>
            <div class="flex items-center justify-between mt-4">
              <span class="text-sm text-yellow-600">⭐ <?= number_format(rand(4.5, 5), 2) ?></span>
              <button class="text-pink-500 text-xl hover:text-red-500 transition duration-300">♡</button>
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

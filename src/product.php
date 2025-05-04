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
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Roboto:wght@400;500&family=Nunito:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-[#f0f4ff] text-gray-900 font-[Nunito, sans-serif]">

  <!-- Navigation Bar -->
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

  <!-- Search Form -->
  <form method="GET" action="" class="text-center my-6">
    <input type="text" name="query" placeholder="Search for a venue..." value="<?= htmlspecialchars($searchTerm) ?>" required>
    <button type="submit">Search</button>
  </form>

  <!-- Category Navigation -->
  <nav class="bg-[#fbe9e7] border-b border-[#a0c4ff] py-4 shadow-sm">
    <div class="flex justify-center gap-3 px-6 overflow-x-auto">
      <?php foreach ($validCategory as $category): ?>
        <?php $active = in_array($category, $cats, true); ?>
        <a href="<?= toggleCatLink($category) ?>" class="text-sm font-medium px-4 py-2 rounded-full transition flex flex-col items-center <?= $active ? 'bg-[#a0c4ff] text-white hover:bg-[#8bb3ff]' : 'bg-[#f5f5f5] hover:bg-[#a0c4ff] hover:text-white text-[#333]' ?>">
          <i class="fas fa-<?= $category === 'intimate' ? 'heart' : ($category === 'business' ? 'briefcase' : ($category === 'fun' ? 'smile' : 't-shirt')) ?> text-lg"></i>
          <span><?= ucfirst($category) ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </nav>

  <!-- Listings Grid -->
  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <?php $imageURL = !empty($row['imgs']) ? $row['imgs'] : "https://source.unsplash.com/400x300/?villa"; ?>
        <a href="listing.php?id=<?= $row['venueID'] ?>" class="block transition-transform hover:scale-105 listing-card">
          <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <img src="<?= $imageURL ?>" class="w-full h-48 object-cover" alt="Stay" />
            <div class="p-4">
              <h3 class="font-semibold text-lg text-[#F28B82]"><?= htmlspecialchars($row['venueName']) ?></h3>
              <p class="text-sm text-gray-600"><?= htmlspecialchars($row['cityAddress']) ?></p>
              <p class="text-sm text-gray-600"><?= $row['availabilityDays'] ?: "Available Daily" ?></p>
              <p class="text-sm font-medium mt-1 text-[#1e40af]">₱<?= htmlspecialchars($row['priceRangeText'] ?? 'N/A') ?></p>
              <div class="flex items-center justify-between mt-2">
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

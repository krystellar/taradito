<?php
  if (session_status() === PHP_SESSION_NONE) {
      session_start();
  }
  include('db_connection.php');
  if (!isset($_SESSION['userID']) && !isset($_SESSION['managerID'])) {
    header("Location: Login.php");
    exit;
}

  define('PROJECT_ROOT', rtrim(dirname($_SERVER['SCRIPT_NAME'], 2), '/'));

  $firstName = '';
  $lastName = '';
  $role = '';

if (isset($_SESSION['userID'])) {
    //logged in as user
    $role = 'User';
    $userID = $_SESSION['userID'];
    
    $stmt = $conn->prepare("SELECT firstName, lastName FROM userdata WHERE userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $firstName = $row['firstName'];
        $lastName = $row['lastName'];
    }
    $stmt->close();

} elseif (isset($_SESSION['managerID'])) {
    // logged in as manager
    $role = 'Manager';
    $managerID = $_SESSION['managerID'];
    
    $stmt = $conn->prepare("SELECT firstName, lastName FROM managerdata WHERE managerID = ?");
    $stmt->bind_param("i", $managerID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $firstName = $row['firstName'];
        $lastName = $row['lastName'];
    }
    $stmt->close();

} else {
    echo "<p>Please log in to see your profile info.</p>";
    exit;
}
  // Search feature
 $searchTerm = isset($_GET['query']) ? $conn->real_escape_string($_GET['query']) : '';
$selectedCategory = isset($_GET['category']) ? (array)$_GET['category'] : [];
$validCategory = ['intimate','business','fun','casual'];
$cats = array_values(array_intersect($validCategory, $selectedCategory));

$sql = "SELECT 
  v.*, 
  p.priceRangeText,
  a.days,
  COALESCE(r.averageRating, 0) AS averageRating,
  COALESCE(ul.totalLikes, 0) AS totalLikes
FROM venuedata v
LEFT JOIN priceRange p ON v.priceRangeID = p.priceRangeID
LEFT JOIN availability a ON v.availabilityDays = a.availabilityDays

LEFT JOIN (
  SELECT venueID, AVG(rating) AS averageRating
  FROM userRatings
  GROUP BY venueID
) r ON v.venueID = r.venueID

LEFT JOIN (
  SELECT venueID, COUNT(*) AS totalLikes
  FROM userliked
  GROUP BY venueID
) ul ON v.venueID = ul.venueID";


$currentSort = $_GET['sort'] ?? '';

$wheres = [];
if ($searchTerm !== '') {
    $searchTermEscaped = $conn->real_escape_string($searchTerm);
    $wheres[] = "(v.venueName LIKE '%$searchTermEscaped%' OR v.barangayAddress LIKE '%$searchTermEscaped%' OR v.cityAddress LIKE '%$searchTermEscaped%')";
}

if (count($cats) > 0) {
    foreach ($cats as $c) {
        $wheres[] = "v.$c = 1";
    }
}

if (count($wheres) > 0) {
    $sql .= ' WHERE ' . implode(' AND ', $wheres);
}

$sql .= ' GROUP BY v.venueID ';
if ($currentSort === 'rating') {
    $sql .= ' ORDER BY averageRating DESC, v.venueName ASC';
} elseif ($currentSort === 'likes') {
    $sql .= ' ORDER BY ul.totalLikes DESC, v.venueName ASC';
} elseif ($currentSort === 'priceAsc'){
    $sql .= ' ORDER BY v.priceRangeID ASC, v.venueName ASC';
} elseif ($currentSort === 'priceDesc'){
    $sql .= ' ORDER BY v.priceRangeID DESC, v.venueName ASC';
} else {
    $sql .= ' ORDER BY v.venueName ASC';
}


$result = $conn->query($sql);
  // for categories
  function toggleCatLink(string $category): string {
    $qs = [];
    if (!empty($_GET['query'])) {
        $qs[] = 'query=' . urlencode($_GET['query']);
    }
    if (!empty($_GET['sort'])) {
        $qs[] = 'sort=' . urlencode($_GET['sort']);
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
  // for sortings
  function toggleSortLink(string $sortType): string {
    $qs = [];
    if (!empty($_GET['query'])) {
        $qs[] = 'query=' . urlencode($_GET['query']);
    }
    if (!empty($_GET['category'])) {
        foreach ((array)$_GET['category'] as $c) {
            $qs[] = 'category[]=' . urlencode($c);
        }
    }
    $currentSort = $_GET['sort'] ?? '';
    if ($currentSort !== $sortType) {
        $qs[] = 'sort=' . urlencode($sortType);
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
  <script defer src=""></script>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&family=Roboto:wght@400;500&family=Nunito:wght@400;600&display=swap" rel="stylesheet">

	<script defer src="second.js"></script>
  <style>


/*Category links*/
//* Category Container Layout */
.category-nav {
  background-color: white;
  overflow: hidden;
  margin-bottom: 0;
  padding-bottom: 0;
  border: none;
  box-shadow: none;
  align-content: ;
}

.category-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 16px;
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  justify-content: center;
}

/* Core Button Style (from Uiverse.io) */
.button {
  display: inline-block;
  text-decoration: none;
  background: #fbca1f;
  font-family: inherit;
  padding: 0.6em 1.3em;
  font-weight: 900;
  font-size: 18px;
  border: 3px solid black;
  border-radius: 0.4em;
  box-shadow: 4px 4px 0 0 black;
  cursor: pointer;
  color: black;
  text-transform: uppercase;
  position: relative;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.button:hover {
  transform: translateY(-4px); /* Move the button up vertically */
  box-shadow: 6px 6px 0 0 black; /* Keep the box-shadow visible */
}

/* Active Button */
.button.active {
  border-style: solid;
  border-color: #333; /* Darker border color */
}


.button__text {
  position: relative;
  z-index: 1;
}

/* Category Types */
.type--A {
  background-color: #F28B82; /* Soft Rose */
  color: black;
}

.type--B {
  background-color: #7BAAF7; /* Modern Sky Blue */
  color: black;
}

.type--C {
  background-color: #FFE066; /* Pastel Amber */
  color: black;
}

.type--D {
  background-color: #C7EDC5; /* Tea Green */
  color: black;
}

/* Active Button - Richer/Darker Shades */
.button.active.type--A {
  background-color: #c94b4b; /* Deeper red-rose */
  color: white;
}

.button.active.type--B {
  background-color: #3b82f6; /* Stronger blue (Tailwind Blue-500) */
  color: white;
}

.button.active.type--C {
  background-color: #d4af37; /* Golden yellow tone */
  color: white;
}

.button.active.type--D {
  background-color: #15803d; /* Deep green (Tailwind Green-700) */
  color: white;
}

.sort-buttons {
  display: flex;
  gap: 0.5rem;
  margin-left: auto;
}

.sort-btn {
  display: inline-block;
  text-decoration: none;
  background: #d1d5db;
  font-family: inherit;
  padding: 0.6em 1.3em;
  font-weight: 900;
  font-size: 18px;
  border: 3px solid black;
  border-radius: 0.4em;
  box-shadow: 4px 4px 0 0 black;
  cursor: pointer;
  color: #1f2937;
  text-transform: uppercase;
  position: relative;
  transition: transform 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
}

.sort-btn:hover {
  transform: translateY(-4px);
  box-shadow: 6px 6px 0 0 black;
  background-color: #9ca3af;
  color: white;
}

.sort-btn.active {
  background-color: #4b5563;
  color: white;
  border-color: #222;
  box-shadow: 6px 6px 0 0 #111;
}


/*Lisitngs*/

    .listings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 24px;
    max-width: 1600px;
    margin: auto;
    padding: 2rem;
  }

  .listing-card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border: 4px solid black;
    background: linear-gradient(to bottom, white, #f3f4f6, #e5e7eb);
    padding: 1rem;
    box-shadow: 8px 8px 0 0 #000;
    transition: transform 0.5s ease, box-shadow 0.5s ease;
  }

  .listing-card:hover {
    transform: scale(1.05);
    background: linear-gradient(to bottom, #e5e7eb, white);
    box-shadow: 12px 12px 0 0 #000;
  }

  .listing-meta {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    font-size: 0.75rem;
  }

 /* Base styling for all categories */
.listing-category {
  border: 2px solid black;
  padding: 0.25rem 0.75rem;
  font-weight: bold;
  color: white;
  transition: transform 0.5s ease, background-color 0.5s ease;
}

/* Intimate: Soft Rose */
.category-intimate {
  background-color: #F28B82;
}

/* Business: Modern Sky Blue */
.category-business {
  background-color: #7BAAF7;
}


/* Fun: Pastel Amber */
.category-fun {
  background-color: #FFE066;
  color: #1f2937;
}

/* Casual: Tea Green */
.category-casual {
  background-color: #C7EDC5;
  color: #1f2937;
}



  .listing-image {
    width: 100%;
    height: 180px;
    object-fit: cover;
    margin: 1rem 0;
    border: 2px solid black;
  }

  .listing-body {
    flex-grow: 1;
  }

  .listing-title {
    font-size: 1.5rem;
    font-weight: 900;
    text-transform: uppercase;
    margin: 1rem 0 0.5rem;
    transition: transform 0.5s ease, color 0.5s ease;
  }

  .listing-title a {
    color: black;
    text-decoration: none;
  }

  .listing-title:hover {
    color: #1e40af;
    transform: scale(1.05);
  }

  .listing-address,
  .listing-availability {
    font-size: 0.95rem;
    color: #374151;
  }

  .listing-price {
    font-size: 1rem;
    font-weight: bold;
    color: #1e3a8a;
    border-left: 4px solid #ef4444;
    padding-left: 0.5rem;
    margin-top: 0.75rem;
    transition: border-color 0.5s ease, color 0.5s ease;
  }

  .listing-price:hover {
    border-color: #1d4ed8;
    color: #4b5563;
  }

  .listing-footer {
    margin-top: 1rem;
  }

  .listing-rating {
    font-size: 0.9rem;
    color: #eab308;
    font-weight: bold;
  }

  .no-venues {
    grid-column: span 4;
    text-align: center;
    color: #6b7280;
  }

/* Floating sticky navbar */
#navbar {
  position: sticky;
  top: 20px;
  z-index: 50;
  display: flex;
  justify-content: center;
  pointer-events: none;
  margin-bottom: 5rem;
  isolation: isolate; /* keeps pseudo-element contained */
}

#navbar::before {
  content: '';
  position: absolute;
  top: -20px;
  left: -20px;
  right: -20px;
  bottom: -1px;
  z-index: -1;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.15); /* softer glow */
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  pointer-events: none;
  transition: all 0.3s ease;
}



.navbar-container {
  pointer-events: auto; 
  background-color: #fff;
  max-width: 1400px;
  width: 100%;
  margin: 0 auto;
  padding: 16px 24px;
  box-shadow: 10px 10px 0 #000;
  border: 4px solid #000;
  display: column;
  align-items: center;
  justify-content: space-between;
  
}

.user-info-container {
  pointer-events: auto; 
  background-color: #fff;
  max-width: 300px;
  width: 100%;
  margin: 0 auto;
  padding: 16px 24px;
  box-shadow: 10px 10px 0 #000;
  border: 4px solid #000;
  display: column;
  align-items: center;
  justify-content: space-between;
    
  }

.nav-main {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  margin-bottom: 1.5rem;
}

/* Logo */
.logo img {
  height: 50px;
  width: 80px;
}

/* Navigation links */
.nav-links {
  display: flex;
  gap: 24px;
  margin-left: auto;
  list-style: none;
  padding: 0;
}

@media (min-width: 768px) {
  .nav-links {
    gap: 32px;
  }
}

.nav-link {
  text-decoration: none;
  font-size: 1.125rem; /* ~18px */
  font-weight: 500;
  color: #000;
  padding: 8px 16px;
  text-transform: uppercase;
  padding-bottom: 1rem;
  margin-bottom: 1.5rem;
  border: 2px solid transparent; /* reserve space */
  transition: transform 0.2s ease, background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
  will-change: transform;
}

.nav-link:hover {
  color: #000;
  background-color: #fff;
  transform: translateY(-2px);
  border-color: #000; /* no layout shift */
  box-shadow: 4px 4px 0 #000;
}


  
  .user-info-container h3 {
      margin-bottom: 10px;
      font-size: 20px;
  }
        
  .user-info-container p {
      font-size: 16px;
  }

   /* Search Bar Container */
/* From Uiverse.io by boryanakrasteva - Inspired Search Bar Style */
.input-container {
  width: 1000px;
  position: relative;
  margin: 20px auto;
  margin-bottom: 2rem;
}

.input-container form {
  position: relative;
  left: 16%;
}

.input {
  width: 100%;
  height: 40px;
  padding: 10px 40px 10px 10px; 
  border: 2.5px solid black;
  font-size: 14px;
  text-transform: uppercase;
  letter-spacing: 2px;
  transition: 0.2s linear;
  font-weight: 600;
}

.input:focus {
  outline: none;
  border: 0.5px solid black;
  box-shadow: -5px -5px 0px black;
}

.clear-btn {
  background: none;
  border: none;
  padding: 0;
  cursor: pointer;
  position: absolute;
  right: 10px;
  top: calc(50% + 5px);
  transform: translateY(calc(-50% - 5px));
}

.icon {
  width: 18px;
  height: 18px;
  transition: transform 0.2s ease;
}

.content {
  opacity: 0;
  animation: floatUp 1s ease forwards;
}

/* Float up keyframes */
@keyframes floatUp {
  0% {
    opacity: 0;
    transform: translateY(40px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}


@keyframes anim {
  0%, 100% {
    transform: translateY(calc(-50% - 5px)) scale(1);
  }
  50% {
    transform: translateY(calc(-50% - 5px)) scale(1.1);
  }
}

</style>
  
</head>
<body class="bg-white text-gray-900 font-[Nunito, sans-serif] content">



<!--Nav bar-->
<header id="navbar">

    <!-- information abt user -->
<div class="user-info-container">
  <a href="#" class="logo">
      <img src="Images/Logo/TaraDito.png" alt="TaraDito Logo" />
    </a>
    <h3><?= htmlspecialchars($firstName . ' ' . $lastName) ?></h3>
    <p><strong>Role:</strong> <?= htmlspecialchars($role) ?></p>
</div>


  <nav class="navbar-container">
     <div class="nav-main">
    <!-- Navigation Links on the right -->
    <ul class="nav-links">
      <li><a href="product.php" class="nav-link">Venues</a></li>
      <li><a href="top_venues_chart.php" class="nav-link">Top picks</a></li>
      <?php
          $dashboardLink = PROJECT_ROOT . '/src/Login.php';
          if (isset($_SESSION['role'])) {
              if ($_SESSION['role'] === 'manager') {
                  $dashboardLink = PROJECT_ROOT . '/src/dashboardAdmin.php';
              } elseif ($_SESSION['role'] === 'user') {
                  $dashboardLink = PROJECT_ROOT . '/src/dashboard.php';
              }
          }
          ?>
        <li>
          <a href="<?= $dashboardLink ?>" class="nav-link">
            Dashboard
          </a>
        </li>
    </ul>
        </div>

   <!-- Search Bar -->
<div class="input-container">
  <form method="GET" action="">
    <input 
      type="text" 
      name="query" 
      placeholder="Search for a venue..." 
      value="<?= htmlspecialchars($searchTerm) ?>" 
      class="input" 
      required
    >
<button 
  type="button" 
  class="clear-btn" 
  aria-label="Clear search" 
  onclick="window.location.href = window.location.pathname;"
  title="Clear search"
>
  <img src="Images/close.png" alt="Clear Search" class="icon" />
</button>


  </form>

  
</div>
</header>
  </nav>


<!-- Category Navigation -->
<nav class="category-nav">
  <div class="category-container">
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
    <div class="sort-buttons">
    <a href="<?= toggleSortLink('rating') ?>" class="sort-btn <?= ($_GET['sort'] ?? '') === 'rating' ? 'active' : '' ?>">
      by Rating
    </a>
    <a href="<?= toggleSortLink('likes') ?>" class="sort-btn <?= ($_GET['sort'] ?? '') === 'likes' ? 'active' : '' ?>">
      by Likes
    </a>
    <a href="<?= toggleSortLink('priceAsc') ?>" class="sort-btn <?= ($_GET['sort'] ?? '') === 'priceAsc' ? 'active' : '' ?>">
      ₱ ↑
    </a>
    <a href="<?= toggleSortLink('priceDesc') ?>" class="sort-btn <?= ($_GET['sort'] ?? '') === 'priceDesc' ? 'active' : '' ?>">
      ₱ ↓
    </a>
  </div>
  </div>
  


    <script>
    // Select all buttons with the class 'button'
    const buttons = document.querySelectorAll('.button');

    // Add click event listener to each button
    buttons.forEach(button => {
      button.addEventListener('click', function() {
        // Toggle the 'active' class when a button is clicked
        button.classList.toggle('active');
      });
    });
  </script>
</nav>
      

<!-- Listings Grid -->
<main class="listings-grid">
  <?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <?php $imageURL = !empty($row['imgs']) ? 'src/uploads/' . htmlspecialchars($row['imgs']) : 'src/uploads/default.jpg'; ?>

      <article class="listing-card" onclick="location.href='listing.php?id=<?= $row['venueID'] ?>';" style="cursor: pointer;">
        <!-- Date and Category -->
        <div class="listing-meta">
        <?php
          $categories = ['intimate', 'business', 'fun', 'casual'];
          foreach ($categories as $cat) {
            if (!empty($row[$cat])) {
              echo '<span class="listing-category category-' . $cat . '">' . ucfirst($cat) . '</span>';
            }
          }
        ?>
      </div>


        <!-- Image -->
        <img class="listing-image" src="<?php echo $row['imgs']; ?>" alt="Venue Image">

        <!-- Content -->
        <div class="listing-body">
          <h3 class="listing-title">
              <?= htmlspecialchars($row['venueName']) ?>
          </h3>
          <p class="listing-address"><?= htmlspecialchars($row['cityAddress']) ?></p>
          <p class="listing-availability"><?= htmlspecialchars($row['days'] ?? "Available Daily") ?></p>
          <p class="listing-price">Price: <?= htmlspecialchars($row['priceRangeText'] ?? 'N/A') ?></p>
        </div>

        <!-- Footer -->
        <div class="listing-footer">
          <p class="listing-rating">
            ⭐ <?= $row['averageRating'] !== null ? number_format($row['averageRating'], 2) : 'No Ratings' ?>
          </p>
          <p class="listing-rating">
            ❤️ <?= $row['totalLikes'] !== null ? number_format($row['totalLikes']) : 'No Likes' ?>
          </p>
        </div>
      </article>

    <?php endwhile; ?>
  <?php else: ?>
    <p class="no-venues">No venues found.</p>
  <?php endif; ?>
  <?php $conn->close(); ?>
</main>


</body>
</html>

<?php
  session_start();
  include('db_connection.php');

  $resFlash = $_SESSION['reservation_success'] ?? false;
  unset($_SESSION['reservation_success']);

  if (!isset($_SESSION['userID'])) {
    header("Location: Login_user.php");
    exit;
  }
  $userID = $_SESSION['userID'];
  
  if (isset($_SESSION['userID']) && isset($_GET['id'])) {
      $venueID = $_GET['id'];
      $conn->execute_query("INSERT INTO userHistory (userID, venueID) VALUES (?, ?)", [$userID, $venueID]);
  }

  $venueID = isset($_GET['id']) ? (int)$_GET['id'] : 0;

  $sql = "SELECT v.*, p.priceRangeText, m.firstName, m.lastName
          FROM venueData v
          LEFT JOIN priceRange p ON v.priceRangeID = p.priceRangeID
          LEFT JOIN managerVenue mv ON v.venueID = mv.venueID
          LEFT JOIN managerData m ON mv.managerID = m.managerID
          WHERE v.venueID = ?";
  $sql_reviews = "SELECT r.*, r.rating, r.review, r.createDate, u.firstName, u.lastName
                  FROM userratings r
                  LEFT JOIN userData u ON r.userID = u.userID
                  WHERE r.venueID = ?
                  ORDER BY r.createDate DESC";

  $sql_avg_rating = "SELECT AVG(rating) AS avg_rating FROM userratings WHERE venueID = ?";
  $result_avg_rating = $conn->execute_query($sql_avg_rating, [$venueID]);
  $average_rating = $result_avg_rating->fetch_assoc()['avg_rating'];
  $average_rating = round($average_rating, 2);

  // Fetch flash messages
  $successMessage = '';
  $errorMessage = '';

  if (isset($_SESSION['reservation_success'])) {
      $successMessage = "Reservation successful! You can check your reservation in your dashboard.";
      unset($_SESSION['reservation_success']);
  }

  if (isset($_SESSION['error'])) {
      $errorMessage = $_SESSION['error'];
      unset($_SESSION['error']);
  }

  $liked = false;
  $wishlisted = false;

  if ($userID) {
      $stmt = $conn->prepare("SELECT 1 FROM userliked WHERE userID = ? AND venueID = ?");
      $stmt->bind_param("ii", $userID, $venueID);
      $stmt->execute();
      $liked = $stmt->get_result()->num_rows > 0;

      $stmt = $conn->prepare("SELECT 1 FROM userwishlist WHERE userID = ? AND venueID = ?");
      $stmt->bind_param("ii", $userID, $venueID);
      $stmt->execute();
      $wishlisted = $stmt->get_result()->num_rows > 0;
  }

  $result = $conn->execute_query($sql, [$venueID]);
  $venue = $result->fetch_assoc();

  $result_reviews = $conn->execute_query($sql_reviews, [$venueID]);
  $reviews = $result_reviews->fetch_all(MYSQLI_ASSOC);

  $sql_total_reviews = "SELECT COUNT(*) AS totalReviews FROM userratings WHERE venueID = ?";
  $result_total_reviews = $conn->execute_query($sql_total_reviews, [$venueID]);
  $total_reviews = $result_total_reviews->fetch_assoc()['totalReviews'];

  $sql_existing_review = "SELECT * FROM userratings WHERE venueID = ? AND userID = ?";
  $stmt = $conn->prepare($sql_existing_review);
  $stmt->bind_param("ii", $venueID, $_SESSION['userID']);
  $stmt->execute();
  $result_existing_review = $stmt->get_result();
  $existingReview = $result_existing_review->fetch_assoc();
  $stmt->close();
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
  <title><?= htmlspecialchars($venue['venueName']) ?> | Design Stays</title>
  <link href="output.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
</head>
<body class="bg-gray-50">

<?php if ($resFlash): ?>
  <script>alert("✅ Reservation successful! Track your reservation on the user dashboard. ");</script>
<?php endif; ?>
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
      <li><a href="Dashboard.php" class="text-lg font-medium text-gray-800 hover:text-white hover:bg-[#a0c4ff] py-2 px-4 rounded-full transition-all duration-300">Dashboard</a></li>
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
  <p id="villa-rating" class="mb-2 text-yellow-600">⭐ <?= number_format($average_rating ?? 0, 2) ?></p>
  <p id="villa-price" class="text-xl font-semibold text-pink-600"><?= htmlspecialchars($venue['priceRangeText']) ?></p>
  <!-- wishlist and like here -->
  <div class="flex gap-4 mt-4">
    <!-- Like Toggle -->
    <form method="post" action="like.php">
      <input type="hidden" name="venueID" value="<?= $venueID ?>">
      <button type="submit" class="<?= $liked ? 'bg-red-600' : 'bg-red-500' ?> text-white px-4 py-2 rounded hover:bg-red-700">
        <?= $liked ? '❤️ Liked' : '🤍 Like' ?>
      </button>
    </form>

    <!-- Wishlist Toggle -->
    <form method="post" action="wishlist.php">
      <input type="hidden" name="venueID" value="<?= $venueID ?>">
      <button type="submit" class="<?= $wishlisted ? 'bg-blue-600' : 'bg-blue-500' ?> text-white px-4 py-2 rounded hover:bg-blue-700">
        <?= $wishlisted ? '📌 Added' : '➕ Add to Wishlist' ?>
      </button>
    </form>
  </div>


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
    <?php if ($venue['pwdFriendly']): ?><div><p class="font-medium">Persons With Disability Accessible</p></div><?php endif; ?>
    <?php if ($venue['parking']): ?><div><p class="font-medium">Parking Available</p></div><?php endif; ?>
    <?php if ($venue['securityStaff']): ?><div><p class="font-medium">Security Personnel</p></div><?php endif; ?>
    <?php if ($venue['wifiAccess']): ?><div><p class="font-medium">Wi-Fi Connectivity</p></div><?php endif; ?>
  </div>

  <!-- Map -->
  <section class="mt-10">
    <h2 class="text-2xl font-bold mb-4">Where you'll be</h2>
    <div id="map" style="height:400px;" class="w-full rounded-lg shadow-md"></div>
  </section>

  <!-- landmarks and routes-->
  <?php if (!empty($venue['landmarks']) || !empty($venue['routes'])): ?>
  <section class="bg-gray-50 border-t mt-6 pt-4">
    <h3 class="text-lg font-semibold text-gray-700 mb-2">Nearby Info</h3>
    
    <?php if (!empty($venue['landmarks'])): ?>
      <div class="mb-3">
        <p class="text-sm text-gray-600"><strong>Landmarks:</strong> <?= nl2br(htmlspecialchars($venue['landmarks'])) ?></p>
      </div>
    <?php endif; ?>

    <?php if (!empty($venue['routes'])): ?>
      <div>
        <p class="text-sm text-gray-600"><strong>Suggested Routes:</strong> <?= nl2br(htmlspecialchars($venue['routes'])) ?></p>
      </div>
    <?php endif; ?>
  </section>
<?php endif; ?>



  <!-- Booking Section -->
  <div class="border rounded-lg p-6 shadow-md bg-white">
    <h2 class="text-2xl font-bold mb-4">Reserve your stay</h2>
    <p class="text-xl font-semibold mb-2" id="villa-price"></p>

    <p class="text-md mb-1"><strong>Contact:</strong> <?= htmlspecialchars($venue['contactNum']) ?></p>
    <p class="text-md mb-4"><strong>Email:</strong> <?= htmlspecialchars($venue['contactEmail']) ?></p>

    <div class="mb-4">
      <strong>Payment Methods:</strong>
      <ul class="list-disc list-inside">
        <?php if ($venue['payCash']) echo '<li>Cash</li>'; ?>
        <?php if ($venue['payElectronic']) echo '<li>Electronic</li>'; ?>
        <?php if ($venue['payBank']) echo '<li>Bank Transfer</li>'; ?>
      </ul>
    </div>

    <form method="POST" action="reserve_venue.php" class="flex flex-col gap-4 mb-4">
      <input type="hidden" name="venueID" value="<?= $venue['venueID'] ?>">
      <input type="hidden" name="userID" value="<?= $_SESSION['userID'] ?>">

      <input name="startDate" type="date" required class="border p-2 rounded" placeholder="Start Date">
      <input name="startTime" type="time" required class="border p-2 rounded" placeholder="Start Time">
      <input name="endDate" type="date" required class="border p-2 rounded" placeholder="End Date">
      <input name="endTime" type="time" required class="border p-2 rounded" placeholder="End Time">

      <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded w-full">
        Reserve
      </button>
    </form>

    <p class="text-center text-gray-500 text-sm mt-2">You won't be charged yet</p>
  </div>
  
  <!-- Add Review Section -->
  <div class="border-t pt-6 mt-6">
    <h2 class="text-xl font-semibold mb-4">Add a Review</h2>

    <form method="POST" action="add_review.php" class="flex flex-col gap-4">
        <input type="hidden" name="venueID" value="<?= $venue['venueID'] ?>">
        <input type="hidden" name="userID" value="<?= $_SESSION['userID'] ?>">

        <label for="rating" class="text-sm font-medium">Rating:</label>
        <div id="rating-stars" class="flex gap-2">
            <?php for ($i = 1; $i <= 5; $i++): ?> <!-- Change loop to start from 1 -->
                <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" class="star-rating-input" />
                <label for="star<?= $i ?>" class="star">&#9733;</label>
            <?php endfor; ?>
        </div>

        <label for="review" class="text-sm font-medium">Your Review:</label>
        <textarea name="review" id="review" rows="4" required class="border p-2 rounded" placeholder="Write your review..."></textarea>

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-full">
            Submit Review
        </button>
    </form>
  </div>

 <!-- Reviews Section -->

  <?php
    if (empty($reviews)) {
        echo '<p class="text-gray-500">No reviews yet</p>';
    } else {
        $editing = isset($_POST['edit_mode']) && $_POST['edit_mode'] == '1';
        $editingReviewID = $editing ? $_POST['ratingID'] : null;

        foreach ($reviews as $review) {
            $isOwner = ($review['userID'] == $_SESSION['userID']);
  ?>

  <div class="flex items-start gap-4 mb-6" id="review-<?= $review['ratingID'] ?>">
      <div>
          <p class="font-bold"><?= htmlspecialchars($review['firstName']) ?> <?= htmlspecialchars($review['lastName']) ?></p>
          <p class="text-gray-500 text-sm"><?= htmlspecialchars($review['createDate']) ?></p>
          <p class="mt-2"><?= htmlspecialchars($review['review']) ?></p>
          <p class="text-yellow-600">⭐ <?= number_format($review['rating'], 1) ?></p>

          <?php if ($isOwner): ?>
              <!-- Delete Button -->
              <form action="delete_review.php" method="POST" class="inline-block mt-2 mr-2">
                  <input type="hidden" name="ratingID" value="<?= $review['ratingID'] ?>">
                  <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
              </form>

              <!-- Update Button -->
              <form action="" method="POST" class="inline-block mt-2">
                  <input type="hidden" name="edit_mode" value="1">
                  <input type="hidden" name="ratingID" value="<?= $review['ratingID'] ?>">
                  <input type="hidden" name="rating" value="<?= $review['rating'] ?>">
                  <input type="hidden" name="review" value="<?= htmlspecialchars($review['review'], ENT_QUOTES) ?>">
                  <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm" onclick="window.location.href='#updateform';">Update</button>
              </form>
          <?php endif; ?>
      </div>
  </div>

<!-- Update Form -->
 <div id="updateform" class="updateform"></div>
  <?php if ($editing): ?>
      <form method="POST" action="update_review.php" class="flex flex-col gap-4 mt-6 border p-4 rounded-lg bg-gray-50">
          <input type="hidden" name="venueID" value="<?= $venue['venueID'] ?>">
          <input type="hidden" name="userID" value="<?= $_SESSION['userID'] ?>">
          <input type="hidden" name="ratingID" value="<?= htmlspecialchars($_POST['ratingID']) ?>">

          <label class="text-sm font-medium">Update Rating:</label>
          <div class="flex gap-2">
              <?php for ($i = 1; $i <= 5; $i++): ?>
                  <input type="radio" id="update-star<?= $i ?>" name="rating" value="<?= $i ?>" class="hidden peer" />
                  <label for="update-star<?= $i ?>" class="star text-2xl cursor-pointer">&#9733;</label>
              <?php endfor; ?>
          </div>


          <label for="update-review" class="text-sm font-medium">Update Review:</label>
          <textarea name="review" id="update-review" rows="4" required class="border p-2 rounded w-full"><?= htmlspecialchars($_POST['review']) ?></textarea>

          <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded w-full">
              Update Review
          </button>
      </form>
  <?php endif; ?>

  <?php
    }
  }
  ?>

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


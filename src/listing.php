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

   <style>
  .container {
  width: 100%;
  max-width: 1280px;
  border: 4px solid #000;
  background-color: #fff;
  padding: 2rem;
  box-shadow: 10px 10px 0 #000;
  font-family: "Arial", sans-serif;
  margin: 0 auto 2rem auto; /* <-- Centers horizontally */
  margin-top: 2rem;
}


  .title {
    font-size: 2rem;
    font-weight: 900;
    color: #000;
    text-transform: uppercase;
    border-bottom: 2px solid #000;
    padding-bottom: 1rem;
    margin-bottom: 1.5rem;
  }

  .image-gallery {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
    margin-bottom: 1.5rem;
  }

  @media (min-width: 768px) {
    .image-gallery {
      grid-template-columns: 1fr 1fr;
    }
  }

  .main-image {
  border-radius: 0.5rem; /* Slight rounding for a tough look */
  width: 100%;
  object-fit: cover;
  height: 20rem;
  border: 10px solid #222; /* Dark, thick border for a stark effect */
  box-shadow: 0 0 0 5px #fff, 0 0 10px rgba(0, 0, 0, 0.5); /* Strong shadow contrast */
}

.sub-images {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem; /* More spacing for a cleaner brutalist layout */
}

.sub-image {
  border-radius: 0.25rem; /* Sharp but slightly rounded edges */
  width: 100%;
  border: 8px solid #333; /* Dark, contrasting border */
  box-shadow: 0 0 0 5px #fff, 0 0 15px rgba(0, 0, 0, 0.6); /* Aggressive shadows */
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.sub-image:hover {
  transform: scale(1.05); /* Zoom effect for impact */
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.8); /* Stronger shadow on hover */
}


  .text-gray {
    color: #4B5563;
  }

  .text-lg {
  font-size: 1.5rem;
  font-weight: 900;
  margin-bottom: 1rem;
  letter-spacing: 0.05em;
  color: #111;
  text-transform: uppercase;
}

.text-sm {
  font-size: 1rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  letter-spacing: 0.1em;
  color: #333;
  text-transform: uppercase;
}

  .text-yellow {
  font-size: 1rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  letter-spacing: 0.1em;
  color: #333;
  text-transform: uppercase;
  }

 .text-xl {
  font-size: 2rem; /* Larger for impact */
  font-weight: 900; /* Bold */
  color: #1D9B45; /* Money green */
  text-transform: uppercase; 
  letter-spacing: 0.1em; 
  margin-bottom: 1.25rem; 
}


  .button-group {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
  }

  .button {
    grid-column: span 2;
    color: #fff;
    font-weight: 900;
    padding: 1rem 2rem;
    border: 3px solid #000;
    border-radius: 0;
    box-shadow: 7px 7px 0 #000;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    text-transform: uppercase;
}

.button:hover {
    background-color: #ff0000;
    border-color: #ff0000;
    color: #fff;
    transform: translate(-2px, -2px);
    box-shadow: 9px 9px 0 #800000;
}

  .update{
    background-color: gold;
    color: black;
  }

  .like {
    background-color: #EF4444;
  }

  .like:hover {
    background-color: #B91C1C;
  }

  .liked {
    background-color: #DC2626;
  }

  .wishlist {
    background-color: #3B82F6;
  }

  .wishlist:hover {
    background-color: #1D4ED8;
  }

  .wishlisted {
    background-color: #2563EB;
  }


  .about-title {
    margin-top: 2rem;
    font-size: 2rem;
    font-weight: 900;
    color: #000;
    text-transform: uppercase;
    border-bottom: 2px solid #000;
    padding-bottom: 1rem;
    margin-bottom: 1.5rem;

  }

  .about-text {
     margin-top: 0.25rem;
    color: #000;
    font-weight: 600;
  }
  
.flex {
    display: flex;
    gap: 2rem;
    max-width: 1280px;
    width: 100%;
    margin: 0 auto; /* Centers the flex container */
}

.w-1-2 {
    width: 50%;
    box-sizing: border-box; /* Includes padding and border in the width */
}

.reviews-container,
.amenities-container,
.review-section,
.booking-section {
    width: 100%;
    max-width: 640px; /* Each container will be at most 500px */
    border: 4px solid #000;
    background-color: #fff;
    padding: 2rem;
    box-shadow: 10px 10px 0 #000;
    font-family: "Arial", sans-serif;
    margin-top: 2rem;
    margin-bottom: 2rem;
}

.reviews-container {
    margin-right: 1rem; /* Optional: Adds some space between the two containers */
}

.amenities{
  color: black;
}

.map-container {
    width: 100%;
    max-width: 1280px;
    border: 4px solid #000;
    background-color: #fff;
    padding: 2rem;
    box-shadow: 10px 10px 0 #000;
    font-family: "Arial", sans-serif;
    margin-bottom: 2rem;
    margin-left: auto;
    margin-right: auto;
}


.booking-title {
    font-size: 2rem;
    font-weight: 900;
    margin-bottom: 1.5rem;
}

.villa-price {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.booking-contact {
    font-size: 1rem;
    margin-bottom: 0.5rem;
}

.payment-methods {
    margin-bottom: 1.5rem;
}

.payment-methods ul {
    list-style-type: disc;
    padding-left: 1.25rem;
    margin-top: 0.5rem;
}

.booking-form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 1rem;
}

.booking-form input[type="date"],
.booking-form input[type="time"] {
    padding: 0.75rem;
    font-size: 1rem;
    border: 3px solid #000;
    border-radius: 8px;
    background-color: #fff;
    color: #000;
    transition: all 0.2s ease;
}

.booking-form input:focus {
    outline: none;
    border-color: #000;
    box-shadow: 3px 3px 0 #000;
}

.booking-form button {
    padding: 1rem;
    font-size: 1rem;
    font-weight: 700;
    text-transform: uppercase;
    background-color: #d63384;
    color: #fff;
    border: 3px solid #000;
    border-radius: 8px;
    box-shadow: 5px 5px 0 #000;
    cursor: pointer;
    transition: all 0.2s ease;
}

.booking-form button:hover {
    background-color: #a61e64;
    transform: translate(-2px, -2px);
    box-shadow: 7px 7px 0 #000;
}

.booking-form button:active {
    transform: translate(5px, 5px);
    box-shadow: none;
}

.booking-note {
    text-align: center;
    font-size: 0.875rem;
    color: #555;
    margin-top: 1rem;
}

/* General Form Styles */
.updateform {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
}

.update-form {
    display: flex;
    flex-direction: column;
    gap: 16px;
    padding: 24px;
    background-color: #f9f9f9;
    border-radius: 8px;
    border: 1px solid #ddd;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Form Labels */
.form-label {
    font-size: 1rem;
    font-weight: bold;
    color: #333;
}

/* Input and Textarea */
.form-textarea,
.update-form input {
    padding: 12px;
    font-size: 1rem;
    border-radius: 6px;
    border: 1px solid #ccc;
    outline: none;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

/* Focused State */
.form-textarea:focus,
.update-form input:focus {
    border-color: #4CAF50;
    box-shadow: 0 0 8px rgba(76, 175, 80, 0.3);
}

/* Button Styles */
.form-button {
    padding: 14px;
    background-color: #4CAF50;
    color: white;
    font-weight: bold;
    font-size: 1rem;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.form-button:hover {
    background-color: #45a049;
}

/* Rating Section */
.rating-stars {
    display: flex;
    gap: 1rem;
    align-items: center;
}

/* Star Ratings */
.star-rating-input {
    display: none;
}

.star {
    font-size: 2.5rem;
    color: #ccc;
    cursor: pointer;
    transition: color 0.3s ease;
}

/* Checked Star */
.star-rating-input:checked + .star {
    color: #fbc02d;
}

/* Hover Effect for Stars */
.star:hover,
.star:hover ~ .star {
    color: #fbc02d;
}


/*Add a review*/

/* Review Section Specific Styling */
/* Reuse existing styles */
.styled-review-form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-top: 2rem;
}

.styled-review-form label {
    font-weight: 700;
    font-size: 1rem;
    display: block;
    margin-top: 1rem;
}

.styled-review-form textarea {
    width: 100%;
    padding: 0.75rem;
    font-size: 1rem;
    border: 3px solid #000;
    background-color: #fff;
    color: #000;
    box-sizing: border-box;
    transition: all 0.2s ease;
}

.styled-review-form textarea:focus {
    outline: none;
    border-color: #000;
    box-shadow: 3px 3px 0 #000;
}

.styled-review-form button {
    width: 100%;
    padding: 1rem;
    font-size: 1rem;
    font-weight: 700;
    text-transform: uppercase;
    background-color: #fff;
    color: #000;
    border: 3px solid #000;
    box-shadow: 5px 5px 0 #000;
    cursor: pointer;
    transition: all 0.2s ease;
}

.styled-review-form button:hover {
    background-color: #000;
    color: #fff;
    transform: translate(-2px, -2px);
    box-shadow: 7px 7px 0 #000;
}

.styled-review-form button:active {
    transform: translate(5px, 5px);
    box-shadow: none;
}


/* Star Rating Container */
.rating-stars {
  display: flex;
  flex-direction: row-reverse; /* Needed for proper glowing order */
  justify-content: left;
  gap: 0.5rem;
}

/* Hide radio buttons */
.star-rating-input {
  display: none;
}

/* Star Style */
.star {
    font-size: 4rem; /* Ensures the stars are big */
    color: #ccc;
    cursor: pointer;
    transition: color 0.2s ease;
    display: inline-block; /* Ensures stars are displayed inline */
}

/* Light up selected stars */
.star-rating-input:checked ~ .star {
  color: gold;
}

/* Hover lighting from left to right */
.rating-stars:hover .star {
  color: #ccc; /* Reset all to gray */
}

.star:hover,
.star:hover ~ .star {
  color: gold;
}

.ratings {
  margin-right: 5px; /* Adjust the gap between the stars */
}

.two-button{
    display: inline-block;
    padding: 0.75rem 1.25rem;
    text-align: center;
    font-size: 0.9rem;
    font-weight: 700;
    text-transform: uppercase;
    border: 3px solid #000;
    color: #000;
    position: relative;
    transition: all 0.2s ease;
    box-shadow: 5px 5px 0 #000;
    cursor: pointer;
}

.delete{
 background-color: #F44336;
}

.delete:hover {
    background-color: #D32F2F;
    transform: scale(1.05);
}

.update{
  background-color: #36aef4;
}

.update:hover {
    background-color: #81bde6;
    transform: scale(1.05);
}

.review {
  font-family: Arial, sans-serif;
}

.review-name {
  font-weight: bold;
  display: inline-block;
  margin-right: 10px;
}

.review-date {
  font-size: 0.875rem;
  color: gray;
  display: inline-block;
}

.review-container {
  margin-top: 20px;
  border: 4px solid #000;
  background-color: #fff;
  padding: 2rem;
  box-shadow: 10px 10px 0 #000;
  font-family: "Arial", sans-serif;
  margin-bottom: 2rem;
  max-width: 100% ;
}

.review-text {
  font-size: 1rem;
  margin: 0;
  white-space: normal; /* Wraps text */
  word-wrap: break-word; /* Prevents cutting off */
}



  </style>

  
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

<div class="container">
  <h1 id="villa-title" class="title"><?= htmlspecialchars($venue['venueName']) ?></h1>

  <!-- Image Gallery -->
  <div class="image-gallery">
    <img src="<?= htmlspecialchars($venue['imgs'] ?? 'https://source.unsplash.com/800x600/?villa')?>" alt="Main Image" class="main-image" />
    <div class="sub-images">
      <?php if (!empty($venue['img2'])): ?><img src="<?= htmlspecialchars($venue['img2']) ?>" class="sub-image" /><?php endif; ?>
      <?php if (!empty($venue['img3'])): ?><img src="<?= htmlspecialchars($venue['img3']) ?>" class="sub-image" /><?php endif; ?>
      <?php if (!empty($venue['img4'])): ?><img src="<?= htmlspecialchars($venue['img4']) ?>" class="sub-image" /><?php endif; ?>
      <?php if (!empty($venue['img5'])): ?><img src="<?= htmlspecialchars($venue['img5']) ?>" class="sub-image" /><?php endif; ?>
    </div>
  </div>

  <p id="villa-location" class="text-gray text-lg">
    <?= htmlspecialchars($venue['barangayAddress']) . ', ' . htmlspecialchars($venue['cityAddress']) ?>
  </p>
  <p id="villa-specs" class="text-sm">Capacity: <?= htmlspecialchars($venue['maxCapacity']) ?> guests</p>
  <p id="villa-rating" class="text-yellow">⭐ <?= number_format($average_rating ?? 0, 2) ?></p>
  <p id="villa-price" class="text-xl"><?= htmlspecialchars($venue['priceRangeText']) ?></p>

  <!-- wishlist and like -->
  <div class="button-group">
    <form method="post" action="like.php">
      <input type="hidden" name="venueID" value="<?= $venueID ?>">
      <button type="submit" class="button <?= $liked ? 'liked' : 'like' ?>">
        <?= $liked 
  ? '<img src="Images/like.png" alt="Liked" style="width: 1.2rem; height: 1.2rem; display: inline;"> Liked'
  : '<img src="Images/like.png" alt="Like" style="width: 1.2rem; height: 1.2rem; display: inline;"> ' ?>
      </button>
    </form>

    <form method="post" action="wishlist.php">
      <input type="hidden" name="venueID" value="<?= $venueID ?>">
      <button type="submit" class="button <?= $wishlisted ? 'wishlisted' : 'wishlist' ?>">
        <?= $wishlisted 
  ? '<img src="Images\office-push-pin.png" alt="Added" style="width: 1.2rem; height: 1.2rem; display: inline;"> Added' 
  : '<img src="Images/add.png" alt="Add to Wishlist" style="width: 1.2rem; height: 1.2rem; display: inline;"> Add to Wishlist' ?>

      </button>
    </form>
  </div>

  <!-- Villa Description -->
    <h2 class="about-title">About this place</h2>
    <p class="about-text">
      <?= htmlspecialchars($venue['venueDesc']) ?>
    </p>
</div>


 <div class="flex">
    <!-- Amenities Section -->
    <div class="amenities-container w-1-2">
        <h2 class="text-xl amenities">Services & Amenities</h2>
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
    </div>

    <!-- Reviews Section -->
    <div class="reviews-container w-1-2">
      <h2 class="text-xl amenities">Reviews</h2>
        <?php
            if (empty($reviews)) {
                echo '<p class="text-gray-500">No reviews yet</p>';
            } else {
                $editing = isset($_POST['edit_mode']) && $_POST['edit_mode'] == '1';
                $editingReviewID = $editing ? $_POST['ratingID'] : null;

                foreach ($reviews as $review) {
                    $isOwner = ($review['userID'] == $_SESSION['userID']);
        ?>

        <div class="" id="review-<?= $review['ratingID'] ?>">
            <div>
               <div class="review">
                <p class="review-name"><?= htmlspecialchars($review['firstName']) ?> <?= htmlspecialchars($review['lastName']) ?></p>
                <p class="review-date"><?= htmlspecialchars($review['createDate']) ?></p>
                 <p class="text-yellow-600">
                <?php
                  $rating = $review['rating'];
                  $fullStars = floor($rating); // Full stars
                  $emptyStars = 5 - $fullStars; // Empty stars

                  // Display full stars
                  for ($i = 0; $i < $fullStars; $i++) {
                      echo '<img src="Images/star.png" alt="Full Star" class="inline-block ratings" width="20" height="20">';
                  }

                  // Display empty stars
                  for ($i = 0; $i < $emptyStars; $i++) {
                      echo '<img src="Images/empty-star.png" alt="Empty Star" class="inline-block ratings" width="20" height="20">';
                  }
                ?>
              </p>
                <div class="review-container">
                <p class="review-text"><?= htmlspecialchars($review['review']) ?></p>
              </div>
              </div>

              

                <?php if ($isOwner): ?>
                    <form action="delete_review.php" method="POST" class="inline-block mt-2 mr-2">
                        <input type="hidden" name="ratingID" value="<?= $review['ratingID'] ?>">
                        <button type="submit" class="two-button delete">Delete</button>
                    </form>

                    <form action="" method="POST" class="inline-block mt-2">
                        <input type="hidden" name="edit_mode" value="1">
                        <input type="hidden" name="ratingID" value="<?= $review['ratingID'] ?>">
                        <input type="hidden" name="rating" value="<?= $review['rating'] ?>">
                        <input type="hidden" name="review" value="<?= htmlspecialchars($review['review'], ENT_QUOTES) ?>">
                        <button type="submit" class="two-button update" onclick="window.location.href='#updateform';">Update</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

<!-- Update Form -->
<div id="updateform" class="updateform"></div>
<?php if ($editing): ?>
    <form method="POST" action="update_review.php" class="update-form">
        <input type="hidden" name="venueID" value="<?= $venue['venueID'] ?>">
        <input type="hidden" name="userID" value="<?= $_SESSION['userID'] ?>">
        <input type="hidden" name="ratingID" value="<?= htmlspecialchars($_POST['ratingID']) ?>">

        <label class="form-label">Update Rating:</label>
        <div class="rating-stars">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" class="star-rating-input">
                <label for="star<?= $i ?>" class="star">&#9733;</label>
            <?php endfor; ?>
        </div>

        <label for="update-review" class="form-label">Update Review:</label>
        <textarea name="review" id="update-review" rows="4" required class="form-textarea"><?= htmlspecialchars($_POST['review']) ?></textarea>

        <button type="submit" class="update button">
            Update Review
        </button>
    </form>
<?php endif; ?>



        <?php
            }
        }
        ?>
    </div>
</div>

<!-- Map -->
<section class="map-container mt-10">
  <h2 class="title">MAP LOCATION</h2>
  <div id="map" style="height:400px;" class="w-full rounded-lg shadow-md"></div>


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
</section>


<div class="flex">
 <!-- Booking Section -->
<div class="booking-section w-1-2">
  <h2 class="booking-title">Reserve your stay</h2>
  <p class="villa-price" id="villa-price"></p>

  <p class="booking-contact"><strong>Contact:</strong> <?= htmlspecialchars($venue['contactNum']) ?></p>
  <p class="booking-contact"><strong>Email:</strong> <?= htmlspecialchars($venue['contactEmail']) ?></p>

  <div class="payment-methods">
    <strong>Payment Methods:</strong>
    <ul>
      <?php if ($venue['payCash']) echo '<li>Cash</li>'; ?>
      <?php if ($venue['payElectronic']) echo '<li>Electronic</li>'; ?>
      <?php if ($venue['payBank']) echo '<li>Bank Transfer</li>'; ?>
    </ul>
  </div>

  <form method="POST" action="reserve_venue.php" class="booking-form">
    <input type="hidden" name="venueID" value="<?= $venue['venueID'] ?>">
    <input type="hidden" name="userID" value="<?= $_SESSION['userID'] ?>">

    <input name="startDate" type="date" required placeholder="Start Date">
    <input name="startTime" type="time" required placeholder="Start Time">
    <input name="endDate" type="date" required placeholder="End Date">
    <input name="endTime" type="time" required placeholder="End Time">

    <button type="submit">Reserve</button>
  </form>

  <p class="booking-note">You won't be charged yet</p>
</div>

<!-- Add Review Section -->
<div class="review-section w-1-2">
  <h2 class="booking-title">Add a Review</h2>

  <form method="POST" action="add_review.php" class="styled-review-form">
  <input type="hidden" name="venueID" value="<?= $venue['venueID'] ?>">
  <input type="hidden" name="userID" value="<?= $_SESSION['userID'] ?>">


    <div class="rating-stars">
      <?php for ($i = 1; $i <= 5; $i++): ?>
        <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" class="star-rating-input">
        <label for="star<?= $i ?>" class="star">&#9733;</label>
      <?php endfor; ?>
    </div>



  <label for="review">Your Review:</label>
  <textarea name="review" id="review" rows="4" required placeholder="Write your review..."></textarea>

  <button type="submit">Submit Review</button>
</form>

</div>
</div>


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


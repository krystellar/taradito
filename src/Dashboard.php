<?php
  session_start();
  include('db_connection.php');
  define('PROJECT_ROOT', rtrim(dirname($_SERVER['SCRIPT_NAME'], 2), '/'));
  if (!isset($_SESSION['userID'])) {
      header("Location: Login_user.php");
      exit;
  }

  $userID = $_SESSION['userID'];
  $userQuery = $conn->execute_query("SELECT firstName, lastName, userEmail, joinDate FROM userData WHERE userID = ?", [$userID]);
  $user = $userQuery->fetch_assoc();

  $resQuery = $conn->execute_query(" SELECT 
      ur.*,
      vd.venueName,
      vd.cityAddress,
      ur.startDate,
      ur.endDate,
      DATEDIFF(ur.endDate, ur.startDate) AS nights,
      pr.priceRangeText,
      rs.statusText
    FROM userreserved ur
    JOIN reservationStatus rs ON ur.statusID = rs.statusID
    JOIN venuedata vd ON ur.venueID = vd.venueID
    JOIN priceRange pr ON vd.priceRangeID = pr.priceRangeID
    WHERE ur.userID = ?
    ORDER BY ur.reservationDate DESC", [$userID]);

  // updated or deleted
  if (isset($_SESSION['reservation_success'])) {
      echo "<script>alert('" . addslashes($_SESSION['reservation_success']) . "');</script>";
      unset($_SESSION['reservation_success']);
  }

  if (isset($_SESSION['reservation_error'])) {
      echo "<script>alert('" . addslashes($_SESSION['reservation_error']) . "');</script>";
      unset($_SESSION['reservation_error']);
  }

    // liked
  $likedStmt = $conn->prepare("SELECT v.venueID, v.venueName,v.barangayAddress,v.cityAddress,pr.priceRangeText
  FROM userliked ul
  JOIN venuedata v ON ul.venueID = v.venueID
  JOIN priceRange pr ON v.priceRangeID = pr.priceRangeID
  WHERE ul.userID = ?");
  $likedStmt->bind_param("i", $_SESSION['userID']);
  $likedStmt->execute();
  $likedVenues = $likedStmt->get_result();

  // wished
  $wishlistStmt = $conn->prepare("SELECT v.venueID, v.venueName,v.barangayAddress, v.cityAddress,pr.priceRangeText
  FROM userwishlist uw
  JOIN venuedata v ON uw.venueID = v.venueID
  JOIN priceRange pr ON v.priceRangeID = pr.priceRangeID
  WHERE uw.userID = ?");
  $wishlistStmt->bind_param("i", $_SESSION['userID']);
  $wishlistStmt->execute();
  $wishlistedVenues = $wishlistStmt->get_result();

  // history
  $viewedStmt = $conn->prepare("SELECT v.venueID, v.venueName, v.cityAddress, pr.priceRangeText, MAX(uh.viewedAt) AS lastViewed
    FROM userHistory uh
    JOIN venuedata v ON uh.venueID = v.venueID
    JOIN priceRange pr ON v.priceRangeID = pr.priceRangeID
    WHERE uh.userID = ?
    GROUP BY v.venueID
    ORDER BY lastViewed DESC
    LIMIT 5
  ");
  $viewedStmt->bind_param("i", $_SESSION['userID']);
  $viewedStmt->execute();
  $recentlyViewed = $viewedStmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Dashboard</title>
  <link href="output.css" rel="stylesheet" />

  <style>
/* General Card Styling */
.card {
    width: 100%;
    max-width: 1280px;
    border: 4px solid #000;
    background-color: #fff;
    padding: 2rem;
    box-shadow: 10px 10px 0 #000;
    font-family: "Arial", sans-serif;
    margin-bottom: 2rem;
}

/* Title Styling */
.card__title {
  font-size: 32px;
  font-weight: 900;
  color: #000;
  text-transform: uppercase;
  margin-bottom: 15px;
  display: block;
  position: relative;
  overflow: hidden;
  text-align: center; /* Center the title */
}

/* Content Styling */
.card__content {
  font-size: 24px;
  line-height: 1.9;
  color: #222;
  margin-bottom: 20px;
  text-align: center;
  padding: 15px 20px;
}

/* Form Styling */
.card__form {
  display: flex;
  flex-direction: column;
  gap: 15px;
  padding: 20px; /* Added padding to the form */
}

/* Input Fields Styling */
.card__form input {
  padding: 12px; /* Increased padding for a larger input field */
  border: 2px solid #000;
  font-size: 16px;
  font-family: inherit;
  width: 100%;
  background-color: #fff;
  border-radius: 5px; /* Rounded borders for a softer input field */
}

/* Input Focus Styling */
.card__form input:focus {
  outline: none;
  background-color: #000;
  color: #fff;
}

/* Hide form by default */
.card__form.hidden {
  display: none;
}

/* Show the form when the checkbox is checked */
#edit_toggle:checked + label + #edit_form {
  display: block;
}

/* Button Styling */
.card__button {
  border: 3px solid #000;
  background: #000;
  color: #fff;
  padding: 12px 24px; /* Consistent padding for buttons */
  font-size: 18px;
  font-weight: bold;
  text-transform: uppercase;
  cursor: pointer;
  width: 500px;
  border-radius: 5px; /* Rounded corners for buttons */
  transition: all 0.3s ease; /* Smooth transition for hover effect */
  display: inline-block;
  text-align: center;
  margin: 0 auto; /* Center button inside the form */
}

/* Button Hover Effect */
.card__button:hover {
  background-color: #fff;
  color: #000;
  border-color: #000;
  transform: scale(1.005); /* Slight scale effect for hover */
}

/* Log Out Button Styling */
.logout-button {
  background-color: red;
  border-color: red;
  width: 100%;
  margin: 10px 0; /* Add margin for separation */
}

/* Edit Button Styling */
.edit-button {
  background-color: #000;
  border-color: #000;
  width: 50%;
  margin: 10px 0; /* Add margin for separation */
}

/* Save Button Styling */
.save-button {
  background-color: #000;
  border-color: #000;
  width: auto;
  margin: 10px 0; /* Add margin for separation */
}

/* Input Field Styling for Edit Form */
.input-field {
  padding: 12px;
  border: 2px solid #000;
  width: 100%;
  margin-bottom: 15px;
  border-radius: 5px; /* Rounded corners for input fields */
}

/* Styling for the "Edit" and "Save Changes" buttons */
.edit-button, .save-button {
  background-color: #000;
  border-color: #000;
  width: 100%;
}

/* Layout adjustments for the "Edit" form */
.edit-form {
  display: none; /* Initially hidden */
  padding: 20px;
  background-color: #f9f9f9;
  border-radius: 10px;
  margin-top: 20px; /* Space between Edit form and other content */
}

/* Responsive Design: Ensure layout looks good on small screens */
@media (max-width: 768px) {
  .card {
    padding: 15px;
  }

  .card__title {
    font-size: 28px;
  }

  .card__content {
    font-size: 14px;
  }

  .card__form input {
    font-size: 14px;
  }

  .card__button {
    font-size: 16px;
    padding: 10px 20px;
  }

  .input-field {
    font-size: 14px;
  }
}

/* Container for the reservation history section */
.sub-container {
    border: 4px solid #000;
    background-color: #fff;
    padding: 2rem;
    box-shadow: 10px 10px 0 #000;
    font-family: "Arial", sans-serif;
    margin-bottom: 2rem;
    margin-top: 2rem;
}

.reservation-history {
  background-color: #e0f7fa; /* Lighter blue background */
}

.reservation-history:hover {
  box-shadow: 0 20px 40px rgba(173, 216, 230, 0.6);
  transform: translateY(-5px);
}


/* Title for the reservation section */
.title {
    font-size: 2rem;
    font-weight: 900;
    color: #000;
    text-transform: uppercase;
    border-bottom: 2px solid #000;
    padding-bottom: 1rem;
    margin-bottom: 1.5rem;
}

/* Table container styling */
.table-container {
  overflow-x: auto;
  background-color: white;
}

/* Table styling */
.table {
  width: 100%;
  border-collapse: collapse;
}

/* Table header styling */
.table-header {
  background-color: #a0c4ff;
  color: black;
  padding: 12px 16px;
  font-weight: bold;
  text-align: left;
  text-transform: uppercase;
}

/* Table row styling */
.table-row {
  background-color: #f9fafb;
  transition: background-color 0.3s ease;
}

.table-row:hover {
  background-color: #e0e7ff;
}

/* Table cell styling */
.table-cell {
  padding: 12px;
  text-align: left;
}

.liked {
  background-color: lightpink;
}

.recents {
  background-color: #e6ccff; /* light purple */
}


/* Status badge styles based on reservation status */
.status {
  font-weight: 600;
}

.status.Pending { color: #f59e0b; } /* yellow */
.status.Confirmed { color: #1d4ed8; } /* blue */
.status.Deposited { color: #4c51bf; } /* indigo */
.status.Happened { color: #16a34a; } /* green */
.status.Paid { color: #10b981; } /* emerald */
.status.Not\ Accepted { color: #dc2626; } /* red */

/* Actions column styling */
.actions {
  text-align: center;
}

/* Hidden form styling for updates */
.hidden-form {
  display: none;
}

/* Form container for the update form */
.form-container {
  padding: 20px;
  background-color: #f0f4f8;
  border-radius: 8px;
}

/* Input field styles */
.input-field {
  width: 100%;
  padding: 12px;
  margin: 10px 0;
  border: 2px solid #a0c4ff;
  border-radius: 8px;
}


/* No reservations message */
.no-content {
  color: #6b7280;
  text-align: center;
  font-size: 16px;
}

/* Container for the buttons */
.button-container {
  display: flex;
  gap: 15px; /* Adds space between buttons */
  justify-content: flex-start; /* Aligns buttons to the left */
  margin-top: 10px; /* Adds space above the buttons */
}

/* Update Button */
.update-button {
    background-color: #36aef4;
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
    margin-bottom: 10px;
}

.update-button:hover {
  background-color: #81bde6;
    transform: scale(1.05);
}


/* Delete Button */
.delete-button {
    background-color: #F44336;
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

.delete-button:hover {
  background-color: #D32F2F;
  transform: scale(1.05);
}

.delete-button:focus {
  outline: none;
  box-shadow: 0 0 5px rgba(231, 76, 60, 0.6);
}

/* General Styles for Liked Venues */
.liked-section {
  background-color: #f8bbd0; /* Light pink background */
}

.liked-section:hover {
  box-shadow: 0 20px 40px rgba(173, 216, 230, 0.6); /* Increase shadow intensity on hover */
  transform: translateY(-5px); /* Lift effect on hover */
}

.view-button {
    display: inline-block;
    padding: 0.75rem 1.25rem;
    text-align: center;
    font-size: 0.9rem;
    font-weight: 700;
    text-transform: uppercase;
    border: 3px solid #000;
    background-color: #fff;
    color: #000;
    position: relative;
    transition: all 0.2s ease;
    box-shadow: 5px 5px 0 #000;
    cursor: pointer;
}

.view-button:hover {
  background-color: #36aef4;
}


/* General Styles for Wishlisted Venues */
.wishlisted-section {
  background-color: #e0f7fa; /* Light blue background */
}

.wishlisted-section:hover {
  box-shadow: 0 20px 40px rgba(173, 216, 230, 0.6); /* Increase shadow intensity on hover */
  transform: translateY(-5px); /* Lift effect on hover */
}



/* General Styles for Recently Viewed Venues */
.recents-section {
  background-color: #f3e8ff; /* Light purple background */
}

.recents-section:hover {
  box-shadow: 0 20px 40px rgba(179, 136, 255, 0.6); /* Stronger purple shadow on hover */
  transform: translateY(-5px); /* Lift effect */
}

#reserve {
  margin-top: 5px;
}

  </style>

  
</head>

<body class="bg-gray-100 text-gray-900">
  <!-- Navbar -->
  <header id="navbar" class="w-full sticky top-0 z-50 transition-colors duration-300 bg-[#dbeafe] shadow-sm" >
    <nav class="max-w-[1320px] mx-auto flex items-center justify-between py-6 px-4 md:px-12">
      <!-- Logo on the left, using absolute positioning -->
      <a href="#" class="flex items-center gap-2 absolute left-10 pl-4">
        <img src="Images/Logo/LogoNav.png" alt="TaraDito Logo" class="h-[60px] w-auto" /> <!-- Increase logo size if needed -->
      </a>
      <ul class="flex gap-6 md:gap-8 flex-grow justify-center">
        <li><a href="#" class="text-lg font-medium text-gray-800 hover:text-white hover:bg-[#a0c4ff] py-2 px-4 rounded-full transition-all duration-300">Home</a></li>
        <li><a href="<?= PROJECT_ROOT ?>/src/product.php" class="text-lg font-medium text-gray-800 hover:text-white hover:bg-[#a0c4ff] py-2 px-4 rounded-full transition-all duration-300">Venues</a></li>
        <li><a href="#" class="text-lg font-medium text-gray-800 hover:text-white hover:bg-[#a0c4ff] py-2 px-4 rounded-full transition-all duration-300">Explore</a></li>
        <li><a href="<?= PROJECT_ROOT ?>/src/Dashboard.php" class="text-lg font-medium text-gray-800 hover:text-white hover:bg-[#a0c4ff] py-2 px-4 rounded-full transition-all duration-300">Dashboard</a></li>
      </ul>
    </nav>
  </header>

  <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-6">Welcome, <?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?></h1>

    <section class="card mb-10">
  <h2 class="card__title">Your Information</h2>

  <div class="card__content">
    <div class="grid sm:grid-cols-2 gap-4 text-sm">
      <div>
        <span class="font-Large"><b>Full Name:</b></span>
        <p><?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?></p>
      </div>
      <div>
        <span class="font-Large"><b>Email:</b></span>
        <p><?= $user['userEmail'] ?></p>
      </div>
      <div>
        <span class="font-Large"><b>Member Since:</b></span>
        <p><?= date("F Y", strtotime($user['joinDate'])) ?></p>
      </div>
    </div>
  </div>

<!-- Log Out Form -->
<form action="logout_user.php" method="POST" style="margin-top: 10px;">
  <button type="submit" class="card__button logout-button">
    Log Out
  </button>
</form>

<!-- Edit button using a hidden checkbox -->
<input type="checkbox" id="edit_toggle" class="hidden" />
<label for="edit_toggle" class="card__button edit-button">
  Edit
</label>

<!-- Edit form, visible only when checkbox is checked -->
<form id="edit_form" action="update_user.php" method="POST" class="card__form edit-form">
  <input 
    type="text" 
    name="firstName" 
    id="firstName" 
    value="<?php echo htmlspecialchars($user['firstName']); ?>" 
    required
    class="input-field"
  />
  
  <input 
    type="text" 
    name="lastName" 
    id="lastName" 
    value="<?php echo htmlspecialchars($user['lastName']); ?>" 
    required
    class="input-field"
  />
  
  <input 
    type="email" 
    name="userEmail" 
    id="userEmail" 
    value="<?php echo htmlspecialchars($user['userEmail']); ?>" 
    required
    class="input-field"
  />
  
  <button type="submit" name="update_user" class="card__button save-button">
    Save Changes
  </button>
</form>

<!-- Reservation History -->
<section class="sub-container reservation-history">
  <h2 class="title">Recent Reservations</h2>
  <div class="table-container">
    <table class="reservation-table">
      <thead>
        <tr>
          <th class="table-header">Venue</th>
          <th class="table-header">Location</th>
          <th class="table-header">Date</th>
          <th class="table-header">Duration</th>
          <th class="table-header">Price Range</th>
          <th class="table-header">Status</th>
          <th class="table-header">Actions</th>
        </tr>
      </thead>
      <tbody>
        <!-- Loop through reservations -->
        <?php if ($resQuery->num_rows === 0): ?>
          <tr>
            <td colspan="7" class="no-content">No reservations found.</td>
          </tr>
        <?php else: ?>
          <?php while ($row = $resQuery->fetch_assoc()): ?>
          <tr id="reservation-<?= $row['reservationID'] ?>" class="table-row">
            <td class="table-cell"><?= htmlspecialchars($row['venueName']) ?></td>
            <td class="table-cell"><?= htmlspecialchars($row['cityAddress']) ?></td>
            <td class="table-cell"><?= date("M j", strtotime($row['startDate'])) ?> – <?= date("F j", strtotime($row['endDate'])) ?></td>
            <td class="table-cell"><?= $row['nights'] ?> night<?= $row['nights'] > 1 ? 's' : '' ?></td>
            <td class="table-cell"><?= htmlspecialchars($row['priceRangeText']) ?></td>
            <td class="table-cell status <?= $row['statusText'] ?>">
              <?= htmlspecialchars($row['statusText']) ?>
            </td>
            <td class="table-cell actions">
              <!-- Update button -->
              <button class="update-button" onclick="toggleForm(<?= $row['reservationID'] ?>)">Update</button>

              <!-- Delete form -->
              <form action="delete_reservation.php" method="POST" class="delete-form">
                <input type="hidden" name="reservationID" value="<?= $row['reservationID'] ?>" />
                <button type="submit" class="delete-button">Delete</button>
              </form>
            </td>
          </tr>

          <!-- Hidden update form -->
           <tr id="form-row-<?= $row['reservationID'] ?>" class="hidden">
                <td colspan="7" class="px-4 py-4">
                  <form action="update_reservation.php" method="POST">
                    <input type="hidden" name="reservationID" value="<?= $row['reservationID'] ?>">
                    <input type="hidden" name="userID" value="<?= $_SESSION['userID'] ?>">

                    <label for="startDate">Start Date:</label>
                    <input name="startDate" type="date" value="<?= $row['startDate'] ?>" required class="border p-2 rounded" placeholder="Start Date">

                    <label for="startTime">Start Time:</label>
                    <input name="startTime" type="time" value="<?= $row['startTime'] ?>" required class="border p-2 rounded" placeholder="Start Time">

                    <label for="endDate">End Date:</label>
                    <input name="endDate" type="date" value="<?= $row['endDate'] ?>" required class="border p-2 rounded" placeholder="End Date">

                    <label for="endTime">End Time:</label>
                    <input name="endTime" type="time" value="<?= $row['endTime'] ?>" required class="border p-2 rounded" placeholder="End Time">

                    <button id="reserve" type="submit" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded w-full">
                      Update Reservation
                    </button>
                  </form>
                </td>
              </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>


  <!-- Liked -->
<section class="sub-container liked-section ">
  <h2 class="title">Liked Venues</h2>
  <div class="table-container">
    <table class="table">
      <thead class="table-header liked">
        <tr>
          <th class="table-cell">Venue</th>
          <th class="table-cell">Location</th>
          <th class="table-cell">Price Range</th>
          <th class="table-cell">Actions</th>
        </tr>
      </thead>
      <tbody class="liked-table-body">
        <?php if ($likedVenues->num_rows === 0): ?>
          <tr>
            <td colspan="4" class="liked-no-venues">No liked venues found.</td>
          </tr>
        <?php else: ?>
          <?php while ($row = $likedVenues->fetch_assoc()): ?>
            <tr>
              <td class="table-cell"><?= htmlspecialchars($row['venueName']) ?></td>
              <td class="table-cell"><?= htmlspecialchars($row['cityAddress']) ?></td>
              <td class="table-cell"><?= htmlspecialchars($row['priceRangeText']) ?></td>
              <td class="table-cell">
                <a href="listing.php?id=<?= $row['venueID'] ?>" class="view-button">View</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>


<!-- Wishlisted -->
<section class="sub-container wishlisted-section">
  <h2 class="title">Wishlisted Venues</h2>
  <div class="table-container">
    <table class="table">
      <thead class="table-header">
        <tr>
          <th class="table-cell">Venue</th>
          <th class="table-cell">Location</th>
          <th class="table-cell">Price Range</th>
          <th class="table-cell">Actions</th>
        </tr>
      </thead>
      <tbody class="wishlisted-table-body">
        <?php if ($wishlistedVenues->num_rows === 0): ?>
          <tr>
            <td colspan="4" class="no-content">No venues in wishlist.</td>
          </tr>
        <?php else: ?>
          <?php while ($row = $wishlistedVenues->fetch_assoc()): ?>
            <tr>
              <td class="table-cell"><?= htmlspecialchars($row['venueName']) ?></td>
              <td class="table-cell"><?= htmlspecialchars($row['cityAddress']) ?></td>
              <td class="table-cell"><?= htmlspecialchars($row['priceRangeText']) ?></td>
              <td class="table-cell">
                <a href="listing.php?id=<?= $row['venueID'] ?>" class="view-button">View</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>

<!-- Recents -->
<section class="sub-container recents-section">
  <h2 class="title">Recently Viewed Venues</h2>
  <div class="table-container">
    <table class="table">
      <thead class="table-header recents">
        <tr>
          <th class="table-cell">Venue</th>
          <th class="table-cell">Location</th>
          <th class="table-cell">Price Range</th>
          <th class="table-cell">Viewed At</th>
          <th class="table-cell">Actions</th>
        </tr>
      </thead>
      <tbody class="recents-table-body">
        <?php if ($recentlyViewed->num_rows === 0): ?>
          <tr><td colspan="5" class="no-content">No venues viewed yet.</td></tr>
        <?php else: ?>
          <?php while ($row = $recentlyViewed->fetch_assoc()): ?>
            <tr>
              <td class="table-cell"><?= htmlspecialchars($row['venueName']) ?></td>
              <td class="table-cell"><?= htmlspecialchars($row['cityAddress']) ?></td>
              <td class="table-cell"><?= htmlspecialchars($row['priceRangeText']) ?></td>
              <td class="table-cell"><?= date('M j, Y H:i', strtotime($row['lastViewed'])) ?></td>
              <td class="table-cell">
                <a href="listing.php?id=<?= $row['venueID'] ?>" class="view-button">View Again</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>


  </div>

</body>
</html>

<script>
  function toggleForm(reservationID) {
    const formRow = document.getElementById(`form-row-${reservationID}`);
    const formIsVisible = !formRow.classList.contains('hidden');
    
    if (formIsVisible) {
      formRow.classList.add('hidden');
    } else {
      formRow.classList.remove('hidden');
    }
  }
</script>

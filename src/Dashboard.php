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

    <!-- User Details Card -->
    <section class="bg-white shadow rounded-xl p-6 mb-10">
      <h2 class="text-xl font-semibold mb-4">Your Information</h2>
      <div class="grid sm:grid-cols-2 gap-4 text-sm">
        <div>
          <span class="font-medium text-gray-700">Full Name:</span>
          <p><?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?></p>
        </div>
        <div>
          <span class="font-medium text-gray-700">Email:</span>
          <p><?= $user['userEmail'] ?></p>
        </div>
        <div>
          <span class="font-medium text-gray-700">Member Since:</span>
          <p><?= date("F Y", strtotime($user['joinDate'])) ?></p>
        </div>
      </div>
      <button onclick="document.getElementById('edit_form').classList.toggle('hidden'); this.classList.toggle('hidden');" 
        class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-6 py-2 rounded-full shadow-sm transition duration-200 flex items-center justify-center edit-button"> Edit </button>
      <form action="logout_user.php" method="POST">
        <button type="submit" class="btn btn-danger">Log Out</button>
    </form>
    </section>
    <form id="edit_form" action="update_user.php" method="POST" class="grid-cols-1 md:grid-cols-2 gap-6 mt-6 hidden">
                <!-- First Name -->
                <div class="bg-gray-50 p-4 rounded-xl shadow-inner">
                    <label for="firstName" class="block text-xs text-gray-500 mb-1">First Name</label>
                    <input 
                        type="text" 
                        name="firstName" 
                        id="firstName" 
                        value="<?php echo htmlspecialchars($user['firstName']); ?>" 
                        class="w-full bg-white p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-300"
                        required
                    >
                </div>

                <!-- Last Name -->
                <div class="bg-gray-50 p-4 rounded-xl shadow-inner">
                    <label for="lastName" class="block text-xs text-gray-500 mb-1">Last Name</label>
                    <input 
                        type="text" 
                        name="lastName" 
                        id="lastName" 
                        value="<?php echo htmlspecialchars($user['lastName']); ?>" 
                        class="w-full bg-white p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-300"
                        required
                    >
                </div>

                <!-- Email -->
                <div class="bg-gray-50 p-4 rounded-xl shadow-inner col-span-1 md:col-span-2">
                    <label for="userEmail" class="block text-xs text-gray-500 mb-1">Email</label>
                    <input 
                        type="email" 
                        name="userEmail" 
                        id="userEmail" 
                        value="<?php echo htmlspecialchars($user['userEmail']); ?>" 
                        class="w-full bg-white p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-300"
                        required
                    >
                </div>            
            <button 
                type="submit" 
                name="update_user" 
                class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-medium px-6 py-3 rounded-full shadow-lg transition-all duration-300 transform save-button">
                Save Changes
            </button>

            </form>

    <!-- Reservation History -->
    <section class="bg-white shadow rounded-xl p-6">
      <h2 class="text-xl font-semibold mb-4">Recent Reservations</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
          <thead class="bg-[#a0c4ff] text-white text-sm">
            <tr>
              <th class="px-4 py-2 text-left">Venue</th>
              <th class="px-4 py-2 text-left">Location</th>
              <th class="px-4 py-2 text-left">Date</th>
              <th class="px-4 py-2 text-left">Duration</th>
              <th class="px-4 py-2 text-left">Price Range</th>
              <th class="px-4 py-2 text-left">Status</th>
              <th class="px-4 py-2 text-left">Actions</th>
            </tr>
          </thead>
          <tbody class="text-sm divide-y divide-gray-200">
            <?php if ($resQuery->num_rows === 0): ?>
              <tr>
                <td colspan="7" class="px-4 py-3 text-center text-gray-500">No reservations found.</td>
              </tr>
            <?php else: ?>
              <?php while ($row = $resQuery->fetch_assoc()): ?>
              <tr id="reservation-<?= $row['reservationID'] ?>">
                <td class="px-4 py-3 font-medium"><?= htmlspecialchars($row['venueName']) ?></td>
                <td class="px-4 py-3"><?= htmlspecialchars($row['cityAddress']) ?></td>
                <td class="px-4 py-3"><?= date("M j", strtotime($row['startDate'])) ?> – <?= date("F j", strtotime($row['endDate'])) ?></td>
                <td class="px-4 py-3"><?= $row['nights'] ?> night<?= $row['nights'] > 1 ? 's' : '' ?></td>
                <td class="px-4 py-3"><?= htmlspecialchars($row['priceRangeText']) ?></td>
                <td class="px-4 py-3">
                  <span class="<?php
                    echo match ($row['statusText']) {
                      'Pending' => 'text-yellow-500',
                      'Not Accepted' => 'text-red-500',
                      'Confirmed' => 'text-blue-600',
                      'Deposited' => 'text-indigo-600',
                      'Happened' => 'text-green-600',
                      'Paid' => 'text-emerald-600',
                      default => 'text-gray-600'
                    };
                    ?> font-semibold"> <?= htmlspecialchars($row['statusText']) ?>
                  </span>
                </td>
                <td class="px-4 py-3">
                  <!-- edit-->
                  <button class="text-blue-600 hover:text-blue-800 mr-4" onclick="toggleForm(<?= $row['reservationID'] ?>)">Update</button>

                  <!-- delete -->
                  <form action="delete_reservation.php" method="POST" class="inline">
                    <input type="hidden" name="reservationID" value="<?= $row['reservationID'] ?>" />
                    <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                  </form>
                </td>
              </tr>

              <!-- hidden updateform -->
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

                    <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded w-full">
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

    <!-- liked -->
    <section class="bg-white shadow rounded-xl p-6 mt-6">
      <h2 class="text-xl font-semibold mb-4">Liked Venues</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
          <thead class="bg-red-200 text-red-900 text-sm">
            <tr>
              <th class="px-4 py-2 text-left">Venue</th>
              <th class="px-4 py-2 text-left">Location</th>
              <th class="px-4 py-2 text-left">Price Range</th>
              <th class="px-4 py-2 text-left">Actions</th>
            </tr>
          </thead>
          <tbody class="text-sm divide-y divide-gray-200">
            <?php if ($likedVenues->num_rows === 0): ?>
              <tr>
                <td colspan="4" class="px-4 py-3 text-center text-gray-500">No liked venues found.</td>
              </tr>
            <?php else: ?>
              <?php while ($row = $likedVenues->fetch_assoc()): ?>
                <tr>
                  <td class="px-4 py-3 font-medium"><?= htmlspecialchars($row['venueName']) ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($row['cityAddress']) ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($row['priceRangeText']) ?></td>
                  <td class="px-4 py-3">
                    <a href="listing.php?id=<?= $row['venueID'] ?>" class="text-blue-600 hover:text-blue-800">View</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
        <!--wished-->
    <section class="bg-white shadow rounded-xl p-6 mt-6">
      <h2 class="text-xl font-semibold mb-4">Wishlisted Venues</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
          <thead class="bg-blue-200 text-blue-900 text-sm">
            <tr>
              <th class="px-4 py-2 text-left">Venue</th>
              <th class="px-4 py-2 text-left">Location</th>
              <th class="px-4 py-2 text-left">Price Range</th>
              <th class="px-4 py-2 text-left">Actions</th>
            </tr>
          </thead>
          <tbody class="text-sm divide-y divide-gray-200">
            <?php if ($wishlistedVenues->num_rows === 0): ?>
              <tr>
                <td colspan="4" class="px-4 py-3 text-center text-gray-500">No venues in wishlist.</td>
              </tr>
            <?php else: ?>
              <?php while ($row = $wishlistedVenues->fetch_assoc()): ?>
                <tr>
                  <td class="px-4 py-3 font-medium"><?= htmlspecialchars($row['venueName']) ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($row['cityAddress']) ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($row['priceRangeText']) ?></td>
                  <td class="px-4 py-3">
                    <a href="listing.php?id=<?= $row['venueID'] ?>" class="text-blue-600 hover:text-blue-800">View</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
    
    <section class="bg-white shadow rounded-xl p-6 mt-6">
      <h2 class="text-xl font-semibold mb-4">Recently Viewed Venues</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
          <thead class="bg-purple-200 text-purple-900 text-sm">
            <tr>
              <th class="px-4 py-2 text-left">Venue</th>
              <th class="px-4 py-2 text-left">Location</th>
              <th class="px-4 py-2 text-left">Price Range</th>
              <th class="px-4 py-2 text-left">Viewed At</th>
              <th class="px-4 py-2 text-left">Actions</th>
            </tr>
          </thead>
          <tbody class="text-sm divide-y divide-gray-200">
            <?php if ($recentlyViewed->num_rows === 0): ?>
              <tr><td colspan="5" class="px-4 py-3 text-center text-gray-500">No venues viewed yet.</td></tr>
            <?php else: ?>
              <?php while ($row = $recentlyViewed->fetch_assoc()): ?>
                <tr>
                  <td class="px-4 py-3 font-medium"><?= htmlspecialchars($row['venueName']) ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($row['cityAddress']) ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($row['priceRangeText']) ?></td>
                  <td class="px-4 py-3"><?= date('M j, Y H:i', strtotime($row['lastViewed'])) ?></td>
                  <td class="px-4 py-3">
                    <a href="listing.php?id=<?= $row['venueID'] ?>" class="text-blue-600 hover:text-blue-800">View Again</a>
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

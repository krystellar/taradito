<?php
// logging in as manager verification
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manager') {
    header("Location: login.php");
    exit();
}

include('db_connection.php');
define('PROJECT_ROOT', rtrim(dirname($_SERVER['SCRIPT_NAME'], 2), '/')); // for file

if (!isset($_SESSION['managerID'])) {
    header("Location: Login.php");
    exit;
}

$managerID = $_SESSION['managerID'];

// Manager profile query
$stmt = $conn->prepare("SELECT firstName, lastName, managerEmail, managerAbout FROM managerData WHERE managerID = ?");
$stmt->bind_param("i", $managerID);  // "i" for integer
$stmt->execute();
$manager = $stmt->get_result()->fetch_assoc();

if (!$manager) {
    echo "Manager profile not found!";
    exit;
}

$requestQuery = "SELECT * FROM venuerequests WHERE managerID = ?";
$stmt = $conn->prepare($requestQuery);
$stmt->bind_param("i", $managerID);
$stmt->execute();
$requestResult = $stmt->get_result();

// Fetch venues under the manager
$sql = "SELECT v.venueID, v.venueName, v.barangayAddress, v.cityAddress, v.priceRangeID, pr.priceRangeText
        FROM venueData v
        JOIN managervenue mv ON v.venueID = mv.venueID
        JOIN priceRange pr ON v.priceRangeID = pr.priceRangeID
        WHERE mv.managerID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $managerID);
$stmt->execute();

if ($stmt->errno) {
    error_log("Error executing venue query: (" . $stmt->errno . ") " . $stmt->error);
    echo "<p class='text-red-600'>Database error fetching venues.</p>";
}

$result = $stmt->get_result();
if (!$result) {
    error_log("Error getting venue result: " . $conn->error);
    echo "<p class='text-red-600'>Error retrieving venue data.</p>";
}

$venues = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch reservations for the manager's venues
$sql = "SELECT ur.reservationID, ur.reservationDate, rs.statusText,
               u.firstName, u.lastName,
               v.venueName, f.facilityName, f.facilityType, vf.price
        FROM userReserved ur
        JOIN reservationStatus rs ON ur.statusID = rs.statusID
        JOIN userData u ON ur.userID = u.userID
        JOIN venueFacilities vf ON ur.facilityID = vf.facilityID
        JOIN facility f ON vf.facilityType = f.facilityType
        JOIN venueData v ON vf.venueID = v.venueID
        JOIN managerVenue mv ON v.venueID = mv.venueID
        WHERE mv.managerID = ?
        ORDER BY ur.reservationDate DESC";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $managerID);
$stmt->execute();
$reservations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch available reservation statuses
$statusStmt = $conn->prepare("SELECT statusID, statusText FROM reservationStatus WHERE statusID >= 2 AND statusID <= 6");
$statusStmt->execute();
$statuses = $statusStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Handle POST requests for reservation updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accept_reservation'])) {
        $reservationID = $_POST['reservationID'];
        $stmt = $conn->prepare("UPDATE userReserved SET statusID = 3 WHERE reservationID = ?");
        $stmt->bind_param("i", $reservationID);
        $stmt->execute();
    }

    if (isset($_POST['reject_reservation'])) {
        $reservationID = $_POST['reservationID'];
        $stmt = $conn->prepare("UPDATE userReserved SET statusID = 2 WHERE reservationID = ?");
        $stmt->bind_param("i", $reservationID);
        $stmt->execute();
    }

    if (isset($_POST['update_status'])) {
        $reservationID = $_POST['reservationID'];
        $newStatusID = $_POST['new_status'];
        $stmt = $conn->prepare("UPDATE userReserved SET statusID = ? WHERE reservationID = ?");
        $stmt->bind_param("ii", $newStatusID, $reservationID);
        $stmt->execute();
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
// Monthly Reservations per Venue (FILTERED BY MANAGER)
$sql1 = "
    SELECT v.venueName, DATE_FORMAT(ur.reservationDate, '%Y-%m') AS month, COUNT(*) AS totalReservations
    FROM userReserved ur
    JOIN venueFacilities vf ON ur.facilityID = vf.facilityID
    JOIN venueData v ON vf.venueID = v.venueID
    JOIN managerVenue mv ON v.venueID = mv.venueID
    WHERE mv.managerID = ?
    GROUP BY month, v.venueID
    ORDER BY month DESC
";

$stmt1 = $conn->prepare($sql1);
$stmt1->bind_param("i", $managerID);
$stmt1->execute();
$result1 = $stmt1->get_result();

if (!$result1) {
    error_log("Error fetching monthly reservations: " . $conn->error);
    $monthlyReservations = [];
} else {
    $result1 = $result1->fetch_all(MYSQLI_ASSOC);
    $monthlyReservations = [];
    foreach ($result1 as $row) {
        $monthlyReservations[$row['month']][] = [
            'venueName' => $row['venueName'],
            'totalReservations' => $row['totalReservations']
        ];
    }
}

// 2. Venue Views and Likes (FILTERED BY MANAGER)
$sql2 = "
SELECT 
    v.venueName,
    COALESCE(COUNT(DISTINCT uh.historyID), 0) AS totalViews,
    COALESCE(COUNT(DISTINCT ul.userID), 0) AS totalLikes
FROM venueData v
JOIN managerVenue mv ON v.venueID = mv.venueID
LEFT JOIN userhistory uh ON v.venueID = uh.venueID
LEFT JOIN userliked ul ON v.venueID = ul.venueID
WHERE mv.managerID = ?
GROUP BY v.venueID
";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("i", $managerID);
$stmt2->execute();
$result2 = $stmt2->get_result();

if (!$result2) {
    error_log("Error fetching views and likes: " . $conn->error);
    $viewsAndLikes = [];
} else {
    $viewsAndLikes = $result2->fetch_all(MYSQLI_ASSOC);
}

if (isset($_GET['delete_id'])) {
    error_log("Delete requested for venueID = " . $_GET['delete_id']);
    // to delete
$delete_id = (int)$_GET['delete_id'];

// Verify this venueID belongs to the logged-in manager
$checkStmt = $conn->prepare("SELECT managerID FROM venuerequests WHERE requestID = ?");
$checkStmt->bind_param("i", $delete_id);
$checkStmt->execute();
$result = $checkStmt->get_result();
$row = $result->fetch_assoc();

if ($row && $row['managerID'] == $managerID) {
    $delStmt = $conn->prepare("DELETE FROM venuerequests WHERE requestID = ?");
    $delStmt->bind_param("i", $delete_id);
    $delStmt->execute();
    $delStmt->close();
}
$checkStmt->close();

header("Location: dashboardadmin.php");
exit;
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard</title>
    <!-- Favicon -->
 <link rel="icon" href="Images/Logo/TaraDito.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.0.0/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tailwindcss@3.0.0/dist/tailwind.min.js"></script>
    <link href="./output.css" rel="stylesheet">
    <style>
     
/* Floating sticky navbar */
#navbar {
  position: sticky;
  top: 20px;
  z-index: 50;
  display: flex;
  justify-content: center;
  pointer-events: none; /* allows shadow and border effects to float above */
}

.navbar-container {
  pointer-events: auto; 
  background-color: #fff;
  max-width: 1100px;
  width: 90%;
  margin: 0 auto;
  padding: 16px 24px;
  box-shadow: 10px 10px 0 #000;
  border: 4px solid #000;
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1rem;
}

/* Logo */
.logo img {
  height: 50px;
  width: 50px;
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

.dashboard {
    width: 100%;
    max-width: 1280px;
    border: 4px solid #000;
    background-color: #fff;
    padding: 2rem;
    box-shadow: 10px 10px 0 #000;
    font-family: "Arial", sans-serif;
    margin-bottom: 2rem;
}

.dashboard-title {
    font-size: 2rem;
    font-weight: 900;
    color: #000;
    text-transform: uppercase;
    border-bottom: 2px solid #000;
    padding-bottom: 1rem;
    margin-bottom: 1.5rem;
}

.dashboard-subtitle{
     font-size: 20px;
    font-weight: 900;
    color: #000;
    text-transform: uppercase;
    border-bottom: 1px solid #000;
    margin-bottom: 0.5rem;
}

.profile-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid #000;
    padding-bottom: 1rem;
}

.profile-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.profile-avatar {
    width: 4rem;
    height: 4rem;
    border-radius: 0;
    border: 3px solid #000;
    object-fit: cover;
    background-color: #ddd;
}

.manager-name {
    font-size: 1.25rem;
    font-weight: 900;
    color: #000;
}

.role {
    font-size: 0.875rem;
    color: #000;
    font-weight: 600;
}

.edit-button {
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

.edit-icon {
    width: 2rem;  /* Reduce icon size */
    height: 2rem; /* Reduce icon size */
    
}

.edit-button:hover {
    background-color: #ff0000;
    color: #fff;
    box-shadow: 7px 7px 0 #800000;
    transform: translate(-2px, -2px);
}

.profile-info-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

@media (min-width: 768px) {
    .profile-info-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .edit-form {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 2rem;
        margin-top: 2rem;
    }
}

.info-card {
    padding: 1.25rem;
    background-color: #fff;
    border: 3px solid #000;
    border-radius: 0;
    box-shadow: 5px 5px 0 #000;
}

.info-label {
    font-size: 0.875rem;
    font-weight: 700;
    color: #000;
    text-transform: uppercase;
}

.info-text {
    margin-top: 0.25rem;
    color: #000;
    font-weight: 600;
}

.full-width {
    grid-column: span 2;
}

.hidden {
    display: none;
}

/* Brutalist Form Style */
.form-group {
    background-color: #fff;
    padding: 1.5rem;
    border: 3px solid #000;
    box-shadow: inset 0 0 0 2px #000;
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-size: 0.75rem;
    color: #000;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 0.5rem;
}

.form-group input,
.form-group textarea {
    background-color: #fff;
    padding: 1rem;
    border: 2px solid #000;
    font-size: 1rem;
    font-family: "Arial", sans-serif;
    outline: none;
    transition: box-shadow 0.2s ease;
    color: #000;
}

.form-group input:focus,
.form-group textarea:focus {
    box-shadow: 0 0 0 3px #000;
}

/* Save Button */
.save-button {
    grid-column: span 2;
    background-color: #00ff88;
    color: black;
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

.save-button:hover {
    background-color: #00C851;
    border-color: #00C851;
    color: #fff;
    transform: translate(-2px, -2px);
}


/*Venues Managed*/
/* Venues Managed Section */
.subsection {
    border: 4px solid #B4741E;
    background-color: #FFE066;

}

.reservation-section{
    max-height: none;
    overflow: visible;
}

/* Optional scrollbar styling for better UX */
.reservation-section::-webkit-scrollbar {
  width: 8px;
}
.reservation-section::-webkit-scrollbar-thumb {
  background-color: #ccc;
  border-radius: 4px;
}

/* No venues message */
.no-venues-message {
    font-size: 1rem;
    color: #4B5563;
    font-weight: 600;
}

/* Venues list container */
.venues-list {
    display: grid;
    gap: 1.5rem;
}

/* Venue card */
.venue-card {
    border: 4px solid black;
    border-radius: 0.75rem;
    padding: 2rem;
    background-color: #FFF6CF;
    box-shadow: 10px 10px 0 #000;
}

/* Venue card header */
.venue-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    border-bottom: 2px solid #000;
    padding-bottom: 1rem;
}

/* Venue card info */
.venue-card-info {
    margin-right: 1rem;
}

/* Venue name */
.venue-name {
    font-size: 1.25rem;
    font-weight: 700;
    color: #333333;
}

/* Venue address */
.venue-address {
    font-size: 1rem;
    color: #4B5563;
}

/* Venue capacity and price */
.venue-capacity-price {
    font-size: 1rem;
    color: #4B5563;
}

/* Delete button */
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

/* Venue edit button */
.venue-edit-button {
    background-color: #00ff88;
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

.venue-edit-button:hover {
    background-color: #81bde6;
    transform: scale(1.05);
}

/* Add button */
.add-venue-button {
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
    margin-bottom: 20px;
}

.add-venue-button:hover {
    background-color: #f1ae5c;
    transform: scale(1.05);
}

.button-container {
    display: flex;
    align-items: center; /* Vertically align buttons */
    gap: 10px; /* Small gap between buttons */
}

/* No reservations message */
.no-reservations {
    font-size: 1rem;
    color: #4B5563;
    font-weight: 600;
}

/* Reservation item */
.reservation-item {
    border: 4px solid black;
    border-radius: 0.75rem;
    padding: 1.5rem;
    background-color: #FFF6CF;
    margin-bottom: 1.5rem;
    box-shadow: 10px 10px 0 #000;
    transition: background-color 0.3s ease; /* Smooth transition for color changes */
}

/* Status-specific colors for reservation items */
.reservation-item.not-accepted {
    background-color: #ff9fa6; /* Lighter red background */
}

.reservation-item.happened-paid {
    background-color: #a4ff99; /* Lighter green background */
}

.reservation-item.confirmed-deposited {
    background-color: #a3c8ff; /* Lighter blue background */
}


/* Reservation status */
.status {
    font-weight: bold;
    color: #4B5563;
}

/* Actions section */
.actions {
    margin-top: 1rem;
}

/* Buttons */
.accept-btn,
.reject-btn,
.update-btn {
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
    margin-right: 1rem;
}

.accept-btn {
    background-color: #4CAF50;
    color: white;
}

.accept-btn:hover {
    background-color: #388E3C;
}

.reject-btn {
    background-color: #F44336;
    color: white;
}

.reject-btn:hover {
    background-color: #D32F2F;
}

.update-btn {
    background-color: #2394d4;
    color: white;
}

.update-btn:hover {
    background-color: #1766ce;
}

/* Status select */
.status-select {
    padding: 0.5rem;
    font-size: 1rem;
    border: 2px solid black;
    background-color: #FFF6CF;
    margin-right: 1rem;
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


</style>
</head>
<body class="custom-bg min-h-screen content">
<!-- Navbar -->
<header id="navbar">
  <nav class="navbar-container">
>
    <a href="#" class="logo">
      <img src="Images/Logo/TaraDito.png" alt="TaraDito Logo" />
    </a>
    <ul class="nav-links">
      <li><a href="product.php" class="nav-link">Venues</a></li>
      <li><a href="top_venues_chart.php" class="nav-link">Top picks</a></li>
      <li><a href="DashboardAdmin.php" class="nav-link">Dashboard</a></li>
    </ul>

  </nav>
</header>

<div class="max-w-6xl mx-auto p-6">
<link rel="stylesheet" href="style.css">

<div class="dashboard">
    <h1 class="dashboard-title">Manager Dashboard</h1>

    <div class="profile-header">
        <div class="profile-info">
            <div>
                <h2 class="manager-name"><?php echo htmlspecialchars($manager['firstName'] . ' ' . $manager['lastName']); ?></h2>
                <p class="role">Venue Manager</p>
            </div>
        </div>
        <button onclick="document.getElementById('edit_form').classList.toggle('hidden'); this.classList.toggle('hidden');"
                class="edit-button">
            <img src="Images/EditIcon.png" alt="Edit" class="edit-icon">
        </button>
        
    </div>

    <!-- Profile Info -->
    <div id="profile_info" class="profile-info-grid">
        <div class="info-card">
            <label class="info-label">First Name</label>
            <p class="info-text"><?php echo htmlspecialchars($manager['firstName']); ?></p>
        </div>
        <div class="info-card">
            <label class="info-label">Last Name</label>
            <p class="info-text"><?php echo htmlspecialchars($manager['lastName']); ?></p>
        </div>
        <div class="info-card full-width">
            <label class="info-label">Email</label>
            <p class="info-text"><?php echo htmlspecialchars($manager['managerEmail']); ?></p>
        </div>
        <div class="info-card full-width">
            <label class="info-label">About</label>
            <p class="info-text"><?php echo htmlspecialchars($manager['managerAbout']); ?></p>
        </div>
    </div>
    <!--log out-->
    <form action="logout.php" method="POST" style="margin-top: 10px;">
    <button type="submit" class="delete-button">
        Log Out
    </button>
    </form>

    <!-- Edit Form -->
    <form id="edit_form" action="update_manager.php" method="POST" class="edit-form hidden">
        <div class="form-group">
            <label for="firstName">First Name</label>
            <input type="text" name="firstName" id="firstName" value="<?php echo htmlspecialchars($manager['firstName']); ?>" required>
        </div>
        <div class="form-group">
            <label for="lastName">Last Name</label>
            <input type="text" name="lastName" id="lastName" value="<?php echo htmlspecialchars($manager['lastName']); ?>" required>
        </div>
        <div class="form-group full-width">
            <label for="managerEmail">Email</label>
            <input type="email" name="managerEmail" id="managerEmail" value="<?php echo htmlspecialchars($manager['managerEmail']); ?>" required>
        </div>
        <div class="form-group full-width">
            <label for="managerAbout">About</label>
            <textarea name="managerAbout" id="managerAbout" rows="5" required><?php echo htmlspecialchars($manager['managerAbout']); ?></textarea>
        </div>
        <button type="submit" name="update_manager" class="save-button">Save Changes</button>
    </form>
</div>

 <!-- Venues Managed Section -->
<div class="dashboard subsection resrvation-section">
    <h2 class="dashboard-title">Venues Managed</h2>

    <!-- Add button -->
    <a href="<?= PROJECT_ROOT ?>/src/venue_add.php" class="add-venue-button">
        + Add Venue
    </a>

    <?php if (empty($venues)): ?>
        <p class="no-venues-message">No venues managed yet.</p>
    <?php else: ?>
        <div class="venues-list">
            <?php foreach ($venues as $venue): ?>
                <div class="venue-card">
                    <div class="venue-card-header">
                        <div class="venue-card-info">
                            <h3 class="venue-name"><?php echo htmlspecialchars($venue['venueName']); ?></h3>
                            <p class="venue-address"><?php echo htmlspecialchars($venue['barangayAddress']); ?>, <?php echo htmlspecialchars($venue['cityAddress']); ?></p>
                        </div>
                       <div class="button-container">
                        <a href="<?= PROJECT_ROOT ?>/src/venue_editing.php?id=<?php echo $venue['venueID']; ?>" class="venue-edit-button">
                            Edit
                        </a>
                        <form action="venue_delete.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this venue?');">
                            <input type="hidden" name="venueID" value="<?= $venue['venueID']; ?>">
                            <button type="submit" class="delete-button">Delete</button>
                        </form>
                        </div>


                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="dashboard subsection reservation-section">
    <h2 class="dashboard-title">Venue Requests</h2>
    <div class = "venue-list">
    <?php if ($requestResult->num_rows > 0): ?>
            <?php while ($request = $requestResult->fetch_assoc()): ?>
                <div class="venue-card">
                    <div class="venue-card-header">
                        <div class="venue-card-info">
                            <h3 class="text-xl font-extrabold uppercase mb-2"><?= htmlspecialchars($request['venueName']) ?></h3>
                            <p><strong>Barangay:</strong> <?= htmlspecialchars($request['barangayAddress']) ?></p>
                            <p><strong>City:</strong> <?= htmlspecialchars($request['cityAddress']) ?></p>
                            <p><strong>Status:</strong> <span class="font-semibold"><?= htmlspecialchars($request['status']) ?></span></p>
                        </div>
                        <div class="button-container">
                            <a href="?delete_id=<?= $request['requestID'] ?>" class="delete-button">Delete</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
    <?php else: ?>
        <p class="text-black font-bold">No venue requests submitted.</p>
    <?php endif; ?>
    </div>
</div>


<!-- Reservation Messages Section -->
<div class="dashboard subsection">
    <h2 class="dashboard-title">Reservations</h2>
    <a href="download_reservations.php" class="add-venue-button">Download CSV</a>
    <?php if (empty($reservations)): ?>
        <p class="no-reservations">No reservations yet.</p>
    <?php else: ?>
        <?php foreach ($reservations as $reservation): ?>
            <div class="reservation-item <?php 
                // Apply the appropriate class based on the reservation's status
                if ($reservation['statusText'] === 'Not Accepted') {
                    echo 'not-accepted';
                } elseif (in_array($reservation['statusText'], ['Happened', 'Paid'])) {
                    echo 'happened-paid';
                } elseif (in_array($reservation['statusText'], ['Confirmed', 'Deposited'])) {
                    echo 'confirmed-deposited';
                }
            ?>">
                <span>
                    <strong>
                        <?php echo htmlspecialchars($reservation['firstName'] . ' ' . $reservation['lastName']); ?>
                    </strong>
                    (<?php echo htmlspecialchars($reservation['venueName']); ?>) –
                    Status: <span class="status"><?php echo htmlspecialchars($reservation['statusText']); ?></span>
                </span>

                <div class="actions">
                    <?php if ($reservation['statusText'] === 'Pending'): ?>
                        <form method="POST" class="action-form">
                            <input type="hidden" name="reservationID" value="<?php echo $reservation['reservationID']; ?>">
                            <button type="submit" name="accept_reservation" class="accept-btn">Accept</button>
                            <button type="submit" name="reject_reservation" class="reject-btn">Reject</button>
                        </form>
                    <?php elseif (in_array($reservation['statusText'], ['Not Accepted','Confirmed','Deposited', 'Happened', 'Paid'])): ?>
                        <form method="POST" class="action-form">
                            <input type="hidden" name="reservationID" value="<?php echo $reservation['reservationID']; ?>">
                            <select name="new_status" class="status-select">
                                <?php foreach ($statuses as $status): ?>
                                    <option value="<?= $status['statusID']; ?>"
                                        <?php if ($status['statusText'] === $reservation['statusText']) echo 'selected'; ?>>
                                        <?= htmlspecialchars($status['statusText']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="update_status" class="update-btn">Update</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Analytics Section -->
<div class="dashboard subsection">
    <h2 class="dashboard-title">Venue Analytics</h2>
    <a href="download_analytics.php" class="add-venue-button">Download CSV</a>

    <!-- Monthly Reservations per Venue -->
    <h3 class="dashboard-subtitle">Monthly Reservations per Your Venues</h3>
    <?php if (empty($monthlyReservations)): ?>
        <p class="no-reservations">No monthly reservation data available.</p>
    <?php else: ?>
        <?php foreach ($monthlyReservations as $month => $venues): ?>
            <div class="reservation-item confirmed-deposited">
                <span><strong><?php echo htmlspecialchars($month); ?></strong></span>
                <ul>
                    <?php foreach ($venues as $venue): ?>
                        <li>
                            <?php echo htmlspecialchars($venue['venueName']); ?> –
                            <span class="status"><?php echo $venue['totalReservations']; ?> reservations</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Views and Likes per Venue -->
    <h3 class="dashboard-subtitle">Venue Views and Likes</h3>
    <?php if (empty($viewsAndLikes)): ?>
        <p class="no-reservations">No view/like data available.</p>
    <?php else: ?>
        <?php foreach ($viewsAndLikes as $item): ?>
            <div class="reservation-item not-accepted">
                <span>
                    <strong><?php echo htmlspecialchars($item['venueName']); ?></strong> –
                    Views: <span class="status"><?php echo $item['totalViews']; ?></span>,
                    Likes: <span class="status"><?php echo $item['totalLikes']; ?></span>
                </span>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>



</body>
</html>




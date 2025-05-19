<?php
// logging in as superadmin verification
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include('db_connection.php');
define('PROJECT_ROOT', rtrim(dirname($_SERVER['SCRIPT_NAME'], 2), '/')); // for file

$requestQuery = "SELECT vr.*, m.lastName, m.firstName, m.managerEmail 
                 FROM venuerequests vr
                 JOIN managerdata m ON vr.managerID = m.managerID
                 ORDER BY vr.submitted_At DESC";

$requestResult = $conn->query($requestQuery);
$venueRequests = [];
if ($requestResult && $requestResult->num_rows > 0) {
    while ($row = $requestResult->fetch_assoc()) {
        $venueRequests[] = $row;
    }
}

if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $delStmt = $conn->prepare("DELETE FROM venueData WHERE venueID = ?");
    $delStmt->bind_param("i", $delete_id);
    $delStmt->execute();
    $delStmt->close();

    header("Location: superadmin.php");
    exit;
}

// Fetch all venues
$sql = "SELECT v.venueID, v.venueName, v.barangayAddress, v.cityAddress, m.firstName, m.lastName 
        FROM venueData v
        LEFT JOIN managervenue mv ON v.venueID = mv.venueID
        LEFT JOIN managerdata m ON mv.managerID = m.managerID
        ORDER BY v.venueName ASC";

$result = $conn->query($sql);

$venues = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['managerFirstName'] = $row['firstName'];
        $row['managerLastName'] = $row['lastName'];
        $venues[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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

.full-width {
    grid-column: span 2;
}

.hidden {
    display: none;
}

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
    max-height: 400px;
    overflow-y: auto;
    padding-right: 8px;
}

.venues-list-scrollable {
    max-height: 400px; /* fixed height */
    overflow-y: auto;
    padding-right: 8px;
}

.venues-list-scrollable::-webkit-scrollbar {
    width: 8px;
}
.venues-list-scrollable::-webkit-scrollbar-thumb {
    background-color: #ccc;
    border-radius: 4px;
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
    
    <!-- Logo on the left -->
    <a href="#" class="logo">
      <img src="Images/Logo/TaraDito.png" alt="TaraDito Logo" />
    </a>

    <!-- Navigation Links on the right -->
    <ul class="nav-links">
      <li><a href="product.php" class="nav-link">Venues</a></li>
      <li><a href="top_venues_chart.php" class="nav-link">Top picks</a></li>
      <li><a href="superAdmin.php" class="nav-link">Dashboard</a></li>
    </ul>

  </nav>
</header>

<div class="max-w-6xl mx-auto p-6">


<link rel="stylesheet" href="style.css">

<div class="dashboard">
    <h1 class="dashboard-title">Admin Dashboard</h1>
    <div class="profile-header">
        <div class="profile-info">
            <h2 class="manager-name"> TARADITO ADMIN</h2>
            <p class="role">ADMIN</p>
        </div>    
    </div>
    <!--log out-->
    <form action="logout.php" method="POST" style="margin-top: 10px;">
    <button type="submit" class="delete-button">
        Log Out
    </button>
    </form>
</div>



  <!-- venue reqyest -->
<div class="dashboard subsection resrvation-section" style="margin-top: 3rem;">
    <h2 class="dashboard-title">Venue Requests</h2>

    <?php if (empty($venueRequests)): ?>
        <p class="no-venues-message">No venue requests found.</p>
    <?php else: ?>
        <div class="venues-list venues-list-scrollable">
            <?php foreach ($venueRequests as $request): ?>
                <div class="venue-card">
                    <div class="venue-card-header">
                        <div class="venue-card-info">
                            <h3 class="venue-name"><?php echo htmlspecialchars($request['venueName'] ?? 'N/A'); ?></h3>
                            <p><strong>Manager:</strong> <?php echo htmlspecialchars($request['firstName'] . ' ' . $request['lastName']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($request['managerEmail']); ?></p>
                            <p><strong>Request Date:</strong> <?php echo htmlspecialchars(date("F j, Y", strtotime($request['submitted_at']))); ?></p>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($request['status']); ?></p>
                        </div>
                        <div class="button-container">
                            <form method="POST" action="handle_request.php" style="display: inline;">
                                <input type="hidden" name="requestID" value="<?php echo $request['requestID']; ?>">
                                <button type="submit" name="action" value="accept" class="accept-btn">Accept</button>
                            </form>
                            <form method="POST" action="handle_request.php" style="display: inline;">
                                <input type="hidden" name="requestID" value="<?php echo $request['requestID']; ?>">
                                <button type="submit" name="action" value="reject" class="reject-btn">Reject</button>
                            </form>
                            <a href="request_details.php?id=<?php echo $request['requestID']; ?>" class="venue-edit-button">
                                View More Info
                            </a>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="dashboard subsection resrvation-section" style="margin-top: 3rem;">
    <h2 class="dashboard-title">All Venues</h2>
    <?php if (empty($venues)): ?>
    <p class="no-venues-message">No venues managed yet.</p>
    <?php else: ?>
    <div class="venues-list">
        <?php foreach ($venues as $venue): ?>
            <div class="venue-card">
                <div class="venue-card-header">
                    <div class="venue-card-info">
                        <h3 class="venue-name"><?php echo htmlspecialchars($venue['venueName']); ?></h3>
                        <p class="venue-address">
                            <?php 
                                echo htmlspecialchars($venue['barangayAddress']) . ', ' . 
                                     htmlspecialchars($venue['cityAddress']); 
                            ?>
                        </p>
                        <p class="venue-manager">
                            Managed by: <?php 
                                echo htmlspecialchars($venue['managerFirstName'] . ' ' . $venue['managerLastName']); 
                            ?>
                        </p>
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






</body>
</html>




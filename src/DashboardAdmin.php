<?php
    // logging in as manager verification
    session_start();
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

    // Fetch venues under the manager
    $sql = "SELECT v.venueID, v.venueName, v.barangayAddress, v.cityAddress, v.maxCapacity, v.priceRangeID, pr.priceRangeText
            FROM venueData v
            JOIN managervenue mv ON v.venueID = mv.venueID
            JOIN priceRange pr ON v.priceRangeID = pr.priceRangeID
            WHERE mv.managerID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $managerID);
    $stmt->execute();

    if ($stmt->errno) {
        error_log("Error executing venue query: (" . $stmt->errno . ") " . $stmt->error);
        echo "<p class='text-red-600'>Database error fetching venues.</p>"; // Display a user-friendly error
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
                u.firstName, u.lastName, v.venueName
            FROM userReserved ur
            JOIN reservationStatus rs ON ur.statusID = rs.statusID
            JOIN userData u ON ur.userID = u.userID
            JOIN venueData v ON ur.venueID = v.venueID
            JOIN managerVenue mv ON v.venueID = mv.venueID
            WHERE mv.managerID = ?
            ORDER BY ur.reservationDate DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $managerID);  // "i" for integer
    $stmt->execute();
    $reservations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Fetch available reservation statuses
    $statusStmt = $conn->prepare("SELECT statusID, statusText FROM reservationStatus WHERE statusID >= 2 AND statusID <= 6");
    $statusStmt->execute();
    $statuses = $statusStmt->get_result()->fetch_all(MYSQLI_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle accepting a reservation
        if (isset($_POST['accept_reservation'])) {
            $reservationID = $_POST['reservationID'];
            $stmt = $conn->prepare("UPDATE userReserved SET statusID = 3 WHERE reservationID = ?");
            $stmt->bind_param("i", $reservationID);  // "i" for integer
            $stmt->execute();
        }

        // Handle rejecting a reservation
        if (isset($_POST['reject_reservation'])) {
            $reservationID = $_POST['reservationID'];
            $stmt = $conn->prepare("UPDATE userReserved SET statusID = 2 WHERE reservationID = ?");
            $stmt->bind_param("i", $reservationID);  // "i" for integer
            $stmt->execute();
        }

        // Handle updating reservation status
        if (isset($_POST['update_status'])) {
            $reservationID = $_POST['reservationID'];
            $newStatusID = $_POST['new_status'];
            $stmt = $conn->prepare("UPDATE userReserved SET statusID = ? WHERE reservationID = ?");
            $stmt->bind_param("ii", $newStatusID, $reservationID);  // "ii" for two integers
            $stmt->execute();
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.0.0/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tailwindcss@3.0.0/dist/tailwind.min.js"></script>
    <link href="./output.css" rel="stylesheet">
    <link href="Dashboard.css" rel="stylesheet">
    <style>
     


    </style>
</head>
<body class="custom-bg min-h-screen">
<div class="max-w-6xl mx-auto p-6">


<link rel="stylesheet" href="style.css">

<div class="dashboard">
    <h1 class="dashboard-title">Manager Dashboard</h1>

    <div class="profile-header">
        <div class="profile-info">
            <img src="Images/1.jpg" alt="Profile Avatar" class="profile-avatar">
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
<div class="dashboard subsection">
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
                            <p class="venue-capacity-price">Capacity: <?php echo htmlspecialchars($venue['maxCapacity']); ?> | Price: <?php echo htmlspecialchars($venue['priceRangeText']); ?></p>
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
<!-- Reservation Messages Section -->
<div class="dashboard subsection">
    <h2 class="dashboard-title">Reservations</h2>

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



</body>
</html>




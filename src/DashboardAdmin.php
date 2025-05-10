<?php
    // logging in as manager verification
    session_start();
    include('db_connection.php');
    define('PROJECT_ROOT', rtrim(dirname($_SERVER['SCRIPT_NAME'], 2), '/')); // for file
    if (!isset($_SESSION['managerID'])) {
        echo "Manager not logged in.";
        exit;
    }
    $managerID = $_SESSION['managerID'];
    $stmt = $conn->execute_query("SELECT firstName, lastName, managerEmail, managerAbout FROM managerData WHERE managerID = ?", [$managerID]);
    $manager = $stmt->fetch_assoc();
    if (!$manager) {
        echo "Manager profile not found!";
        exit;
    }

    // venues under the manager
    $sql = " SELECT v.venueID, v.venueName, v.barangayAddress, v.cityAddress, v.maxCapacity, v.priceRangeID, pr.priceRangeText
        FROM venueData v
        JOIN managerVenue mv ON v.venueID = mv.venueID
        JOIN priceRange pr ON v.priceRangeID = pr.priceRangeID
        WHERE mv.managerID = ?
    ";
    $stmt = $conn->execute_query($sql, [$managerID]);
    $venues = $stmt->fetch_all(MYSQLI_ASSOC);

    // reservations for the manager's venues
        $sql = "SELECT ur.reservationID, ur.reservationDate, rs.statusText,
                u.firstName, u.lastName, v.venueName
            FROM userReserved ur
            JOIN reservationStatus rs ON ur.statusID = rs.statusID
            JOIN userData u ON ur.userID = u.userID
            JOIN venueData v ON ur.venueID = v.venueID
            JOIN managerVenue mv ON v.venueID = mv.venueID
            WHERE mv.managerID = ?
            ORDER BY ur.reservationDate DESC
        ";

        $stmt = $conn->execute_query($sql, [$managerID]);
        $reservations = $stmt->fetch_all(MYSQLI_ASSOC);

    // reservation manager interactions
    $statusStmt = $conn->execute_query("SELECT statusID, statusText FROM reservationStatus WHERE statusID >= 2 AND statusID <= 6");
    $statuses = $statusStmt->fetch_all(MYSQLI_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['accept_reservation'])) {
            $reservationID = $_POST['reservationID'];
            $conn->execute_query("UPDATE userReserved SET statusID = 3 WHERE reservationID = ?", [$reservationID]);
        }

        if (isset($_POST['reject_reservation'])) {
            $reservationID = $_POST['reservationID'];
            $conn->execute_query("UPDATE userReserved SET statusID = 2 WHERE reservationID = ?", [$reservationID]);
        }
        if (isset($_POST['update_status'])) {
            $reservationID = $_POST['reservationID'];
            $newStatusID = $_POST['new_status'];
            $conn->execute_query("UPDATE userReserved SET statusID = ? WHERE reservationID = ?", [$newStatusID, $reservationID]);
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
    <link href="Dashbord.css" rel="stylesheet">
    <style>
      

    </style>
</head>
<body class="custom-bg min-h-screen">
<div class="max-w-6xl mx-auto p-6">


            
    <!-- Manager Profile Dashboard -->
    <div class="p-8 rounded-xl border-2 border-[#D36A6A] mb-8" style="background-color: #FDE8E5;">
    <h1 class="text-4xl font-bold mb-6 text-[#333333] tracking-tight leading-tight">
        Manager Dashboard
    </h1>

    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <img src="Images/1.jpg" alt="Profile Avatar"
                class="w-16 h-16 rounded-full border-2 border-[#D36A6A] object-cover">
            <div>
                <h2 class="text-2xl font-bold text-[#333333]">
                    <?php echo htmlspecialchars($manager['firstName'] . ' ' . $manager['lastName']); ?>
                </h2>
                <p class="text-sm text-[#D36A6A]">Venue Manager</p>
            </div>
        </div>
     <button onclick="document.getElementById('edit_form').classList.toggle('hidden'); this.classList.toggle('hidden');"
    class="bg-[#D36A6A] hover:bg-[#A94444] text-white font-semibold px-6 py-2 rounded-md transition duration-200 flex items-center justify-center">
    <img src="Images/EditIcon.png" alt="Edit" class="w-5 h-5">
</button>

    </div>

    <!-- Display Profile Info -->
    <div id="profile_info">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- First Name -->
            <div class="p-4 rounded-md border-2 border-[#D36A6A]" style="background-color: #FFF7F3;">
                <label class="block text-sm font-medium text-[#333333]">First Name</label>
                <p class="mt-1 text-[#333333]"><?php echo htmlspecialchars($manager['firstName']); ?></p>
            </div>

            <!-- Last Name -->
            <div class="p-4 rounded-md border-2 border-[#D36A6A]" style="background-color: #FFF7F3;">
                <label class="block text-sm font-medium text-[#333333]">Last Name</label>
                <p class="mt-1 text-[#333333]"><?php echo htmlspecialchars($manager['lastName']); ?></p>
            </div>

            <!-- Email -->
            <div class="p-4 rounded-md border-2 border-[#D36A6A] col-span-1 md:col-span-2" style="background-color: #FFF7F3;">
                <label class="block text-sm font-medium text-[#333333]">Email</label>
                <p class="mt-1 text-[#333333]"><?php echo htmlspecialchars($manager['managerEmail']); ?></p>
            </div>

            <!-- About -->
            <div class="p-4 rounded-md border-2 border-[#D36A6A] col-span-1 md:col-span-2" style="background-color: #FFF7F3;">
                <label class="block text-sm font-medium text-[#333333]">About</label>
                <p class="mt-1 text-[#333333]"><?php echo htmlspecialchars($manager['managerAbout']); ?></p>
            </div>
        </div>
    </div>
    
<!-- Edit form: hidden by default -->
<form id="edit_form" action="update_manager.php" method="POST" class="grid-cols-1 md:grid-cols-2 gap-8 mt-8 hidden">
    <!-- First Name -->
    <div class="bg-gray-50 p-6 rounded-xl shadow-inner">
        <label for="firstName" class="block text-xs text-gray-500 mb-2">First Name</label>
        <input 
            type="text" 
            name="firstName" 
            id="firstName" 
            value="<?php echo htmlspecialchars($manager['firstName']); ?>" 
            class="w-full bg-white p-4 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-300"
            required
        >
    </div>

    <!-- Last Name -->
    <div class="bg-gray-50 p-6 rounded-xl shadow-inner">
        <label for="lastName" class="block text-xs text-gray-500 mb-2">Last Name</label>
        <input 
            type="text" 
            name="lastName" 
            id="lastName" 
            value="<?php echo htmlspecialchars($manager['lastName']); ?>" 
            class="w-full bg-white p-4 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-300"
            required
        >
    </div>

    <!-- Email -->
    <div class="bg-gray-50 p-6 rounded-xl shadow-inner col-span-1 md:col-span-2">
        <label for="managerEmail" class="block text-xs text-gray-500 mb-2">Email</label>
        <input 
            type="email" 
            name="managerEmail" 
            id="managerEmail" 
            value="<?php echo htmlspecialchars($manager['managerEmail']); ?>" 
            class="w-full bg-white p-4 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-300"
            required
        >
    </div>

    <!-- About -->
    <div class="bg-gray-50 p-6 rounded-xl shadow-inner col-span-1 md:col-span-2">
        <label for="managerAbout" class="block text-xs text-gray-500 mb-2">About</label>
        <textarea 
            name="managerAbout" 
            id="managerAbout" 
            rows="5" 
            class="w-full bg-white p-4 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-300"
            required
        ><?php echo htmlspecialchars($manager['managerAbout']); ?></textarea>
    </div>

    <!-- Save Button -->
    <button 
        type="submit" 
        name="update_manager" 
        class="inline-flex items-center gap-3 bg-green-500 hover:bg-green-600 text-white font-medium px-8 py-4 rounded-full shadow-lg transition-all duration-300 transform save-button"
    >
        Save Changes
    </button>
</form>

    

    </div>
<!-- Venues Managed Section -->
<div class="border-2 border-[#B4741E] bg-[#FFE066] p-6 mb-8 rounded-lg" style="background-color: #FFE066;">
    <h2 class="text-xl font-semibold mb-4">Venues Managed</h2>

    <?php if (empty($venues)): ?>
        <p class="text-gray-600">No venues managed yet.</p>
    <?php else: ?>
        <div class="grid gap-6">
            <?php foreach ($venues as $venue): ?>
                <div class="border-2 border-[#B4741E] rounded-lg p-6" style="background-color: #FFF6CF;">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($venue['venueName']); ?></h3>
                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($venue['barangayAddress']); ?>, <?php echo htmlspecialchars($venue['cityAddress']); ?></p>
                            <p class="text-sm text-gray-600">Capacity: <?php echo htmlspecialchars($venue['maxCapacity']); ?> | Price: <?php echo htmlspecialchars($venue['priceRangeText']); ?></p>
                        </div>
                        <a 
    href="<?= PROJECT_ROOT ?>/src/venue_editing.php?id=<?php echo $venue['venueID']; ?>" 
    class="edit-button">
    Edit
</a>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

   <!-- Reservation Messages Section -->
<div class="reservation-section">
    <h2 class="reservation-title">Reservations</h2>

    <?php if (empty($reservations)): ?>
        <p class="no-reservations">No reservations yet.</p>
    <?php else: ?>
        <?php foreach ($reservations as $reservation): ?>
            <div class="reservation-item">
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

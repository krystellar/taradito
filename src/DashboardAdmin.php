<?php
    // logging in as manager verification
    session_start();
    include('db_connection.php');
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
    $sql = "
        SELECT v.venueID, v.venueName, v.barangayAddress, v.cityAddress, v.maxCapacity, v.priceRangeID, pr.priceRangeText
        FROM venueData v
        JOIN managerVenue mv ON v.venueID = mv.venueID
        JOIN priceRange pr ON v.priceRangeID = pr.priceRangeID
        WHERE mv.managerID = ?
    ";
    $stmt = $conn->execute_query($sql, [$managerID]);
    $venues = $stmt->fetch_all(MYSQLI_ASSOC);
    
    // update venues
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_venue'])) {
        $venueID = $_POST['venue_id'];
        $venueName = $_POST['venue_name'];
        $barangay = $_POST['barangay_address'];
        $city = $_POST['city_address'];
        $capacity = $_POST['capacity'];
        $priceRange = $_POST['price_range'];

        $updateSql = " UPDATE venueData
            SET venueName = ?, barangayAddress = ?, cityAddress = ?, maxCapacity = ?, priceRangeID = ?
            WHERE venueID = ?";
        $conn->execute_query($updateSql, [$venueName, $barangay, $city, $capacity, $priceRange, $venueID]);

        // after updating, redirect to the same page to see the changes
        header("Location: ".$_SERVER['PHP_SELF']);
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
    <link href="ManagerDashboard.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-gray-100 to-gray-200 min-h-screen">
<div class="max-w-6xl mx-auto p-6">

            <h1 class="text-3xl font-bold mb-8">Manager Dashboard</h1>

            
    <!-- Manager Profile Dashboard -->
    <div class="bg-white p-8 rounded-3xl shadow-lg transition-all duration-300 mb-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <img src="Images/1.jpg" alt="Profile Avatar" class="w-16 h-16 rounded-full border-2 border-blue-300 shadow-sm object-cover profile-avatar">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($manager['firstName'] . ' ' . $manager['lastName']); ?></h2>
                    <p class="text-sm text-blue-500">Venue Manager</p>
                </div>
            </div>
            <button onclick="document.getElementById('edit_form').classList.toggle('hidden'); this.classList.toggle('hidden');" 
            class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-6 py-2 rounded-full shadow-sm transition duration-200 flex items-center justify-center edit-button">
            <img src="Images/EditIcon.png" alt="Edit" class="w-5 h-5">
        </button>

        </div>
            <!-- Display Profile Info (No Form) -->
            <div id="profile_info">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- First Name -->
                    <div class="bg-gray-50 p-4 rounded-xl shadow-inner info-block">
                        <label class="info-label">First Name</label>
                        <p class="info-content"><?php echo htmlspecialchars($manager['firstName']); ?></p>
                    </div>

                    <!-- Last Name -->
                    <div class="bg-gray-50 p-4 rounded-xl shadow-inner info-block">
                        <label class="info-label">Last Name</label>
                        <p class="info-content"><?php echo htmlspecialchars($manager['lastName']); ?></p>
                    </div>

                    <!-- Email -->
                    <div class="bg-gray-50 p-4 rounded-xl shadow-inner col-span-1 md:col-span-2 info-block">
                        <label class="info-label">Email</label>
                        <p class="info-content"><?php echo htmlspecialchars($manager['managerEmail']); ?></p>
                    </div>

                    <!-- About -->
                    <div class="bg-gray-50 p-4 rounded-xl shadow-inner col-span-1 md:col-span-2 info-block">
                        <label class="info-label">About</label>
                        <p class="info-content"><?php echo htmlspecialchars($manager['managerAbout']); ?></p>
                    </div>

                </div>
            </div>

            <!-- Edit form: hidden by default -->
            <form id="edit_form" action="update_manager.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 hidden">
                <!-- First Name -->
                <div class="bg-gray-50 p-4 rounded-xl shadow-inner">
                    <label for="firstName" class="block text-xs text-gray-500 mb-1">First Name</label>
                    <input 
                        type="text" 
                        name="firstName" 
                        id="firstName" 
                        value="<?php echo htmlspecialchars($manager['firstName']); ?>" 
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
                        value="<?php echo htmlspecialchars($manager['lastName']); ?>" 
                        class="w-full bg-white p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-300"
                        required
                    >
                </div>

                <!-- Email -->
                <div class="bg-gray-50 p-4 rounded-xl shadow-inner col-span-1 md:col-span-2">
                    <label for="managerEmail" class="block text-xs text-gray-500 mb-1">Email</label>
                    <input 
                        type="email" 
                        name="managerEmail" 
                        id="managerEmail" 
                        value="<?php echo htmlspecialchars($manager['managerEmail']); ?>" 
                        class="w-full bg-white p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-300"
                        required
                    >
                </div>

                <!-- About -->
                <div class="bg-gray-50 p-4 rounded-xl shadow-inner col-span-1 md:col-span-2">
                    <label for="managerAbout" class="block text-xs text-gray-500 mb-1">About</label>
                    <textarea 
                        name="managerAbout" 
                        id="managerAbout" 
                        rows="4" 
                        class="w-full bg-white p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-300"
                        required
                    ><?php echo htmlspecialchars($manager['managerAbout']); ?></textarea>
                </div>
            

            <!-- Save Button -->
            <button 
                type="submit" 
                name="update_manager" 
                class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-medium px-6 py-3 rounded-full shadow-lg transition-all duration-300 transform save-button"
            >
                Save Changes
            </button>

        </form>
    

        </form>
    </div>

            <!-- Venues Managed Section -->
        <!-- Venues Managed Section -->
    <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition-shadow duration-300 mb-8">
        <h2 class="text-xl font-semibold mb-4">Venues Managed</h2>

        <?php if (empty($venues)): ?>
            <p class="text-gray-600">No venues managed yet.</p>
        <?php else: ?>
            <div class="grid gap-6">
                <?php foreach ($venues as $venue): ?>
                    <div class="border border-gray-200 rounded-xl shadow-sm p-6 bg-gray-50 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($venue['venueName']); ?></h3>
                                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($venue['barangayAddress']); ?>, <?php echo htmlspecialchars($venue['cityAddress']); ?></p>
                                <p class="text-sm text-gray-600">Capacity: <?php echo htmlspecialchars($venue['maxCapacity']); ?> | Price: <?php echo htmlspecialchars($venue['priceRangeText']); ?></p>
                            </div>
                            <button type="button" onclick="document.getElementById('edit_form_<?php echo $venue['venueID']; ?>').classList.toggle('hidden')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                                Edit
                            </button>
                        </div>

                        <!-- Edit form: hidden by default -->
                        <form id="edit_form_<?php echo $venue['venueID']; ?>" method="POST" class="space-y-4 hidden bg-white p-4 rounded-lg border mt-4">
                            <input type="hidden" name="venue_id" value="<?php echo $venue['venueID']; ?>">
                            <input type="hidden" name="update_venue" value="1">

                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Venue Name</label>
                                    <input type="text" name="venue_name" value="<?php echo htmlspecialchars($venue['venueName']); ?>" class="w-full p-2 border rounded-lg" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Barangay</label>
                                    <input type="text" name="barangay_address" value="<?php echo htmlspecialchars($venue['barangayAddress']); ?>" class="w-full p-2 border rounded-lg" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">City</label>
                                    <input type="text" name="city_address" value="<?php echo htmlspecialchars($venue['cityAddress']); ?>" class="w-full p-2 border rounded-lg" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Max Capacity</label>
                                    <input type="number" name="capacity" value="<?php echo htmlspecialchars($venue['maxCapacity']); ?>" class="w-full p-2 border rounded-lg" required>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Price Range</label>
                                    <input type="text" name="price_range" value="<?php echo htmlspecialchars($venue['priceRangeID']); ?>" class="w-full p-2 border rounded-lg" required>
                                </div>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
</div>


</body>
</html>

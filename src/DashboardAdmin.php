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
</head>
<body class="bg-gray-100">

    <div class="container mx-auto p-8">
        <h1 class="text-3xl font-bold mb-8">Manager Dashboard</h1>

        <!-- Manager Profile Section -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h2 class="text-xl font-semibold mb-4">Manager's Profile</h2>
            <form action="update_manager.php" method="POST" class="space-y-4">
                <div>
                    <label for="manager_firstName" class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" name="firstName" id="firstName" value="<?php echo htmlspecialchars($manager['firstName']); ?>" class="w-full p-3 border rounded-lg" required>
                </div>
                <div>
                    <label for="manager_lastName" class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" name="lastName" id="lastName" value="<?php echo htmlspecialchars($manager['lastName']); ?>" class="w-full p-3 border rounded-lg" required>
                </div>
                <div>
                    <label for="manager_email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="managerEmail" id="managerEmail" value="<?php echo htmlspecialchars($manager['managerEmail']); ?>" class="w-full p-3 border rounded-lg" required>
                </div>
                <div>
                    <label for="manager_about" class="block text-sm font-medium text-gray-700">About</label>
                    <textarea name="managerAbout" id="managerAbout" rows="4" class="w-full p-3 border rounded-lg" required><?php echo htmlspecialchars($manager['managerAbout']); ?></textarea>
                </div>
                <button type="submit" name="update_manager" class="w-full bg-blue-500 text-white p-3 rounded-lg">Update Details</button>
            </form>
        </div>

        <!-- Venues Managed Section -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h2 class="text-xl font-semibold mb-4">Venues Managed</h2>

            <?php if (empty($venues)): ?>
                <p>No venues managed yet.</p>
            <?php else: ?>
                <table class="min-w-full table-auto mb-6">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 border-b text-left">Venue Name</th>
                            <th class="px-4 py-2 border-b text-left">Barangay Address</th>
                            <th class="px-4 py-2 border-b text-left">City Address</th>
                            <th class="px-4 py-2 border-b text-left">Capacity</th>
                            <th class="px-4 py-2 border-b text-left">Price Range</th>
                            <th class="px-4 py-2 border-b text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($venues as $venue): ?>
                            <tr>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($venue['venueName']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($venue['barangayAddress']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($venue['cityAddress']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($venue['maxCapacity']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($venue['priceRangeText']); ?></td>
                                <td class="px-4 py-2">
                                    <button type="button" onclick="document.getElementById('edit_form_<?php echo $venue['venueID']; ?>').classList.toggle('hidden')" class="bg-blue-500 text-white p-2 rounded-lg">Edit</button>
                                </td>
                            </tr>

                            <!-- hidden text fields for update -->
                            <tr id="edit_form_<?php echo $venue['venueID']; ?>" class="hidden">
                                <td colspan="6">
                                    <form method="POST" class="space-y-4">
                                        <input type="hidden" name="venue_id" value="<?php echo $venue['venueID']; ?>">

                                        <!-- mao na ning fields -->
                                        <div class="flex space-x-4">
                                            <div class="flex-1">
                                                <label for="venue_name_<?php echo $venue['venueID']; ?>" class="block text-sm font-medium text-gray-700">Venue Name</label>
                                                <input type="text" name="venue_name" value="<?php echo htmlspecialchars($venue['venueName']); ?>" class="w-full p-3 border rounded-lg" required>
                                            </div>
                                            <div class="flex-1">
                                                <label for="barangay_address_<?php echo $venue['venueID']; ?>" class="block text-sm font-medium text-gray-700">Barangay Address</label>
                                                <input type="text" name="barangay_address" value="<?php echo htmlspecialchars($venue['barangayAddress']); ?>" class="w-full p-3 border rounded-lg" required>
                                            </div>
                                            <div class="flex-1">
                                                <label for="city_address_<?php echo $venue['venueID']; ?>" class="block text-sm font-medium text-gray-700">City Address</label>
                                                <input type="text" name="city_address" value="<?php echo htmlspecialchars($venue['cityAddress']); ?>" class="w-full p-3 border rounded-lg" required>
                                            </div>
                                            <div class="flex-1">
                                                <label for="capacity_<?php echo $venue['venueID']; ?>" class="block text-sm font-medium text-gray-700">Capacity</label>
                                                <input type="number" name="capacity" value="<?php echo htmlspecialchars($venue['maxCapacity']); ?>" class="w-full p-3 border rounded-lg" required>
                                            </div>
                                        </div>
                                        <!-- dropdwon for pricerange -->
                                        <div class="flex space-x-4">
                                            <div class="flex-1">
                                                <label for="price_range_<?php echo $venue['venueID']; ?>" class="block text-sm font-medium text-gray-700">Price Range</label>
                                                <select name="price_range" class="w-full p-3 border rounded-lg" required>
                                                    <?php
                                                    $priceRanges=$conn->query("SELECT priceRangeID, priceRangeText FROM priceRange");
                                                    while ($priceRange = $priceRanges->fetch_assoc()):
                                                    ?>
                                                        <option value="<?php echo $priceRange['priceRangeID']; ?>"
                                                            <?php echo ($priceRange['priceRangeID'] == $venue['priceRangeID']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($priceRange['priceRangeText']); ?>
                                                        </option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <button type="submit" name="update_venue" class="bg-green-500 text-white p-3 rounded-lg w-full">Update Venue</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

</body>
</html>

<?php
session_start();
include('db_connection.php');
if (!isset($_SESSION['managerID'])) {
    header("Location: Login.php");
    exit;
}

$priceRangeSql = "SELECT * FROM priceRange";
    $resultPriceRange = $conn->query($priceRangeSql);

    if ($resultPriceRange->num_rows > 0) {
        $priceRanges = $resultPriceRange->fetch_all(MYSQLI_ASSOC);
    } else {
        $priceRanges = [];
    }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $managerID = $_SESSION['managerID']; 
    $venueName = mysqli_real_escape_string($conn, $_POST['venueName']);
    $barangayAddress = mysqli_real_escape_string($conn, $_POST['barangayAddress']);
    $cityAddress = mysqli_real_escape_string($conn, $_POST['cityAddress']);
    $venueDesc = mysqli_real_escape_string($conn, $_POST['venueDesc']);
    $landmarks = mysqli_real_escape_string($conn, $_POST['landmarks']);
    $routes = mysqli_real_escape_string($conn, $_POST['routes']);
    $priceRangeID = mysqli_real_escape_string($conn, $_POST['priceRange']); 
    $intimate  = isset($_POST['intimate'])  ? 1 : 0;
    $business  = isset($_POST['business'])  ? 1 : 0;
    $casual    = isset($_POST['casual'])    ? 1 : 0;
    $fun       = isset($_POST['fun'])       ? 1 : 0;
    $eventPlanner = isset($_POST['eventPlanner']) ? 1 : 0;
    $equipRentals = isset($_POST['equipRentals']) ? 1 : 0;
    $decoServices = isset($_POST['decoServices']) ? 1 : 0;
    $onsiteStaff = isset($_POST['onsiteStaff']) ? 1 : 0;
    $techSupport = isset($_POST['techSupport']) ? 1 : 0;
    $pwdFriendly = isset($_POST['pwdFriendly']) ? 1 : 0;
    $parking    = isset($_POST['parking'])    ? 1 : 0;
    $cateringServices = isset($_POST['cateringServices']) ? 1 : 0;
    $securityStaff = isset($_POST['securityStaff']) ? 1 : 0;
    $wifiAccess = isset($_POST['wifiAccess']) ? 1 : 0;
    $payCash = isset($_POST['payCash']) ? 1 : 0;
    $payElectronic = isset($_POST['payElectronic']) ? 1 : 0;
    $payBank = isset($_POST['payBank']) ? 1 : 0;
    $imgs = mysqli_real_escape_string($conn, $_POST['imgs']);
    $img2 = mysqli_real_escape_string($conn, $_POST['img2']);
    $img3 = mysqli_real_escape_string($conn, $_POST['img3']);
    $img4 = mysqli_real_escape_string($conn, $_POST['img4']);
    $img5 = mysqli_real_escape_string($conn, $_POST['img5']);

    // Insert into venueData table
    $sql = "INSERT INTO venueData (
            venueName,
            barangayAddress,
            cityAddress,
            venueDesc,
            landmarks,
            routes,
            priceRangeID,
            intimate,
            business,
            casual,
            fun,
            eventPlanner,
            equipRentals,
            decoServices,
            onsiteStaff,
            techSupport,
            pwdFriendly,
            parking,
            cateringServices,
            securityStaff,
            wifiAccess,
            payCash,
            payElectronic,
            payBank,
            imgs,
            img2,
            img3,
            img4,
            img5
        ) VALUES (
            '$venueName',
            '$barangayAddress',
            '$cityAddress',
            '$venueDesc',
            '$landmarks',
            '$routes',
            '$priceRangeID',
            $intimate,
            $business,
            $casual,
            $fun,
            $eventPlanner,
            $equipRentals,
            $decoServices,
            $onsiteStaff,
            $techSupport,
            $pwdFriendly,
            $parking,
            $cateringServices,
            $securityStaff,
            $wifiAccess,
            $payCash,
            $payElectronic,
            $payBank,
            '$imgs',
            '$img2',
            '$img3',
            '$img4',
            '$img5'
        );";

    if ($conn->query($sql)) {
        $venueID = $conn->insert_id;

        // Insert into managerVenue table to link manager and venue
        $managerVenueSql = "INSERT INTO managerVenue (managerID, venueID) VALUES ($managerID, $venueID)";
        
        if ($conn->query($managerVenueSql)) {
            echo "<p class='text-green-600 text-center mt-4'>Venue added successfully and linked to manager.</p>";
            header("Location: DashboardAdmin.php");
            exit;
        } else {
            echo "<p class='text-red-600 text-center mt-4'>Error linking venue to manager: " . $conn->error . "</p>";
        }
    } else {
        echo "<p class='text-red-600 text-center mt-4'>Error adding venue: " . $conn->error . "</p>";
    }

    $conn->close();
}
?>


<!-- HTML Form for venue details -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Venue | TARADITO</title>
    <link href="output.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <h1>Add New Venue</h1>
    <form action="venue_add.php" method="POST">
        <!-- Venue Info -->
        <label for="venueName">Venue Name:</label>
        <input type="text" name="venueName" id="venueName" required>

        <label for="barangayAddress">Barangay Address:</label>
        <input type="text" name="barangayAddress" id="barangayAddress" required>

        <label for="cityAddress">City Address:</label>
        <input type="text" name="cityAddress" id="cityAddress" required>

        <label for="venueDesc">Venue Description:</label>
        <textarea name="venueDesc" id="venueDesc" rows="4"></textarea>

        <label for="landmarks">Landmarks:</label>
        <input type="text" name="landmarks" id="landmarks">

        <label for="routes">Routes:</label>
        <input type="text" name="routes" id="routes">
        
        <label for="priceRange" class="form-label">Price Range:</label>
            <select name="priceRange" id="priceRange" class="form-select" required>
                <option value="">Select Price Range</option>
                <?php foreach ($priceRanges as $range): ?>
                    <option value="<?= $range['priceRangeID']; ?>">
                        <?= $range['priceRangeText']; ?>
                    </option>
                <?php endforeach; ?>
            </select>

        <!-- Categories (Checkboxes) -->
        <label><input type="checkbox" name="intimate" id="intimate"> Intimate</label>
        <label><input type="checkbox" name="business" id="business"> Business</label>
        <label><input type="checkbox" name="casual" id="casual"> Casual</label>
        <label><input type="checkbox" name="fun" id="fun"> Fun</label>

        <!-- Amenities (Checkboxes) -->
        <label><input type="checkbox" name="eventPlanner"> Event Planner</label>
        <label><input type="checkbox" name="equipRentals"> Equipment Rentals</label>
        <label><input type="checkbox" name="decoServices"> Decoration Services</label>
        <label><input type="checkbox" name="onsiteStaff"> On-site Staff</label>
        <label><input type="checkbox" name="techSupport"> Tech Support</label>
        <label><input type="checkbox" name="pwdFriendly"> PWD Friendly</label>
        <label><input type="checkbox" name="parking"> Parking</label>
        <label><input type="checkbox" name="cateringServices"> Catering Services</label>
        <label><input type="checkbox" name="securityStaff"> Security Staff</label>
        <label><input type="checkbox" name="wifiAccess"> Wi-Fi Access</label>

        <!-- Payment Options (Checkboxes) -->
        <label><input type="checkbox" name="payCash"> Cash</label>
        <label><input type="checkbox" name="payElectronic"> Electronic Payment</label>
        <label><input type="checkbox" name="payBank"> Bank Transfer</label>

        <!-- Image URLs -->
        <label for="imgs">Main Image URL:</label>
        <input type="url" name="imgs" id="imgs">

        <label for="img2">Second Image URL:</label>
        <input type="url" name="img2" id="img2">

        <label for="img3">Third Image URL:</label>
        <input type="url" name="img3" id="img3">

        <label for="img4">Fourth Image URL:</label>
        <input type="url" name="img4" id="img4">

        <label for="img5">Fifth Image URL:</label>
        <input type="url" name="img5" id="img5">

        <!-- Submit Button -->
        <button type="submit">Add Venue</button>
    </form>
</div>

</body>
</html>
<script>
document.querySelector("form").addEventListener("submit", function (e) {
    const checkboxes = ["intimate", "casual", "fun", "business"];
    const oneChecked = checkboxes.some(id => document.getElementById(id)?.checked);
    if (!oneChecked) {
        e.preventDefault();
        alert("Please select at least one category: Intimate, Casual, Fun, or Business.");
    }
});
</script>

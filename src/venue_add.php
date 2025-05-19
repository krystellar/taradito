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

$facilitySql = "SELECT * FROM facility";
$resultFacility = $conn->query($facilitySql);

if ($resultFacility->num_rows > 0) {
    $facilityOptions = $resultFacility->fetch_all(MYSQLI_ASSOC);
} else {
    $facilityOptions = [];
}


$availabilitySql = "SELECT * FROM availability";
$resultAvailability = $conn->query($availabilitySql);

if ($resultAvailability->num_rows > 0) {
    $availabilityDaysOptions = $resultAvailability->fetch_all(MYSQLI_ASSOC);
} else {
    $availabilityDaysOptions = [];
}

    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true); // create directory if it doesn't exist
    }

    // Function to handle single image upload
    function uploadImage($fileInputName, $targetDir) {
        if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES[$fileInputName]['tmp_name'];
            $fileName = basename($_FILES[$fileInputName]['name']);
            $fileName = uniqid() . "_" . $fileName;
            $filePath = $targetDir . $fileName;
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileType = mime_content_type($fileTmpPath);

            if (in_array($fileType, $allowedTypes)) {
                if (move_uploaded_file($fileTmpPath, $filePath)) {
                    return $filePath;
                }
            }
        }
        return null;
    }


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $managerID = $_SESSION['managerID']; 
    $venueName = mysqli_real_escape_string($conn, $_POST['venueName']);
    $barangayAddress = mysqli_real_escape_string($conn, $_POST['barangayAddress']);
    $cityAddress = mysqli_real_escape_string($conn, $_POST['cityAddress']);
    $venueDesc = mysqli_real_escape_string($conn, $_POST['venueDesc']);
    $availabilityDays = mysqli_real_escape_string($conn, $_POST['availabilityDays']);
    $amAvail = isset($_POST['amAvail'])  ? 1 : 0;
    $nnAvail  = isset($_POST['nnAvail'])  ? 1 : 0;
    $pmAvail = isset($_POST['pmAvail'])    ? 1 : 0;
    $publicTranspo = mysqli_real_escape_string($conn, $_POST['publicTranspo']);
    $landmarks = mysqli_real_escape_string($conn, $_POST['landmarks']);
    $routes = mysqli_real_escape_string($conn, $_POST['routes']);
    $priceRangeID = mysqli_real_escape_string($conn, $_POST['priceRange']); 
    $contactEmail = mysqli_real_escape_string($conn, $_POST['contactEmail']);
    $contactNum = mysqli_real_escape_string($conn, $_POST['contactNum']);
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

    $latitude =  mysqli_real_escape_string($conn, $_POST['latitude']);
    $longitude = mysqli_real_escape_string($conn, $_POST['longitude']);

    $imgs = uploadImage('imgs', $targetDir);
    $img2 = uploadImage('img2', $targetDir);
    $img3 = uploadImage('img3', $targetDir);

    // Insert into venueData table
    $sql = "INSERT INTO venuerequests (
        managerID, venueName, barangayAddress, cityAddress, venueDesc, availabilityDays,
        amAvail, nnAvail, pmAvail, publicTranspo,
        landmarks, routes, priceRangeID, contactEmail, contactNum,
        intimate, business, casual, fun,
        eventPlanner, equipRentals, decoServices, onsiteStaff, techSupport, pwdFriendly, parking,
        cateringServices, securityStaff, wifiAccess,
        payCash, payElectronic, payBank,
        imgs, img2, img3,
        latitude, longitude,
        status
    ) VALUES (
        $managerID, '$venueName', '$barangayAddress', '$cityAddress', '$venueDesc', '$availabilityDays',
        $amAvail, $nnAvail, $pmAvail, '$publicTranspo',
        '$landmarks', '$routes', '$priceRangeID', '$contactEmail', '$contactNum',
        $intimate, $business, $casual, $fun,
        $eventPlanner, $equipRentals, $decoServices, $onsiteStaff, $techSupport, $pwdFriendly, $parking,
        $cateringServices, $securityStaff, $wifiAccess,
        $payCash, $payElectronic, $payBank,
        '$imgs', '$img2', '$img3',
        '$latitude', '$longitude',
        'pending'
    )";


    if ($conn->query($sql)) { 
        $requestID = $conn->insert_id;
        $managerRequestSql = "INSERT INTO managervenuerequests (requestID, managerID) VALUES ($requestID, $managerID)";
        
        if ($conn->query($managerRequestSql)) {
            
            if (isset($_POST['facilityType'], $_POST['maxCapacity'], $_POST['price'])) {
                $facilityTypes = $_POST['facilityType'];
                $maxCapacities = $_POST['maxCapacity'];
                $facilityPrices = $_POST['price'];

                for ($i = 0; $i < count($facilityTypes); $i++) {
                    $facilityType = intval($facilityTypes[$i]);
                    $maxCap = intval($maxCapacities[$i]);
                    $fprice = floatval($facilityPrices[$i]);

                    // Insert into venueFacilities (auto-increment ID, so no need to include)
                    $insertFacilitySql = "INSERT INTO requestfacilities (requestID, facilityType, maxCapacity, price) VALUES (
                        $requestID,
                        $facilityType,
                        $maxCap,
                        $fprice
                    )";

                    $conn->query($insertFacilitySql);
        
                }
            }

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Venue</title>
    <style>
   /* Brutalist form styling */
body {
    font-family: "Arial", sans-serif;
    background-color: #fff;
    color: #000;
    margin: 0;
    padding: 0;
}

.container {
    width: 80%;
    max-width: 900px;
    margin: 50px auto;
    padding: 2rem;
    background-color: #fff;
    border: 4px solid #000;
    box-shadow: 10px 10px 0 #000;
}

h1 {
    font-weight: 900;
    font-size: 2rem;
    text-transform: uppercase;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid #000;
    padding-bottom: 1rem;
}

label {
    font-weight: 700;
    font-size: 1rem;
    margin-top: 1rem;
    display: block;
}

input, textarea, select {
    width: 100%;
    padding: 0.75rem;
    margin-top: 0.5rem;
    font-size: 1rem;
    border: 3px solid #000;
    background-color: #fff;
    color: #000;
    transition: all 0.2s ease;
    box-sizing: border-box;
}

input[type="checkbox"], input[type="radio"] {
    width: auto;
    margin-right: 1rem;
}

input, select, textarea:focus {
    outline: none;
    border-color: #000;
    box-shadow: 3px 3px 0 #000;
}

.checkbox-group {
    display: flex;
    flex-direction: column;
    margin-top: 1rem;
}

.checkbox-title {
    font-weight: 700;
    font-size: 1.1rem;
    margin-bottom: 1rem;
}

.checkbox-group label {
    display: inline-block;
    margin-right: 2rem;
    margin-top: 0.5rem;
    font-weight: normal;
    font-size: 1rem;
}

button {
    width: 100%;
    padding: 1rem;
    font-size: 1rem;
    font-weight: 700;
    text-transform: uppercase;
    background-color: #fff;
    color: #000;
    border: 3px solid #000;
    box-shadow: 5px 5px 0 #000;
    margin-top: 2rem;
    cursor: pointer;
    transition: all 0.2s ease;
    box-sizing: border-box;
}

button:hover {
    background-color: #000;
    color: #fff;
    transform: translate(-2px, -2px);
    box-shadow: 7px 7px 0 #000;
}

button:active {
    transform: translate(5px, 5px);
    box-shadow: none;
}

   
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


    </style>
</head>
<body>

<!-- Navbar -->
<header id="navbar">
  <nav class="navbar-container">
    
    <!-- Logo on the left -->
    <a href="#" class="logo">
      <img src="Images/Logo/TaraDito.png" alt="TaraDito Logo" />
    </a>

    <!-- Navigation Links on the right -->
    <ul class="nav-links">
      <li><a href="DashboardAdmin.php" class="nav-link">Go Back</a></li>
    </ul>

  </nav>
</header>

    <div class="container">
        <h1>Add New Venue</h1>
        <form action="venue_add.php" method="POST"  enctype="multipart/form-data">
            <!-- Venue Info -->
            <label for="venueName">Venue Name:</label>
            <input type="text" name="venueName" id="venueName" required>

            <label for="barangayAddress">Barangay Address:</label>
            <input type="text" name="barangayAddress" id="barangayAddress" required>

            <label for="cityAddress">City Address:</label>
            <input type="text" name="cityAddress" id="cityAddress" required>

            <label for="venueDesc">Venue Description:</label>
            <textarea name="venueDesc" id="venueDesc" rows="4"></textarea>

            <label for="availabilityDays">Availability Days:</label>
            <select name="availabilityDays" id="availabilityDays" required>
                <option value="">Select Day</option>
                <?php foreach ($availabilityDaysOptions as $day): ?>
                    <option value="<?= $day['availabilityDays'] ?>"><?= htmlspecialchars($day['days']) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Availability Times:</label><br>

            <input type="checkbox" id="amAvail" name="amAvail" value="1">
            <label for="amAvail">AM</label><br>

            <input type="checkbox" id="nnAvail" name="nnAvail" value="1">
            <label for="nnAvail">NN</label><br>

            <input type="checkbox" id="pmAvail" name="pmAvail" value="1">
            <label for="pmAvail">PM</label><br>

            <label for="contactEmail">Email:</label>
            <input type="text" name="contactEmail" id="contactEmail">

            <label for="contactNum">Contact Number:</label>
            <input type="number" name="contactNum" id="contactNum">

            <label for="publicTranspo">Public Transportation:</label>
            <input type="text" name="publicTranspo" id="publicTranspo">

            <label for="landmarks">Landmarks:</label>
            <input type="text" name="landmarks" id="landmarks">

            <label for="routes">Routes:</label>
            <input type="text" name="routes" id="routes">

            <!-- Categories (Checkboxes) -->
            <div class="checkbox-group">
                <p class="checkbox-title">Select your Event Categories:</p>
                <label><input type="checkbox" name="intimate" id="intimate"> Intimate</label>
                <label><input type="checkbox" name="business" id="business"> Business</label>
                <label><input type="checkbox" name="casual" id="casual"> Casual</label>
                <label><input type="checkbox" name="fun" id="fun"> Fun</label>
            </div>

            <!-- Amenities (Checkboxes) -->
            <div class="checkbox-group">
                <p class="checkbox-title">Select Available Amenities:</p>
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
            </div>

            <!-- Payment Options (Checkboxes) -->
            <div class="checkbox-group">
                <p class="checkbox-title">Select Available Payment Options:</p>
                <label><input type="checkbox" name="payCash"> Cash</label>
                <label><input type="checkbox" name="payElectronic"> Electronic Payment</label>
                <label><input type="checkbox" name="payBank"> Bank Transfer</label>
            </div>

            <!--coords-->
            <h2> Coordinates </h2>
            <label for="latitude">Latitude:</label>
            <input type="text" name="latitude" id="latitude">

            <label for="longitude">Longitude:</label>
            <input type="text" name="longitude" id="longitude">
            <!-- Image URLs -->
            <div class="section">
                <h2 class="text-xl font-semibold">Images</h2>

                <label for="imgs">Image 1:</label>
                <input type="file" name="imgs" id="imgs" accept="image/*" required>

                <label for="img2">Image 2:</label>
                <input type="file" name="img2" id="img2" accept="image/*">

                <label for="img3">Image 3:</label>
                <input type="file" name="img3" id="img3" accept="image/*">
            </div>
            <div class="facilityRepeater container" >
                <h2>Add Facilities:</h2>
                <button type="button" onclick="addFacility()">Add Another Facility</button>
                <div class="facility-group checkbox-group">
                    
                    <label for="facilityType">Facility Type:</label>
                    <select name="facilityType[]" required>
                    <option value="">Select Facility Type</option>
                    <?php foreach ($facilityOptions as $facility): ?>
                        <option value="<?= $facility['facilityType']; ?>"><?= htmlspecialchars($facility['facilityName']); ?></option>
                    <?php endforeach; ?>
                    </select>

                    <label for="maxCapacity">Max Capacity:</label>
                    <input type="number" name="maxCapacity[]" min="0" required>

                    <label for="price">Price:</label>
                    <input type="number" name="price[]" min="0" step="0.01" required>
                    <button type="button" class="removeFacility">Remove</button>
                </div>
            </div>
            <label for="priceRange" class="form-label">Price Range:</label>
            <select name="priceRange" id="priceRange" class="form-select" required>
                <option value="">Select Price Range</option>
                <?php foreach ($priceRanges as $range): ?>
                    <option value="<?= $range['priceRangeID']; ?>">
                        <?= $range['priceRangeText']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <!-- Submit Button -->
            <button type="submit">Add Venue</button>
        </form>
    </div>
    
</body>
</html>

<template id="facilityOptionsTemplate">
  <option value="">Select Facility Type</option>
  <?php foreach ($facilityOptions as $facility): ?>
    <option value="<?= $facility['facilityType']; ?>">
      <?= htmlspecialchars($facility['facilityName']); ?>
    </option>
  <?php endforeach; ?>
</template>


<script>
document.querySelector("form").addEventListener("submit", function (e) {
    const checkboxes = ["intimate", "casual", "fun", "business"];
    const oneChecked = checkboxes.some(id => document.getElementById(id)?.checked);
    if (!oneChecked) {
        e.preventDefault();
        alert("Please select at least one category: Intimate, Casual, Fun, or Business.");
    }
})
function addFacility() {
  const container = document.querySelector('.facilityRepeater');
  const templateOptions = document.getElementById('facilityOptionsTemplate').innerHTML;

  const newGroup = document.createElement('div');
  newGroup.className = 'facility-group';

  newGroup.innerHTML = `
    <label for="facilityType">Facility Type:</label>
    <select name="facilityType[]" required>${templateOptions}</select>

    <label for="maxCapacity">Max Capacity:</label>
    <input type="number" name="maxCapacity[]" min="0" required>

    <label for="price">Price:</label>
    <input type="number" name="price[]" min="0" step="0.01" required>

    <button type="button" class="removeFacility">Remove</button>
    <hr>
  `;

  container.appendChild(newGroup);

  newGroup.querySelector('.removeFacility').addEventListener('click', () => {
    container.removeChild(newGroup);
  });
}


document.querySelectorAll('.removeFacility').forEach(btn => {
  btn.addEventListener('click', function () {
    this.closest('.facility-group').remove();
  });
});

</script>

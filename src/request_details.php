<?php
session_start();
include('db_connection.php');

$requestID = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT 
    vr.*,
    mvr.managerID, m.firstName AS managerFirstName, m.lastName AS managerLastName,
    a.availabilityDays, a.days,
    r.priceRangeID, r.priceRangeText
FROM venuerequests vr
LEFT JOIN managervenuerequests mvr ON vr.requestID = mvr.requestID
LEFT JOIN managerData m ON mvr.managerID = m.managerID
LEFT JOIN availability a ON vr.availabilityDays = a.availabilityDays
LEFT JOIN priceRange r ON vr.priceRangeID = r.priceRangeID
WHERE vr.requestID = ?
";

$requestStmt = $conn->prepare($sql);
$requestStmt->bind_param("i", $requestID);
$requestStmt->execute();
$requestResult = $requestStmt->get_result();

if ($requestResult->num_rows === 0) {
    echo "<p class='text-center text-red-600 mt-10'>Request not found.</p>";
    exit;
}

$requestData = $requestResult->fetch_assoc();

// Then fetch facilities related to this request
$facilitySQL = "SELECT f.facilityName, rf.*
                FROM requestFacilities rf
                INNER JOIN facility f ON rf.facilityType = f.facilityType
                WHERE rf.requestID = ?";

$facilityStmt = $conn->prepare($facilitySQL);
$facilityStmt->bind_param("i", $requestID);
$facilityStmt->execute();
$facilityResult = $facilityStmt->get_result();
$facilities = $facilityResult->fetch_all(MYSQLI_ASSOC);


$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit <?= htmlspecialchars($requestData['venueName']) ?> | Design Stays</title>
    <link href="output.css" rel="stylesheet">
    <style>
       /* Add some custom styles for modern design */
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

input,
textarea,
select {
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

input[type="checkbox"],
input[type="radio"] {
    width: auto;
    margin-right: 1rem;
}

input:focus,
select:focus,
textarea:focus {
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

.section {
    margin-top: 2rem;
    border: 4px solid #000;
    background-color: #fff;
    padding: 2rem;
    box-shadow: 10px 10px 0 #000;
    font-family: "Arial", sans-serif;
    margin-bottom: 2rem;
}

   .section {
        background-color: white;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-top: 1.5rem;
    }

    .section h2 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #333333;
        margin-bottom: 1rem;
    }

    .category-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

   
    .category-label {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: #333333;
        position: relative;
        overflow: hidden;
        transition: 0.3s;
    }

    .category-label input[type="checkbox"] {
        height: 1.25rem;
        width: 1.25rem;
        border: 2px solid transparent;
        transition: all 0.3s;
    }


    .category-container:hover .category-hover-bg {
        opacity: 0.3;
    }

    .amenities-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    @media (min-width: 640px) {
        .amenities-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .amenities-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    .amenity-label {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: #333333;
    }

    .amenity-label input[type="checkbox"] {
        height: 1.25rem;
        width: 1.25rem;
        border: 1px solid #ccc;
        border-radius: 0.25rem;
    }

    .amenity-label span {
        font-size: 0.875rem;
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
      <li><a href="superAdmin.php" class="nav-link">Go Back</a></li>
    </ul>

  </nav>
</header>

<!-- Main Content -->
<div class="container">
<div class="section">
    <h2>Venue Details</h2>
    <p><strong>Venue Name:</strong> <?= htmlspecialchars($requestData['venueName']) ?></p>
    <p><strong>Barangay Address:</strong> <?= htmlspecialchars($requestData['barangayAddress']) ?></p>
    <p><strong>City Address:</strong> <?= htmlspecialchars($requestData['cityAddress']) ?></p>
    <p><strong>Contact Email:</strong> <?= htmlspecialchars($requestData['contactEmail']) ?></p>
    <p><strong>Contact Number:</strong> <?= htmlspecialchars($requestData['contactNum']) ?></p>
    <p><strong>Nearby Landmark:</strong> <?= htmlspecialchars($requestData['landmarks']) ?></p>
    <p><strong>Route Description:</strong> <?= htmlspecialchars($requestData['routes']) ?></p>
</div>

<!-- Venue Description -->
<div class="section">
    <h2>Description</h2>
    <p><?= nl2br(htmlspecialchars($requestData['venueDesc'])) ?></p>
</div>

<!-- Uploaded Images -->
<div class="section">
    <h2>Uploaded Images</h2>
    <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
        <?php
        $imageFields = ['imgs', 'img2', 'img3'];
        $hasImage = false;

        foreach ($imageFields as $field) {
            if (!empty($requestData[$field])) {
                $hasImage = true;
                echo '<img src="' . htmlspecialchars($requestData[$field]) . '" alt="Venue Image" style="width: 200px; height: 150px; object-fit: cover; border: 3px solid #000; box-shadow: 5px 5px 0 #000;">';
            }
        }

        if (!$hasImage) {
            echo "<p>No images uploaded.</p>";
        }
        ?>
    </div>
</div>



<!-- Price Range -->
<div class="section">
    <h2>Price Range</h2>
    <p><?= htmlspecialchars($requestData['priceRangeText']) ?></p>
</div>

<!-- Availability Days -->
<div class="section">
    <h2>Availability Days</h2>
    <p><?= htmlspecialchars($requestData['days']) ?></p>
</div>

<!-- Available Times -->
<div class="section">
    <h2>Available Times</h2>
    <p>
        <?php
        $availableTimes = [];
        if (!empty($requestData['amAvail'])) $availableTimes[] = "Morning";
        if (!empty($requestData['nnAvail'])) $availableTimes[] = "Afternoon";
        if (!empty($requestData['pmAvail'])) $availableTimes[] = "Night";

        echo !empty($availableTimes) ? implode(", ", $availableTimes) : "None";
        ?>
    </p>
</div>

<!-- Categories -->
<div class="section">
    <h2>Categories</h2>
    <p>
        <?php
        $availableCategories = [];
        if (!empty($requestData['intimate'])) $availableCategories[] = "Intimate";
        if (!empty($requestData['business'])) $availableCategories[] = "Business";
        if (!empty($requestData['casual'])) $availableCategories[] = "Casual";
        if (!empty($requestData['fun'])) $availableCategories[] = "Fun";

        echo !empty($availableCategories) ? implode(", ", $availableCategories) : "None";
        ?>
    </p>
</div>

<!-- Facilities Section -->
<div class="section">
    <h2>Facilities</h2>
    <?php if (!empty($facilities)): ?>
        <table style="width:100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="border: 2px solid #000; padding: 0.5rem;">Facility Name</th>
                    <th style="border: 2px solid #000; padding: 0.5rem;">Max Capacity</th>
                    <th style="border: 2px solid #000; padding: 0.5rem;">Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($facilities as $facility): ?>
                <tr>
                    <td style="border: 2px solid #000; padding: 0.5rem;"><?= htmlspecialchars($facility['facilityName']) ?></td>
                    <td style="border: 2px solid #000; padding: 0.5rem;"><?= htmlspecialchars($facility['maxCapacity']) ?></td>
                    <td style="border: 2px solid #000; padding: 0.5rem;">₱<?= htmlspecialchars($facility['price']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No facilities listed.</p>
    <?php endif; ?>
</div>


            
</body>
</html>



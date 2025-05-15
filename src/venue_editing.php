<?php
session_start();
include('db_connection.php');
if (!isset($_SESSION['managerID'])) {
    header("Location: Login.php");
    exit;
}

$venueID = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sql = "SELECT v.*,p.priceRangeText,
      m.firstName AS managerFirstName,
      m.lastName AS managerLastName
    FROM venueData v
    LEFT JOIN priceRange p ON v.priceRangeID = p.priceRangeID
    LEFT JOIN managerVenue mv ON v.venueID = mv.venueID
    LEFT JOIN managerData m ON mv.managerID = m.managerID
    WHERE v.venueID = ?";

$priceRangeSql = "SELECT * FROM priceRange";
$resultPriceRange = $conn->query($priceRangeSql);

if ($resultPriceRange->num_rows > 0) {
    // Price ranges found
    $priceRanges = $resultPriceRange->fetch_all(MYSQLI_ASSOC);
} else {
    $priceRanges = [];
}

$stmt  = $conn->execute_query($sql, [$venueID]);
$venue  = $stmt->fetch_assoc();
$conn->close();

if (!$venue) {
    echo "<p class='text-center text-red-600 mt-10'>Venue not found.</p>";
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit <?= htmlspecialchars($venue['venueName']) ?> | Design Stays</title>
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
      <li><a href="DashboardAdmin.php" class="nav-link">Go Back</a></li>
    </ul>

  </nav>
</header>

<!-- Main Content -->
<div class="container">
    <div class="card">
        <h1 class="card-header">Edit Venue: <?= htmlspecialchars($venue['venueName']) ?></h1>

        <form action="update_venue.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="venueID" value="<?= htmlspecialchars($venue['venueID']) ?>">

            <!-- Venue Info -->
            <div class="section">
                <div class="grid-container">
                    <div>
                        <label for="venueName" class="form-label">Venue Name</label>
                        <input type="text" name="venueName" id="venueName" value="<?= htmlspecialchars($venue['venueName']) ?>" class="form-input" required>
                    </div>

                    <div>
                        <label for="barangayAddress" class="form-label">Barangay Address</label>
                        <input type="text" name="barangayAddress" id="barangayAddress" value="<?= htmlspecialchars($venue['barangayAddress']) ?>" class="form-input" required>
                    </div>

                    <div>
                        <label for="cityAddress" class="form-label">City Address</label>
                        <input type="text" name="cityAddress" id="cityAddress" value="<?= htmlspecialchars($venue['cityAddress']) ?>" class="form-input" required>
                    </div>

                    <div>
                        <label for="landmarks" class="form-label">Nearby Landmark</label>
                        <input type="text" name="landmarks" id="landmarks" value="<?= htmlspecialchars($venue['landmarks']) ?>" class="form-input">
                    </div>

                    <div>
                        <label for="routes" class="form-label">Route Description</label>
                        <input type="text" name="routes" id="routes" value="<?= htmlspecialchars($venue['routes']) ?>" class="form-input">
                    </div>
                </div>
            </div>


            <!-- Venue Description -->
            <div class="section">
                <label for="venueDesc" class="form-label">Venue Description</label>
                <textarea name="venueDesc" id="venueDesc" rows="4" class="form-textarea"><?= htmlspecialchars($venue['venueDesc']) ?></textarea>
            </div>

            <label for="priceRange">Price Range:</label>
                <select name="priceRange" id="priceRange" required>
                    <option value="">Select Price Range</option>
                    <?php foreach ($priceRanges as $range): ?>
                        <option value="<?= $range['priceRangeID']; ?>"
                            <?php if (isset($venue['priceRangeID']) && $venue['priceRangeID'] == $range['priceRangeID']) echo 'selected'; ?>>
                            <?= $range['priceRangeText']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>


            <!-- Image Upload Section -->
            <div class="section">
                <h2 class="text-xl font-semibold text-[#333333]">Images</h2>
                <!-- Images -->
                <label>Image 1:</label><br>
                <?php if (!empty($venue['imgs'])): ?>
                    <img src="<?= $venue['imgs'] ?>" width="100"><br>
                <?php endif; ?>
                <input type="file" name="img1"><br>

                <label>Image 2:</label><br>
                <?php if (!empty($venue['img2'])): ?>
                    <img src="<?= $venue['img2'] ?>" width="100"><br>
                <?php endif; ?>
                <input type="file" name="img2"><br>

                <label>Image 3:</label><br>
                <?php if (!empty($venue['img3'])): ?>
                    <img src="<?= $venue['img3'] ?>" width="100"><br>
                <?php endif; ?>
                <input type="file" name="img3"><br>
            </div>


            
           
<!-- Category -->
<div class="section">
    <h2>Category</h2>
    <div class="category-grid">
        <?php 
        $categories = [
            'intimate' => ['label' => 'Intimate', 'color' => 'black'],
            'business' => ['label' => 'Business', 'color' => 'black'],
            'casual'   => ['label' => 'Casual', 'color' => 'black'],
            'fun'      => ['label' => 'Fun', 'color' => 'black']
        ];
        foreach ($categories as $col => $data):
            $isChecked = !empty($venue[$col]) ? 'checked' : '';
            $primaryColor = $data['color'];
            $checkboxId = "cat_$col";
        ?>
        <div class="category-container" style="border-color: <?= $primaryColor ?>;">
            <label for="<?= $checkboxId ?>" class="category-label">
                <input 
                    id="<?= $checkboxId ?>"
                    type="checkbox" 
                    name="<?= $col ?>" 
                    value="1" 
                    <?= $isChecked ?> 
                    style="accent-color: <?= $primaryColor ?>;"
                />
                <span style="color: <?= $primaryColor ?>"><?= htmlspecialchars($data['label']) ?></span>
            </label>
            <span class="category-hover-bg" style="background-color: <?= $primaryColor ?>;"></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>

           
<!-- Amenities -->
<div class="section">
    <h2>Amenities</h2>
    <div class="amenities-grid">
        <?php
        $amenities = [
            'eventPlanner' => 'Event Planner',
            'equipRentals' => 'Equipment Rentals',
            'decoServices' => 'Decoration Services',
            'onsiteStaff' => 'On-site Staff',
            'techSupport' => 'Tech Support',
            'pwdFriendly' => 'PWD Friendly',
            'parking' => 'Parking',
            'cateringServices' => 'Catering Services',
            'securityStaff' => 'Security Staff',
            'wifiAccess' => 'Wi-Fi Access'
        ];
        foreach ($amenities as $key => $label) {
            $checkboxId = "amenity_$key";
            $checked = !empty($venue[$key]) ? 'checked' : '';
            echo "
            <label for='$checkboxId' class='amenity-label'>
                <input id='$checkboxId' type='checkbox' name='$key' $checked>
                <span>$label</span>
            </label>";
        }
        ?>
    </div>
</div>

            <!-- Submit Button -->
            <button type="submit" class="form-button">Save Changes</button>
        </form>
    </div>
</div>

</body>
</html>

<?php
include('db_connection.php');

$venueID = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT v.*, p.priceRangeText, m.firstName, m.lastName
        FROM venueData v
        LEFT JOIN priceRange p ON v.priceRangeID = p.priceRangeID
        LEFT JOIN managerData m ON v.managerID = m.managerID
        WHERE v.venueID = ?";

$result = $conn->execute_query($sql, [$venueID]);
$venue = $result->fetch_assoc();
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
            font-family: 'Arial', sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
        }
        .card-header {
            font-size: 1.75rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-input, .form-textarea, .form-select {
            width: 100%;
            padding: 12px 16px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-top: 8px;
            margin-bottom: 16px;
            box-sizing: border-box;
            font-size: 1rem;
        }
        .form-input:focus, .form-textarea:focus, .form-select:focus {
            outline: none;
            border-color: #a0c4ff;
            box-shadow: 0 0 5px rgba(160, 196, 255, 0.7);
        }
        .form-button {
            background-color: #7BAAF7;
            color: white;
            padding: 12px 24px;
            font-size: 1.125rem;
            font-weight: 600;
            border-radius: 8px;
            width: 100%;
            cursor: pointer;
            transition: background-color 0.3s;
            border: none;
        }
        .form-button:hover {
            background-color: #5a90d0;
        }
        .section {
            margin-bottom: 30px;
        }
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        .checkbox-group label {
            display: flex;
            align-items: center;
            font-size: 1rem;
            color: #333;
        }
        .checkbox-group input {
            margin-right: 10px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<header id="navbar" class="w-full sticky top-0 z-50 bg-[#a0c4ff] shadow-sm">
  <nav class="max-w-[1320px] mx-auto flex items-center justify-between py-6 px-4 md:px-12">
    <a href="#" class="flex items-center gap-2 absolute left-10 pl-4">
      <img src="Images/Logo/LogoNav.png" alt="TaraDito Logo" class="h-[60px] w-auto" />
    </a>
    <ul class="flex gap-6 md:gap-8 flex-grow justify-center">
      <li><a href="#" class="text-lg font-medium text-white hover:bg-[#5CAC64] py-2 px-4 rounded-full transition-all duration-300">Home</a></li>
      <li><a href="product.php" class="text-lg font-medium text-white hover:bg-[#5CAC64] py-2 px-4 rounded-full transition-all duration-300">Venues</a></li>
      <li><a href="#" class="text-lg font-medium text-white hover:bg-[#5CAC64] py-2 px-4 rounded-full transition-all duration-300">Explore</a></li>
      <li><a href="#" class="text-lg font-medium text-white hover:bg-[#5CAC64] py-2 px-4 rounded-full transition-all duration-300">Contact</a></li>
    </ul>
  </nav>
</header>

<!-- Main Content -->
<div class="container">
    <div class="card">
        <h1 class="card-header">Edit Venue: <?= htmlspecialchars($venue['venueName']) ?></h1>

        <form action="update_venue.php" method="POST">
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
                </div>
            </div>

            <!-- Venue Description -->
            <div class="section">
                <label for="venueDesc" class="form-label">Venue Description</label>
                <textarea name="venueDesc" id="venueDesc" rows="4" class="form-textarea"><?= htmlspecialchars($venue['venueDesc']) ?></textarea>
            </div>

            <!-- Image URLs -->
            <div class="section">
                <h2 class="text-xl font-semibold text-[#333333]">Image URLs</h2>
                <div class="grid-container">
                    <?php
                    $imageFields = ['imgs' => 'Main', 'img2' => 'Second', 'img3' => 'Third', 'img4' => 'Fourth', 'img5' => 'Fifth'];
                    foreach ($imageFields as $name => $label) {
                        echo "
                        <div>
                            <label for='$name' class='form-label'>$label Image URL</label>
                            <input type='url' name='$name' id='$name' value='" . htmlspecialchars($venue[$name]) . "' class='form-input'>
                        </div>";
                    }
                    ?>
                </div>
            </div>

            <!-- Amenities -->
            <div class="section">
                <h2 class="text-xl font-semibold text-[#333333]">Amenities</h2>
                <div class="checkbox-group">
                    <?php
                    $amenities = [
                        'eventPlanner' => 'Event Planner',
                        'equipRentals' => 'Equipment Rentals',
                        'decoServices' => 'Decoration Services',
                        'onsiteStaff' => 'On-site Staff',
                        'techSupport' => 'Tech Support'
                    ];
                    foreach ($amenities as $key => $label) {
                        $checked = $venue[$key] ? 'checked' : '';
                        echo "
                        <label>
                            <input type='checkbox' name='$key' $checked class='h-5 w-5'>
                            $label
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

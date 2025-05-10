<?php
include('db_connection.php');

$venueID = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sql = "SELECT v.*,p.priceRangeText,
      m.firstName AS managerFirstName,
      m.lastName AS managerLastName
    FROM venueData v
    LEFT JOIN priceRange p ON v.priceRangeID = p.priceRangeID
    LEFT JOIN managerVenue mv ON v.venueID = mv.venueID
    LEFT JOIN managerData m ON mv.managerID = m.managerID
    WHERE v.venueID = ?";
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
    font-family: 'Arial', sans-serif;
    background-color: #f0f4f8; /* Slightly lighter background */
    margin: 0;
    padding: 0;
}

.container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 20px;
}

.card {
    background-color: #ffffff;
    border-radius: 12px; /* Slightly more rounded corners */
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    padding: 30px; /* Increased padding for better spacing */
    margin-top: 20px;
}

.card-header {
    font-size: 2rem; /* Increased font size */
    font-weight: 700; /* Bolder font weight */
    color: #2c3e50; /* Darker text color */
    margin-bottom: 30px; /* Increased margin for better separation */
    text-align: center;
}

.form-input, .form-textarea, .form-select {
    width: 100%;
    padding: 14px 18px; /* Increased padding for better touch targets */
    border-radius: 8px;
    border: 1px solid #ddd;
    margin-top: 8px;
    margin-bottom: 20px; /* Increased bottom margin */
    box-sizing: border-box;
    font-size: 1rem;
    transition: border-color 0.3s, box-shadow 0.3s; /* Smooth transition */
}

.form-input:focus, .form-textarea:focus, .form-select:focus {
    outline: none;
    border-color: #3498db; /* Changed focus border color */
    box-shadow: 0 0 5px rgba(52, 152, 219, 0.5); /* Changed focus shadow color */
}

.form-button {
    background-color: #3498db; /* Changed button color */
    color: white;
    padding: 14px 28px; /* Increased padding */
    font-size: 1.125rem;
    font-weight: 600;
    border-radius: 8px;
    width: 100%;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s; /* Added transform for hover effect */
    border: none;
}

.form-button:hover {
    background-color: #2980b9; /* Darker shade on hover */
    transform: translateY(-2px); /* Slight lift effect */
}

.section {
    margin-bottom: 40px; /* Increased margin for better separation */
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

h2 {
    font-size: 1.5rem; /* Increased font size for section headers */
    color: #2c3e50; /* Darker color for better contrast */
    margin-bottom: 10px; /* Added margin for spacing */
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
            
            <!-- Category -->
<div class="section bg-white p-6 rounded-2xl shadow-md mt-6">
    <h2 class="text-2xl font-semibold text-[#333333] mb-4">Category</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <?php 
        $categories = [
            'intimate' => ['label' => 'Intimate', 'color' => '#F28B82'],
            'business' => ['label' => 'Business', 'color' => '#7BAAF7'],
            'casual'   => ['label' => 'Casual', 'color' => '#5CAC64'],
            'fun'      => ['label' => 'Fun', 'color' => '#FFE066']
        ];
        foreach ($categories as $col => $data):
            $isChecked = !empty($venue[$col]) ? 'checked' : '';
            $primaryColor = $data['color'];
        ?>
        <div class="category-container p-4 rounded-lg border-2 transition duration-300 hover:scale-105 relative"
            style="border-color: <?= $primaryColor ?>; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <label class="flex items-center space-x-3 text-[#333] group relative overflow-hidden transition duration-300">
                <input 
                    type="checkbox" 
                    name="<?= $col ?>" 
                    value="1" 
                    <?= $isChecked ?> 
                    class="h-5 w-5 text-blue-600 rounded focus:ring-blue-500 border-2 border-transparent transition-all duration-300 group-hover:border-<?= $col ?>-500 group-hover:ring-2 group-hover:ring-<?= $col ?>-500"
                />
                <span class="text-base font-medium" style="color: <?= $primaryColor ?>"><?= htmlspecialchars($data['label']) ?></span>
            </label>
            <!-- Hover background, set to z-index -1 to keep it behind the checkbox -->
            <span class="absolute inset-0 opacity-0 group-hover:opacity-30 transition-all duration-300" 
                  style="background-color: <?= $primaryColor ?>; z-index: -1;"></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>




          <!-- Amenities -->
            <div class="section bg-white p-6 rounded-2xl shadow-md">
                <h2 class="text-2xl font-semibold text-[#333333] mb-4">Amenities</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
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
                        $checked = $venue[$key] ? 'checked' : '';
                        echo "
                        <label class='flex items-center space-x-3 text-[#333]'>
                            <input type='checkbox' name='$key' $checked class='h-5 w-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300'>
                            <span class='text-sm'>$label</span>
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

<?php
// Simulated session data for the manager
session_start();
$_SESSION['manager_id'] = 1;  // Assume the manager is logged in with ID 1
$_SESSION['manager_name'] = 'John Doe';
$_SESSION['manager_email'] = 'john.doe@example.com';

// Simulated venue data
$venues = [
    ['id' => 1, 'manager_id' => 1, 'venue_name' => 'Grand Hall', 'location' => 'Downtown', 'capacity' => 200, 'price' => 500.00],
    ['id' => 2, 'manager_id' => 1, 'venue_name' => 'Beachfront Paradise', 'location' => 'Coastline', 'capacity' => 150, 'price' => 300.00]
];

// Simulated reservation messages
$reservations = [
    ['id' => 1, 'venue_id' => 1, 'customer_name' => 'Alice', 'status' => 'pending'],
    ['id' => 2, 'venue_id' => 2, 'customer_name' => 'Bob', 'status' => 'pending']
];

// Handle venue update and message actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_manager'])) {
        $_SESSION['manager_name'] = $_POST['manager_name'];
        $_SESSION['manager_email'] = $_POST['manager_email'];
    }

    if (isset($_POST['update_venue'])) {
        $venueId = $_POST['venue_id'];
        $venueName = $_POST['venue_name'];
        $location = $_POST['location'];
        $capacity = $_POST['capacity'];
        $price = $_POST['price'];

        // Update the venue (in the array, simulating database update)
        foreach ($venues as &$venue) {
            if ($venue['id'] == $venueId) {
                $venue['venue_name'] = $venueName;
                $venue['location'] = $location;
                $venue['capacity'] = $capacity;
                $venue['price'] = $price;
                break;
            }
        }
    }

    if (isset($_POST['accept_reservation']) || isset($_POST['reject_reservation'])) {
        $reservationId = $_POST['reservation_id'];
        $status = isset($_POST['accept_reservation']) ? 'accepted' : 'rejected';

        // Update reservation status
        foreach ($reservations as &$reservation) {
            if ($reservation['id'] == $reservationId) {
                $reservation['status'] = $status;
                break;
            }
        }
    }
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

    <!-- Manager's Details Section -->
    <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
        <h2 class="text-xl font-semibold mb-4">Manager's Details</h2>
    
        <form method="POST" class="space-y-4">
            <div>
                <label for="manager_name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="manager_name" id="manager_name" value="<?php echo $_SESSION['manager_name']; ?>" class="w-full p-3 border rounded-lg" required>
            </div>
            <div>
                <label for="manager_email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="manager_email" id="manager_email" value="<?php echo $_SESSION['manager_email']; ?>" class="w-full p-3 border rounded-lg" required>
            </div>
            <button type="submit" name="update_manager" class="w-full bg-blue-500 text-white p-3 rounded-lg">Update Details</button>
        </form>
    </div>

    <!-- Venues Owned Section -->
    <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
        <h2 class="text-xl font-semibold mb-4">Venues Owned</h2>
        <table class="min-w-full table-auto">
            <thead>
                <tr>
                    <th class="px-4 py-2">Venue Name</th>
                    <th class="px-4 py-2">Location</th>
                    <th class="px-4 py-2">Capacity</th>
                    <th class="px-4 py-2">Price</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($venues as $venue): ?>
                    <tr>
                        <td class="px-4 py-2"><?php echo $venue['venue_name']; ?></td>
                        <td class="px-4 py-2"><?php echo $venue['location']; ?></td>
                        <td class="px-4 py-2"><?php echo $venue['capacity']; ?></td>
                        <td class="px-4 py-2">$<?php echo number_format($venue['price'], 2); ?></td>
                        <td class="px-4 py-2">
                            <form method="POST" class="inline">
                                <input type="hidden" name="venue_id" value="<?php echo $venue['id']; ?>">
                                <input type="text" name="venue_name" value="<?php echo $venue['venue_name']; ?>" class="p-2 border rounded-lg">
                                <input type="text" name="location" value="<?php echo $venue['location']; ?>" class="p-2 border rounded-lg">
                                <input type="number" name="capacity" value="<?php echo $venue['capacity']; ?>" class="p-2 border rounded-lg">
                                <input type="number" name="price" value="<?php echo $venue['price']; ?>" class="p-2 border rounded-lg">
                                <button type="submit" name="update_venue" class="bg-green-500 text-white p-2 rounded-lg">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Reservation Messages Section -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-xl font-semibold mb-4">Reservations</h2>
        <?php foreach ($reservations as $reservation): ?>
            <div class="flex justify-between items-center mb-4">
                <span><?php echo $reservation['customer_name']; ?> - Status: <?php echo $reservation['status']; ?></span>
                <div class="space-x-2">
                    <?php if ($reservation['status'] == 'pending'): ?>
                        <form method="POST" class="inline">
                            <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                            <button type="submit" name="accept_reservation" class="bg-green-500 text-white p-2 rounded-lg">Accept</button>
                            <button type="submit" name="reject_reservation" class="bg-red-500 text-white p-2 rounded-lg">Reject</button>
                        </form>
                    <?php else: ?>
                        <span class="text-gray-500">Already <?php echo $reservation['status']; ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>

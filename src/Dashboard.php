<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['userID'])) {
    header("Location: Login_user.php");
    exit;
}

$userID = $_SESSION['userID'];
$userQuery = $conn->execute_query("SELECT firstName, lastName, userEmail, joinDate FROM userData WHERE userID = ?", [$userID]);
$user = $userQuery->fetch_assoc();

$resQuery = $conn->execute_query(" SELECT 
    vd.venueName,
    vd.cityAddress,
    ur.startDate,
    ur.endDate,
    DATEDIFF(ur.endDate, ur.startDate) AS nights,
    pr.priceRangeText,
    rs.statusText
  FROM userreserved ur
  JOIN reservationStatus rs ON ur.statusID = rs.statusID
  JOIN venuedata vd ON ur.venueID = vd.venueID
  JOIN priceRange pr ON vd.priceRangeID = pr.priceRangeID
  WHERE ur.userID = ?
  ORDER BY ur.startDate DESC", [$userID]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Dashboard</title>
  <link href="output.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 text-gray-900">

  <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-6">Welcome, <?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?></h1>

    <!-- User Details Card -->
    <section class="bg-white shadow rounded-xl p-6 mb-10">
      <h2 class="text-xl font-semibold mb-4">Your Information</h2>
      <div class="grid sm:grid-cols-2 gap-4 text-sm">
        <div>
          <span class="font-medium text-gray-700">Full Name:</span>
          <p><?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?></p>
        </div>
        <div>
          <span class="font-medium text-gray-700">Email:</span>
          <p><?= htmlspecialchars($user['userEmail']) ?></p>
        </div>
        <div>
          <span class="font-medium text-gray-700">Member Since:</span>
          <p><?= date("F Y", strtotime($user['joinDate'])) ?></p>
        </div>
      </div>
    </section>

    <!-- Reservation History -->
    <section class="bg-white shadow rounded-xl p-6">
      <h2 class="text-xl font-semibold mb-4">Recent Reservations</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
          <thead class="bg-[#a0c4ff] text-white text-sm">
            <tr>
              <th class="px-4 py-2 text-left">Venue</th>
              <th class="px-4 py-2 text-left">Location</th>
              <th class="px-4 py-2 text-left">Date</th>
              <th class="px-4 py-2 text-left">Duration</th>
              <th class="px-4 py-2 text-left">Price Range</th>
              <th class="px-4 py-2 text-left">Status</th>
            </tr>
          </thead>
          <tbody class="text-sm divide-y divide-gray-200">
            <?php if ($resQuery->num_rows === 0): ?>
              <tr>
                <td colspan="6" class="px-4 py-3 text-center text-gray-500">No reservations found.</td>
              </tr>
            <?php else: ?>
              <?php while ($row = $resQuery->fetch_assoc()): ?>
              <tr>
                <td class="px-4 py-3 font-medium"><?= htmlspecialchars($row['venueName']) ?></td>
                <td class="px-4 py-3"><?= htmlspecialchars($row['cityAddress']) ?></td>
                <td class="px-4 py-3"><?= date("M j", strtotime($row['startDate'])) ?> – <?= date("j", strtotime($row['endDate'])) ?></td>
                <td class="px-4 py-3"><?= $row['nights'] ?> night<?= $row['nights'] > 1 ? 's' : '' ?></td>
                <td class="px-4 py-3"><?= htmlspecialchars($row['priceRangeText']) ?></td>
                <td class="px-4 py-3">
                  <span class="<?php
                    echo match ($row['statusText']) {
                      'Pending' => 'text-yellow-500',
                      'Not Accepted' => 'text-red-500',
                      'Confirmed' => 'text-blue-600',
                      'Deposited' => 'text-indigo-600',
                      'Happened' => 'text-green-600',
                      'Paid' => 'text-emerald-600',
                      default => 'text-gray-600'
                    };
                    ?> font-semibold"> <?= htmlspecialchars($row['statusText']) ?>
                  </span>

                </td>
              </tr>
              <?php endwhile; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>

</body>
</html>

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
    <h1 class="text-3xl font-bold mb-6">Welcome, Jane Doe</h1>

    <!-- User Details Card -->
    <section class="bg-white shadow rounded-xl p-6 mb-10">
      <h2 class="text-xl font-semibold mb-4">Your Information</h2>
      <div class="grid sm:grid-cols-2 gap-4 text-sm">
        <div>
          <span class="font-medium text-gray-700">Full Name:</span>
          <p>Jane Doe</p>
        </div>
        <div>
          <span class="font-medium text-gray-700">Email:</span>
          <p>jane.doe@email.com</p>
        </div>
        <div>
          <span class="font-medium text-gray-700">Phone:</span>
          <p>+63 912 345 6789</p>
        </div>
        <div>
          <span class="font-medium text-gray-700">Member Since:</span>
          <p>January 2024</p>
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
              <th class="px-4 py-2 text-left">Amount</th>
              <th class="px-4 py-2 text-left">Status</th>
            </tr>
          </thead>
          <tbody class="text-sm divide-y divide-gray-200">
            <tr>
              <td class="px-4 py-3 font-medium">Casa Balamban</td>
              <td class="px-4 py-3">Cebu, Philippines</td>
              <td class="px-4 py-3">Apr 20 – 25</td>
              <td class="px-4 py-3">5 nights</td>
              <td class="px-4 py-3">₱68,000</td>
              <td class="px-4 py-3"><span class="text-green-600 font-semibold">Confirmed</span></td>
            </tr>
            <tr>
              <td class="px-4 py-3 font-medium">Biombo Retreat</td>
              <td class="px-4 py-3">Bali, Indonesia</td>
              <td class="px-4 py-3">May 1 – 6</td>
              <td class="px-4 py-3">5 nights</td>
              <td class="px-4 py-3">₱64,613</td>
              <td class="px-4 py-3"><span class="text-yellow-600 font-semibold">Pending</span></td>
            </tr>
            <tr>
              <td class="px-4 py-3 font-medium">The Cliff House</td>
              <td class="px-4 py-3">Tagaytay, PH</td>
              <td class="px-4 py-3">Mar 12 – 14</td>
              <td class="px-4 py-3">2 nights</td>
              <td class="px-4 py-3">₱22,500</td>
              <td class="px-4 py-3"><span class="text-red-600 font-semibold">Cancelled</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>

</body>
</html>

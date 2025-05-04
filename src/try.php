<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PHP with Tailwind CSS</title>
  <link href="output.css" rel="stylesheet"> <!-- Adjust path as needed -->
</head>
<body class="bg-blue-100 min-h-screen flex flex-col items-center justify-center p-4">

  <div class="bg-green-200 shadow-md rounded-lg p-6 w-full max-w-md text-center">
    <h1 class="text-2xl font-bold text-green-600 mb-4">Welcome to My PHP Page</h1>

    <?php
      $user = "Kel";
      $time = date("h:i A");

      echo "<p class='text-black-200 text-lg mb-2'>Hello, <span class='font-semibold'>$user</span>!</p>";
      echo "<p class='text-sm text-green-500'>Current time: $time</p>";
    ?>

    <a href="?refresh=true" class="mt-6 inline-block bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded transition">
      Refresh Page
    </a>
  </div>

</body>
</html>

<?php
session_start();
include('db_connection.php');

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['signup'])) {
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $conn->execute_query("SELECT userEmail FROM userdata WHERE userEmail = ?", [$email]);
        if ($stmt->num_rows > 0) {
            $error = "Email is already registered.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $insertSql = "INSERT INTO userData (firstName, lastName, userEmail, userPass) VALUES (?, ?, ?, ?)";
            $result = $conn->execute_query($insertSql, [$firstName, $lastName, $email, $hashedPassword]);

            if ($result) {
                $success = "User added successfully!";
                header("Location: Login_user.php");
                exit;
            } else {
                $error = "An error occurred while adding the user.";
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
  <title>Sign Up</title>
  <link href="./output.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/tailwindcss@3.0.0/dist/tailwind.min.js"></script>
</head>
<body class="bg-gradient-to-r from-[#F28B82] via-[#7BAAF7] to-[#5CAC64] flex items-center justify-center min-h-screen">

  <div class="container bg-white p-8 rounded-2xl shadow-lg max-w-sm w-full">
    <div class="text-center mb-6">
      <h2 class="text-3xl font-extrabold text-[#1089D3]">Sign Up</h2>
    </div>
    
    <?php if (!empty($error)): ?>
      <div class="text-red-600 text-sm font-medium"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
      <div class="text-green-600 text-sm font-medium"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <form action="Signup_user.php" method="POST" class="space-y-4">
      <div class="space-y-2">
        <input required type="text" name="firstName" placeholder="First Name" class="w-full p-4 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#12B1D1] shadow-sm text-gray-700">
        <input required type="text" name="lastName" placeholder="Last Name" class="w-full p-4 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#12B1D1] shadow-sm text-gray-700">
        <input required type="email" name="email" placeholder="Email" class="w-full p-4 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#12B1D1] shadow-sm text-gray-700">
        <input required type="password" name="password" placeholder="Password" class="w-full p-4 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#12B1D1] shadow-sm text-gray-700">
        <input required type="password" name="confirm_password" placeholder="Confirm Password" class="w-full p-4 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#12B1D1] shadow-sm text-gray-700">
      </div>
      
      <input type="submit" name="signup" value="Sign Up" class="w-full p-4 mt-4 text-white font-semibold bg-gradient-to-r from-[#1089D3] to-[#12B1D1] rounded-xl cursor-pointer shadow-lg hover:scale-105 transition-all duration-200 transform">
    </form>
    
    <div class="text-center mt-4">
      <span class="text-sm text-gray-500">Already have an account? <a href="Login_user.php" class="text-blue-600">Sign In</a></span>
    </div>
  </div>
</body>
</html>

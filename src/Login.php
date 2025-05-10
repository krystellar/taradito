<?php
session_start();
include('db_connection.php');

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please fill in both fields.";
    } else {
        $stmt = $conn->execute_query(
            "SELECT managerID, managerPass FROM managerData WHERE managerEmail = ?",
            [$email]
        );
        $user = $stmt->fetch_assoc();

        if ($user && password_verify($password, $user['managerPass'])) {
            $_SESSION['managerID'] = $user['managerID'];
            header("Location: DashboardAdmin.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In</title>
  <link href="./output.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/tailwindcss@3.0.0/dist/tailwind.min.js"></script>
  
</head>
<body class="bg-gradient-to-r from-[#F28B82] via-[#7BAAF7] to-[#5CAC64] flex items-center justify-center min-h-screen">

  <div class="container bg-white p-8 rounded-2xl shadow-lg max-w-sm w-full">
    <div class="text-center mb-6">
      <h2 class="text-3xl font-extrabold text-[#1089D3]">Sign In</h2>
    </div>
    
    <form action="#" method="POST" class="space-y-4">
      <?php if (!empty($error)): ?>
        <div class="text-red-600 text-sm font-medium"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <div class="space-y-2">
        <input required type="email" name="email" id="email" placeholder="E-mail" 
          class="w-full p-4 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#12B1D1] shadow-sm transition duration-200 text-gray-700">
        <input required type="password" name="password" id="password" placeholder="Password" 
          class="w-full p-4 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#12B1D1] shadow-sm transition duration-200 text-gray-700">
      </div>
      
      <span class="text-xs text-right block mt-1 text-blue-600">
        <a href="#">Forgot Password?</a>
      </span>
      
      <input type="submit" value="Sign In" 
        class="w-full p-4 mt-4 text-white font-semibold bg-gradient-to-r from-[#1089D3] to-[#12B1D1] rounded-xl cursor-pointer shadow-lg hover:scale-105 transition-all duration-200 transform">
    </form>

    <div class="text-center mt-6">
      <span class="text-sm text-gray-500">Or Sign in with</span>
      <div class="flex justify-center gap-4 mt-2">
        <button class="bg-black text-white p-2 rounded-full shadow-lg hover:scale-110 transition-all duration-200"></button>
        <button class="bg-black text-white p-2 rounded-full shadow-lg hover:scale-110 transition-all duration-200"></button>
        <button class="bg-black text-white p-2 rounded-full shadow-lg hover:scale-110 transition-all duration-200"></button>
      </div>
    </div>
    <div class="text-center mt-4">
      <span class="text-sm text-gray-500">By signing in, you agree to our <a href="#" class="text-blue-600">user licence agreement</a>.</span>
    </div>

    <!-- sign up here -->
    <div class="text-center mt-4">
      <span class="text-sm text-gray-500">Don't have an account? <a href="Signup.php" class="text-blue-600">Sign Up</a></span>
    </div>

  </div>
</body>
</html>

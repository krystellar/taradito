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
 <style>
  * {
    box-sizing: border-box;
  }

  body {
  margin: 0;                
  min-height: 100vh;         
  display: flex;             
  align-items: center;       
  justify-content: center;   
  
  /* Background image */
  background-image: url('Images/BG.png');
  background-size: cover;    
  background-position: center; 
  background-repeat: no-repeat; 
}

  .card {
    width: 400px;
    padding: 20px;
    background: #fff;
    border: 6px solid #000;
    box-shadow: 12px 12px 0 #000;
    transition: transform 0.3s, box-shadow 0.3s;
  }

  .card:hover {
    transform: translate(-5px, -5px);
    box-shadow: 17px 17px 0 #000;
  }

  .card__title {
    font-size: 32px;
    font-weight: 900;
    color: #000;
    text-transform: uppercase;
    margin-bottom: 15px;
    display: block;
    position: relative;
    overflow: hidden;
    align: right;
  }

  .card__title::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;
    width: 90%;
    height: 3px;
    background-color: #000;
    transform: translateX(-100%);
    transition: transform 0.3s;
  }

  .card:hover .card__title::after {
    transform: translateX(0);
  }

  .card__content {
    font-size: 16px;
    line-height: 1.4;
    color: #000;
    margin-bottom: 20px;
  }

  .card__form {
    display: flex;
    flex-direction: column;
    gap: 15px;
    justify-content: center;
  }

  .card__form input {
    padding: 10px;
    border: 3px solid #000;
    font-size: 16px;
    font-family: inherit;
    transition: transform 0.3s;
    width: 100%
  }

  .card__form input:focus {
    outline: none;
    transform: scale(1.05);
    background-color: #000;
    color: #ffffff;
  }

  .card__button {
    border: 3px solid #000;
    background: #000;
    color: #fff;
    padding: 10px;
    font-size: 18px;
    font-weight: bold;
    text-transform: uppercase;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: transform 0.3s;
    width: 50PX;
    height: 100%;
    
  }

  .card__button::before {
    content: "Sure?";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 105%;
    background-color: #5ad641;
    color: #000;
    display: flex;
    align-items: center;
    justify-content: center;
    transform: translateY(100%);
    transition: transform 0.3s;
  }

  .card__button:hover::before {
    transform: translateY(1);
  }

  .card__button:active {
    transform: scale(0.95);
  }

.terms,
.signup {
  text-align: center;
  font-size: 0.9rem;
  color: #666;
  margin-top: 1.5rem;
  line-height: 1.6;
}

.terms a,
.signup a {
  color: #7BAAF7; /* Modern Sky Blue */
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s ease;
}

.terms a:hover,
.signup a:hover {
  color: #5A90D0; /* Slightly darker blue on hover */
  text-decoration: underline;
}


  @keyframes glitch {
    0% {
      transform: translate(2px, 2px);
    }
    25% {
      transform: translate(-2px, -2px);
    }
    50% {
      transform: translate(-2px, 2px);
    }
    75% {
      transform: translate(2px, -2px);
    }
    100% {
      transform: translate(2px, 2px);
    }
  }

  .glitch {
    animation: glitch 0.3s infinite;
  }
</style>
</head>
<body>


  <div class="card bg-white p-8 rounded-2xl shadow-lg max-w-sm w-full">
    <div class="text-center mb-6">
      <h2 class="card__title">Sign Up</h2>
    </div>
    
    <?php if (!empty($error)): ?>
      <div class="error-message text-red-600 text-sm font-medium"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
      <div class="success-message text-green-600 text-sm font-medium"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <form action="Signup.php" method="POST" class="card__form space-y-4">
      <div class="space-y-2">
        <input required type="text" name="firstName" placeholder="First Name" class="input-field ">
        <input required type="text" name="lastName" placeholder="Last Name" class="input-field ">
        <input required type="email" name="email" placeholder="Email" class="input-field ">
        <input required type="password" name="password" placeholder="Password" class="input-field ">
        <input required type="password" name="confirm_password" placeholder="Confirm Password" class="input-field ">
      </div>
      
      <input type="submit" name="signup" value="Make the Account" class="card__button">
    </form>
    
    <div class="text-center mt-4">
      <span class="text-sm text-gray-500">Already have an account? <a href="Login.php" class="text-blue-600">Sign In</a></span>
    </div>
  </div>

</body>
</html>

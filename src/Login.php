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
  <style>
   <style>
  * {
    box-sizing: border-box;
  }

  body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background: linear-gradient(to right, #F28B82, #7BAAF7, #5CAC64);
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
  }

  .card {
    width: 300px;
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
  }

  .card__form input {
    padding: 10px;
    border: 3px solid #000;
    font-size: 16px;
    font-family: inherit;
    transition: transform 0.3s;
    width: calc(100% - 26px);
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
    left: 20%;
    font-weight: bold;
    text-transform: uppercase;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: transform 0.3s;
    width: 50%;
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
    transform: translateY(0);
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

  <div class="card">
    <div class="text-center">
      <h2 class="card__title">Sign In</h2>
    </div>

    <form action="#" method="POST" class="card__form">
      <?php if (!empty($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <input required type="email" name="email" id="email" placeholder="E-mail">
      <input required type="password" name="password" id="password" placeholder="Password">

      <button type="submit" class="card__button">Sign In</button>
    </form>

  

    <div class="terms">
      By signing in, you agree to our <a href="#">user licence agreement</a>.
    </div>
    <div class="signup">
      Don't have an account? <a href="Signup.php">Sign Up</a>
    </div>
  </div>

</body>

</html>


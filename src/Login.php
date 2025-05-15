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
     position: relative;
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
    font-weight: bold;
    text-transform: uppercase;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: transform 0.3s;
    width: 50%;
    height: 100%;
    left: 25%;
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

  .word-background {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  overflow: hidden;
  pointer-events: none;
  z-index: -1;
}

.word-background span {
  position: absolute;
  white-space: nowrap;  /* Prevents wrapping */
  color: rgba(80, 80, 80, 0.2); /* A soft but visible gray */
  font-family: 'Montserrat', Arial, sans-serif; /* Stylish sans-serif font */
  font-weight: 700; /* Bold for better visibility */
  text-transform: uppercase; /* CAPSLOCK */
  letter-spacing: 2px; /* Spacing for elegant caps */
  z-index: -1; /* Keep it behind main content */
  pointer-events: none; /* Avoid blocking interactions */
}

@keyframes floatLeft {
  0% {
    transform: translateX(100vw);
  }
  100% {
    transform: translateX(-100vw);
  }
}

/* Style for the hero section */
.hero {
  position: relative;
  width: 100%;
  height: 100vh;
  overflow: hidden;
  background-color: #f1f1f1;

  display: flex;              /* Enables flex layout */
  justify-content: center;    /* Horizontally center */
  align-items: center;        /* Vertically center */
  text-align: center;         /* Optional: center-align text */
}


/* Style for the word-background container */
.word-background {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 0; /* Place behind other content */
}

/* Ensure words are not overflowing outside of the container */
.word-background span {
  position: absolute;
  white-space: nowrap;  /* Prevents words from breaking into multiple lines */
  color: rgba(0, 0, 0, 0.1);  /* Faint color for a background effect */
  font-family: Arial, sans-serif;
  z-index: 0; /* Ensure words are behind content */
}

</style>

</head>
<body>
<section class="hero">
  <div class="word-background">
<span>Hanap tayo ng magandang spot.</span>
<span>May alam ka bang maaliwalas na lugar?</span>
<span>Yung hindi matao sana.</span>
<span>Basta presentable tingnan.</span>
<span>Malapit-lapit lang sana.</span>
<span>Yung relaxing ang ambiance.</span>
<span>Pwede bang pasilip ng venue?</span>
<span>May aircon ba?</span>
<span>Pang-Instagram ba 'yan?</span>
<span>Yung parang spa ang vibes.</span>
<span>Hindi crowded, please.</span>
<span>Kailangan ko ng tahimik na lugar.</span>
<span>Saan ba maganda ngayon?</span>
<span>Meron ba sa tabi-tabi lang?</span>
<span>Gusto ko ng aesthetic.</span>
<span>Sana may natural lighting.</span>
<span>Pasok ba sa budget?</span>
<span>Yung hindi mainit.</span>
<span>Doon tayo sa cozy.</span>
<span>Saan malapit dito?</span>
<span>May parking ba?</span>
<span>Yung Instagrammable.</span>
<span>Pwede ba magpa-reserve?</span>
<span>Open space ba yan?</span>
<span>Tara, silipin natin.</span>
<span>Okay ba sa vibe mo?</span>
<span>Pwede bang maki-check?</span>
<span>Comfortable ba diyan?</span>
<span>Yung hindi hassle puntahan.</span>
<span>Saan ba ang secret spots?</span>
<span>Gusto ko ng fresh na feel.</span>
<span>Saan safe at secure?</span>
<span>Yung pang-pamper talaga.</span>
<span>May privacy ba?</span>
<span>Yung pwedeng tumambay.</span>
<span>May ambience ba?</span>
<span>Yung parang hidden gem.</span>
<span>Hindi masyadong public.</span>
<span>Chill lang sana.</span>
<span>Saan ang peaceful na lugar?</span>
<span>Yung pang-soft launch.</span>
<span>Ayoko ng masikip.</span>
<span>Mas okay kung minimalist.</span>
<span>Yung clean look.</span>
<span>Medyo artsy sana.</span>
<span>Sana may scent diffuser.</span>
<span>Basta maaliwalas.</span>
<span>Dapat presentable sa photos.</span>

</div>
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

      <button type="submit" class="card__button">Log In</button>
    </form>

  

    <div class="terms">
      By signing in, you agree to our <a href="#">user licence agreement</a>.
    </div>
    <div class="signup">
      Don't have an account? <a href="Signup.php">Sign Up</a>
    </div>
  </div>
</section>


<script>
  const phrases = document.querySelectorAll('.word-background span');
  const containerHeight = window.innerHeight;  // Assuming the container is the entire viewport height
  const containerWidth = window.innerWidth;    // Assuming the container is the entire viewport width
  const margin = 10; // Space between words (adjust as necessary)
  let usedPositions = []; // To track used vertical positions
  let maxAttempts = 50; // Maximum number of attempts to find a valid position for each word
  let phraseHeights = [];

  // First, let's calculate and store the height of each phrase to avoid recalculating it multiple times
  phrases.forEach(phrase => {
    phraseHeights.push(phrase.offsetHeight);
  });

  // Function to get a random vertical position without overlap
  function getRandomPosition(phrases, index) {
    const phraseHeight = phraseHeights[index];
    let topPosition;
    let attempts = 0;

    // Try to find a random position that does not overlap with previous words
    do {
      topPosition = Math.random() * (containerHeight - phraseHeight - margin); // Random position within container height
      attempts++;
      if (attempts > maxAttempts) {
        // Fallback: spread out the words across the height (avoid just clustering at the bottom)
        const evenSpacing = Math.floor(index * (containerHeight / phrases.length));
        topPosition = Math.min(evenSpacing, containerHeight - phraseHeight - margin);
        break;
      }
    } while (usedPositions.some(position => Math.abs(position - topPosition) < phraseHeight + margin));

    // Mark this position as used
    usedPositions.push(topPosition);
    return topPosition;
  }

  // Set properties for each word
  phrases.forEach((phrase, index) => {
    // Get a random top position for each word
    const top = getRandomPosition(phrases, index);
    
    // Random delay for animation
    const delay = Math.random() * 3; // Adjust the range to a more reasonable delay

    // Random duration for the animation (from 20 to 40 seconds)
    const duration = 20 + Math.random() * 10;

    // Random horizontal position (within container width)
    const left = Math.random() * (containerWidth - phrase.offsetWidth);

    // Apply styles dynamically
    phrase.style.position = 'absolute'; 
    phrase.style.top = `${top}px`;
    phrase.style.left = `${left}px`;  // Position words horizontally
    phrase.style.animationDuration = `${duration}s`;
    phrase.style.animationDelay = `${delay}s`;
    phrase.style.animationName = 'floatLeft';
phrase.style.animationTimingFunction = 'linear';
phrase.style.animationIterationCount = 'infinite';


    // Optional: Random font size for each word
    const randomSize = 12 + Math.random() * 18; // Font size range from 12px to 30px
    phrase.style.fontSize = `${randomSize}px`;

    // Ensure words do not overflow from the viewport (additional safety check)
    if (phrase.offsetTop + phrase.offsetHeight > containerHeight) {
      phrase.style.top = `${containerHeight - phrase.offsetHeight - margin}px`; // Place near the bottom if overflow
    }
  });
</script>

</body>

</html>


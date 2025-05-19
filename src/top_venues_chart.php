<?php
session_start();
  include('db_connection.php');

  define('PROJECT_ROOT', rtrim(dirname($_SERVER['SCRIPT_NAME'], 2), '/'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Venue Stats - Brutal Style</title>
  <style>
    body {
      background: #fff;
      display: flex;
      flex-direction: column;
      gap: 40px;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 40px;
    }

    .card {
      width: 800px;
      padding: 30px;
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

    /* Title */
    .card__title {
      font-size: 36px;
      font-weight: 900;
      color: #000;
      text-transform: uppercase;
      margin-bottom: 30px;
      position: relative;
      overflow: hidden;
      display: inline-block;
      user-select: none;
    }
    .card__title::after {
      content: "";
      position: absolute;
      bottom: 0;
      left: 0;
      width: 90%;
      height: 4px;
      background-color: #000;
      transform: translateX(-100%);
      transition: transform 0.3s;
    }
    .card:hover .card__title::after {
      transform: translateX(0);
    }

    /* Chart container */
    #chartContainer, #chartContainer2 {
      width: 100%;
      height: 450px;
      border: 6px solid #000;
      box-shadow: 10px 10px 0 #000;
      position: relative;
    }

    canvas {
      width: 100% !important;
      height: 100% !important;
      display: block;
    }

     /* Nav buttons container */
    #navButtons {
      margin-bottom: 20px;
      display: flex;
      justify-content: center;
      gap: 10px;
    }

    /* Style for buttons */
    #navButtons button {
      background-color: #000;
      color: #fff;
      border: none;
      padding: 10px 20px;
      font-weight: 900;
      cursor: pointer;
      text-transform: uppercase;
      border: 3px solid #000;
      transition: background-color 0.3s, color 0.3s;
    }
    #navButtons button.active,
    #navButtons button:hover {
      background-color: #fff;
      color: #000;
    }

    
/* Floating sticky navbar */
#navbar {
  position: sticky;
  top: 20px;
  z-index: 50;
  display: flex;
  justify-content: center;
  pointer-events: none; /* allows shadow and border effects to float above */
  margin-bottom: 2rem;
  max-width: 1280px;
  width: 100%;
}

.navbar-container {
  pointer-events: auto; 
  background-color: #fff;
  max-width: 1280px;
  width: 100%;
  margin: 0 auto;
  padding: 16px 24px;
  box-shadow: 10px 10px 0 #000;
  border: 4px solid #000;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

/* Logo */
.logo img {
  height: 50px;
  width: 50px;
}

/* Navigation links */
.nav-links {
  display: flex;
  gap: 24px;
  margin-left: auto;
  list-style: none;
  padding: 0;
}

@media (min-width: 768px) {
  .nav-links {
    gap: 32px;
  }
}

.nav-link {
  text-decoration: none;
  font-size: 1.125rem; /* ~18px */
  font-weight: 500;
  color: #000;
  padding: 8px 16px;
  text-transform: uppercase;
  padding-bottom: 1rem;
  margin-bottom: 1.5rem;
  border: 2px solid transparent; /* reserve space */
  transition: transform 0.2s ease, background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
  will-change: transform;
}

.nav-link:hover {
  color: #000;
  background-color: #fff;
  transform: translateY(-2px);
  border-color: #000; /* no layout shift */
  box-shadow: 4px 4px 0 #000;
}

.content {
  opacity: 0;
  animation: floatUp 1s ease forwards;
}

/* Float up keyframes */
@keyframes floatUp {
  0% {
    opacity: 0;
    transform: translateY(40px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

  </style>
</head>
<body class="content">

<header id="navbar">
  <nav class="navbar-container">
    
    <!-- Logo on the left -->
    <a href="#" class="logo">
      <img src="Images/Logo/TaraDito.png" alt="TaraDito Logo" />
    </a>

    <!-- Navigation Links on the right -->
    <ul class="nav-links">
    <li><a href="product.php" class="nav-link">Venues</a></li>
    <li><a href="top_venues_chart.php" class="nav-link">Top picks</a></li>
    <?php
        $dashboardLink = PROJECT_ROOT . '/src/index.php';
        $dashboardText = 'Home';
        if (isset($_SESSION['role'])) {
            if ($_SESSION['role'] === 'manager') {
                $dashboardLink = PROJECT_ROOT . '/src/dashboardAdmin.php';
                $dashboardText = 'Dashboard';
            } elseif ($_SESSION['role'] === 'user') {
                $dashboardLink = PROJECT_ROOT . '/src/dashboard.php';
                $dashboardText = 'Dashboard';
            } elseif ($_SESSION['role'] === 'admin') {
                $dashboardLink = PROJECT_ROOT . '/src/superAdmin.php';
                $dashboardText = 'Dashboard';
            }
        }
      ?>
    <li>
        <a href="<?= $dashboardLink ?>" class="nav-link"> <?= $dashboardText ?></a>
    </li>
</ul>


  </nav>
</header>

 <!-- Navigation Buttons -->
<div id="navButtons">
  <button data-target="reserved" class="active">Most Reserved</button>
  <button data-target="rated">Most Rated</button>
  <button data-target="liked">Most Liked</button>
</div>

<!-- Chart Containers -->
<div id="reserved" class="chart-container active">
  <div class="card">
    <span class="card__title">Top 5 Most Reserved Venues</span>
    <div id="chartContainer">
      <canvas id="venueChart"></canvas>
    </div>
  </div>
</div>

<div id="rated" class="chart-container" style="display:none;">
  <div class="card">
    <span class="card__title">Top 5 Most Rated Venues</span>
    <div id="chartContainer2">
      <canvas id="mostRatedVenueChart"></canvas>
    </div>
  </div>
</div>

<div id="liked" class="chart-container" style="display:none;">
  <div class="card">
    <span class="card__title">Top 5 Most Liked Venues</span>
    <div id="chartContainer3">
      <canvas id="likedVenueChart"></canvas>
    </div>
  </div>
</div>


  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    function truncateName(name) {
      const words = name.split(' ');
      if (words.length <= 2) {
        return name;
      } else {
        return words.slice(0, 2).join(' ') + '…';
      }
    }

    // First Chart: Most Reserved Venues
    fetch('get_top_venues.php')
      .then(response => response.json())
      .then(data => {
        const labels = data.map(item => truncateName(item.venueName));
        const values = data.map(item => item.totalReservations);

        new Chart(document.getElementById('venueChart'), {
          type: 'bar',
          data: {
            labels: labels,
            datasets: [{
              label: 'Total Reservations',
              data: values,
              backgroundColor: '#7BAAF7',
              borderColor: '#5A90D0',
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: { display: false },
              title: {
                display: true,
                font: { size: 24, weight: 'bold' },
                color: '#000'
              }
            },
            scales: {
              x: {
                ticks: {
                  color: '#000',
                  maxRotation: 0,
                  minRotation: 0,
                  font: { weight: '900', size: 16 }
                },
                grid: {
                  display: false,
                  drawBorder: true,
                  borderColor: '#000'
                }
              },
              y: {
                beginAtZero: true,
                ticks: {
                  color: '#000',
                  font: { weight: '900', size: 16 }
                },
                grid: {
                  color: '#000',
                  borderColor: '#000',
                  borderDash: [5, 5]
                },
                title: {
                  display: true,
                  text: 'Total Reservations',
                  color: '#000',
                  font: { weight: '900', size: 20 }
                }
              }
            }
          }
        });
      });

    // Second Chart: Most Rated Venues
    fetch('most_rated_venue.php')
      .then(res => res.json())
      .then(data => {
        const labels = data.map(item => truncateName(item.venueName));
        const values = data.map(item => item.avgRating);

        new Chart(document.getElementById('mostRatedVenueChart'), {
          type: 'bar',
          data: {
            labels: labels,
            datasets: [{
              label: 'Average Rating',
              data: values,
              backgroundColor: '#5ad641',
              borderColor: '#3a9c1e',
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: { display: false },
              title: {
                display: true,
                font: { size: 24, weight: 'bold' },
                color: '#000'
              }
            },
            scales: {
              x: {
                ticks: {
                  color: '#000',
                  maxRotation: 0,
                  minRotation: 0,
                  font: { weight: '900', size: 16 }
                },
                grid: {
                  display: false,
                  drawBorder: true,
                  borderColor: '#000'
                }
              },
              y: {
                beginAtZero: true,
                max: 5,
                ticks: {
                  color: '#000',
                  font: { weight: '900', size: 16 }
                },
                grid: {
                  color: '#eee',
                  borderColor: '#000',
                  borderDash: [5, 5]
                },
                title: {
                  display: true,
                  text: 'Average Rating',
                  color: '#000',
                  font: { weight: '900', size: 20 }
                }
              }
            }
          }
        });
      });
  </script>


  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Truncate venue name to max 2 words + ellipsis
    function truncateName(name) {
      const words = name.split(' ');
      if (words.length <= 2) return name;
      return words.slice(0, 2).join(' ') + '…';
    }

    fetch('most_liked_venue.php')
      .then(response => response.json())
      .then(data => {
        const labels = data.map(item => truncateName(item.venueName));
        const values = data.map(item => item.totalLikes);

        new Chart(document.getElementById('likedVenueChart'), {
          type: 'bar',
          data: {
            labels: labels,
            datasets: [{
              label: 'Total Likes',
              data: values,
              backgroundColor: '#ff6b6b',
              borderColor: '#d43f3f',
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: { display: false },
              title: {
                display: true,
                text: 'Top 5 Most Liked Venues',
                font: { size: 24, weight: 'bold' },
                color: '#000'
              }
            },
            scales: {
              x: {
                ticks: {
                  color: '#000',
                  maxRotation: 0,
                  minRotation: 0,
                  font: { weight: '900', size: 16 }
                },
                grid: { display: false, drawBorder: true, borderColor: '#000' }
              },
              y: {
                beginAtZero: true,
                ticks: {
                  color: '#000',
                  font: { weight: '900', size: 16 }
                },
                grid: {
                  color: '#eee',
                  borderColor: '#000',
                  borderDash: [5, 5]
                },
                title: {
                  display: true,
                  text: 'Total Likes',
                  color: '#000',
                  font: { weight: '900', size: 20 }
                }
              }
            }
          }
        });
      })
      .catch(error => console.error('Error loading data:', error));
  </script>

<script>
     const buttons = document.querySelectorAll('#navButtons button');
const containers = document.querySelectorAll('.chart-container');

buttons.forEach(button => {
  button.addEventListener('click', () => {
    // Remove active class from buttons
    buttons.forEach(btn => btn.classList.remove('active'));
    // Hide all containers
    containers.forEach(c => {
      c.style.display = 'none';
      c.classList.remove('active');
    });

    // Activate clicked button
    button.classList.add('active');
    // Show related container
    const target = button.getAttribute('data-target');
    const targetContainer = document.getElementById(target);
    targetContainer.style.display = 'block';
    targetContainer.classList.add('active');
  });
});
  </script>

</body>
</html>


</body>
</html>

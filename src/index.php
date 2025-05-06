<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="./output.css" rel="stylesheet">
  <link href="new.css" rel="stylesheet">
	<script defer src="second.js"></script>
	<style>
    
  </style>

</head>
<body>
<!-- Navbar -->
<header id="navbar" class="w-full sticky top-0 z-50 transition-colors duration-300">
  <nav class="max-w-[1320px] mx-auto flex items-center justify-between py-6 px-4 md:px-12">
    
    <!-- Logo on the left -->
    <a href="#" class="flex items-center gap-2">
    <img src="Images/Logo/LogoNav.png" alt="TaraDito Logo" style="height: 30px; width: auto;" />


    </a>

    <!-- Navigation Links on the right -->
    <ul class="flex gap-6 md:gap-8 ml-auto">
      <li><a href="#" class="text-lg font-medium text-gray-800 hover:text-white hover:bg-[#a0c4ff] py-2 px-4 rounded-full transition-all duration-300">Home</a></li>
      <li><a href="product.php" class="text-lg font-medium text-gray-800 hover:text-white hover:bg-[#a0c4ff] py-2 px-4 rounded-full transition-all duration-300">Venues</a></li>
      <li><a href="#" class="text-lg font-medium text-gray-800 hover:text-white hover:bg-[#a0c4ff] py-2 px-4 rounded-full transition-all duration-300">Explore</a></li>
      <li><a href="#" class="text-lg font-medium text-gray-800 hover:text-white hover:bg-[#a0c4ff] py-2 px-4 rounded-full transition-all duration-300">Contact</a></li>
    </ul>

  </nav>
</header>


<!-- Hero Section -->
<section class="w-full px-12 bg-blue-50 flex items-center py-50 h-full animated-bg">
    <div class="max-w-[1320px] mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 items-center">

      <!-- Hero Text -->
      <div class="space-y-8 animate-fade-left md:pr-12 lg:pr-20">
        <h1 class="text-5xl md:text-6xl leading-tight text-black font-light">
          Book spaces for <br />every kind of gathering
        </h1>
        <p class="text-lg text-gray-700">
          Whether you're planning a cozy dinner, a professional meetup, a birthday bash, or a cause-driven event—find the perfect venue, fast and hassle-free.
        </p>
        <button class="bg-black text-white text-lg font-medium py-3 px-6 rounded-full border-2 border-[#002053] hover:bg-[#a0c4ff] hover:text-black hover:border-periwinkle-400 transition-all duration-300">
          Get Started / Sign In
        </button>
      </div>

      <!-- 2x2 Square Image Grid -->
      <div class="grid grid-cols-2 grid-rows-2 gap-2 w-[500px] h-[500px] animate-spin-slow">
        <div class="w-full h-full">
          <img src="Images/Logo/Pink.png" alt="Box 1" class="w-full h-full object-cover rounded-md shadow" />
        </div>
        <div class="w-full h-full">
          <img src="Images/Logo/Yellow.png" alt="Box 2" class="w-full h-full object-cover rounded-md shadow" />
        </div>
        <div class="w-full h-full">
          <img src="Images/Logo/Blue.png" alt="Box 3" class="w-full h-full object-cover rounded-md shadow" />
        </div>
        <div class="w-full h-full">
          <img src="Images/Logo/Green.png" alt="Box 4" class="w-full h-full object-cover rounded-md shadow" />
        </div>
      </div>
    </div>
</section>

<!--SERVICES-->
<div class="wrapper mt-0">
    <div class="card-container">
        <!-- Heading -->
        <div class="section-header text-center mb-4">
            <h2 class="text-2xl font-semibold">Find your perfect event space</h2>
            <p class="text-lg text-gray-600">Browse venues and services to plan your next event with ease.</p>
        </div>

        <!-- Cards Section -->
        <div class="cards-section">
            <input type="radio" id="c1" name="card" checked>
            <label for="c1" class="card">
                <div class="row">
                    <div class="icon">1</div>
                    <div class="description">
                        <h4>INTIMATE EVENTS</h4>
                        <p>Cozy, personal spaces for dinners, proposals, and small gatherings.</p>
                    </div>
                </div>
            </label>
            <input type="radio" id="c2" name="card">
            <label for="c2" class="card">
                <div class="row">
                    <div class="icon">2</div>
                    <div class="description">
                        <h4>BUSINESS MEETUPS</h4>
                        <p>Professional venues for meetings, conferences, and networking events.</p>
                    </div>
                </div>
            </label>
            <input type="radio" id="c3" name="card">
            <label for="c3" class="card">
                <div class="row">
                    <div class="icon">3</div>
                    <div class="description">
                        <h4>PARTIES & CELEBRATIONS</h4>
                        <p>Fun, vibrant spaces for birthdays, anniversaries, and celebrations of all kinds.</p>
                    </div>
                </div>
            </label>
            <input type="radio" id="c4" name="card">
            <label for="c4" class="card">
                <div class="row">
                    <div class="icon">4</div>
                    <div class="description">
												<h4>CASUAL EVENTS</h4>
												<p>Chill spaces for hangouts, socials, casual meetups, and laid-back celebrations.</p>
                    </div>
                </div>
            </label>
        </div>
    </div>
</div>
</section>



</body>
</html>
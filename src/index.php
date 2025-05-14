<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="./output.css" rel="stylesheet">
	<script defer src="second.js"></script>
	<style>
    /* Card Styles */
.cards-section {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 2rem;
    padding-top: 2rem;
  }
  
  .card {
    width: 150px;
    height: 500px;
    background-size: cover;
    cursor: pointer;
    overflow: hidden;
    margin: 20px;
    display: flex;
    align-items: flex-end;
    transition: all 0.3s ease-in-out;
    border: 4px solid #000;
    background-color: #fff;
    padding: 2rem;
    box-shadow: 10px 10px 0 #000;
    font-family: "Arial", sans-serif;
    margin-bottom: 2rem;
  }
  
  .card:hover {
    transform: scale(1.05);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
  }
  
  .card .row {
    display: flex;
    align-items: center;
    justify-content: flex-end;
  }
  
  .card .row .icon {
    border-radius: 50%;
    width: 60px;
    height: 60px;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-left: 20px;
    color: white;
    font-weight: bold;
    transition: background-color 0.3s ease;
  }
  
  /* Custom icon colors */
  .card[for="c1"] .icon {
    background: #F28B82; /* Soft Rose */
  }
  .card[for="c1"] .icon:hover {
    background: #e06767;
  }
  
  .card[for="c2"] .icon {
    background: #7BAAF7; /* Sky Blue */
  }
  .card[for="c2"] .icon:hover {
    background: #5f90dd;
  }
  
  .card[for="c3"] .icon {
    background: #FFE066; /* Pastel Amber */
    color: #333;
  }
  .card[for="c3"] .icon:hover {
    background: #f4d149;
  }
  
  .card[for="c4"] .icon {
    background: #A8E6A3; /* Apple Green */
    color: #333;
  }
  .card[for="c4"] .icon:hover {
    background: #88ce85;
  }
  
  .card[for="c1"] {
    box-shadow: 10px 10px 0 #000, 0 0 50px #f28b82;
  }
  .card[for="c2"] {
    box-shadow: 10px 10px 0 #000, 0 0 50px #7baaf7;
  }
  .card[for="c3"] {
    box-shadow: 10px 10px 0 #000, 0 0 50px #ffe066;
  }
  .card[for="c4"] {
    box-shadow: 10px 10px 0 #000, 0 0 50px #a8e6a3;
  }
  
  .card .row .description {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    height: 120px;
    width: 600px;
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.3s ease-in-out;
    background-color: #ffffff;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    margin-left: 30px;
    transition-delay: 0.3s;
  }
  
  .card .row .description h4 {
    margin-top: 0;
    color: #002053; /* Strong contrast text color */
  }
  
  .card .row .description p {
    margin-bottom: 0;
    color: #333;
  }
  
  input {
    display: none;
  }
  
  input:checked + label {
    width: 700px;
  }
  
  input:checked + label .description {
    opacity: 1 !important;
    transform: translateY(0) !important;
  }
  
  /* Background Images */
  .card[for="c1"] {
    background-image: url(Images/Wedding.jpg);
  }
  .card[for="c2"] {
    background-image: url(Images/Business.jpg);
  }
  .card[for="c3"] {
    background-image: url(Images/Birthday.jpg);
  }
  .card[for="c4"] {
    background-image: url(Images/casual.jpeg);
  }
  
  /* Layout */
  .wrapper {
    width: 100%;
    height: 100vh;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    flex-direction: column;
  }
  
  .card-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 2rem;
  }
  
      /* Define the slow spin animation */
      @keyframes spin-slow {
        0% {
          transform: rotate(0deg);
        }
        100% {
          transform: rotate(360deg);
        }
      }
  
      /* Apply the spin-slow animation to the grid container */
      .animate-spin-slow {
        animation: spin-slow 6s linear infinite;
      }
  
      .masonry-gallery {
        column-count: 4;
        column-gap: 1rem;
      }
    
      @media (max-width: 1024px) {
        .masonry-gallery {
          column-count: 3;
        }
      }
    
      @media (max-width: 768px) {
        .masonry-gallery {
          column-count: 2;
        }
      }
    
      @media (max-width: 480px) {
        .masonry-gallery {
          column-count: 1;
        }
      }
    
      .gallery-item {
        width: 100%;
        margin-bottom: 1rem;
        border-radius: 0.5rem;
        display: block;
        break-inside: avoid;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      }
  
  .title{
       font-size: 2rem;
    font-weight: 900;
    color: #000;
    text-transform: uppercase;
    border-bottom: 2px solid #000;
    padding-bottom: 1rem;
    margin-bottom: 1.5rem;
  }

  .info-text {
    margin-top: 0.25rem;
    color: #000;
    font-weight: 600;
}

 .login-button {
    background-color: #36aef4;
    display: inline-block;
    padding: 0.75rem 1.25rem;
    text-align: center;
    font-size: 0.9rem;
    font-weight: 700;
    text-transform: uppercase;
    border: 3px solid #000;
    color: #000;
    position: relative;
    transition: all 0.2s ease;
    box-shadow: 5px 5px 0 #000;
    cursor: pointer;
    margin-right: 1rem;
    }

    .login-button:hover {
      background-color: #81bde6;
    transform: scale(1.05);
    }

    #ghost {
  position: relative;
  scale: 0.8;
}

#red {
  animation: upNDown infinite 0.5s;
  position: relative;
  width: 140px;
  height: 140px;
  display: grid;
  grid-template-columns: repeat(14, 1fr);
  grid-template-rows: repeat(14, 1fr);
  grid-column-gap: 0px;
  grid-row-gap: 0px;
  grid-template-areas:
    "a1  a2  a3  a4  a5  top0  top0  top0  top0  a10 a11 a12 a13 a14"
    "b1  b2  b3  top1 top1 top1 top1 top1 top1 top1 top1 b12 b13 b14"
    "c1 c2 top2 top2 top2 top2 top2 top2 top2 top2 top2 top2 c13 c14"
    "d1 top3 top3 top3 top3 top3 top3 top3 top3 top3 top3 top3 top3 d14"
    "e1 top3 top3 top3 top3 top3 top3 top3 top3 top3 top3 top3 top3 e14"
    "f1 top3 top3 top3 top3 top3 top3 top3 top3 top3 top3 top3 top3 f14"
    "top4 top4 top4 top4 top4 top4 top4 top4 top4 top4 top4 top4 top4 top4"
    "top4 top4 top4 top4 top4 top4 top4 top4 top4 top4 top4 top4 top4 top4"
    "top4 top4 top4 top4 top4 top4 top4 top4 top4 top4 top4 top4 top4 top4"
    "top4 top4 top4 top4 top4 top4 top4 top4 top4 top4 top4 top4 top4 top4"
    "top4 top4 top4 top4 top4 top4 top4 top4 top4 top4 top4 top4 top4 top4"
    "top4 top4 top4 top4 top4 top4 top4 top4 top4 top4 top4 top4 top4 top4"
    "st0 st0 an4 st1 an7 st2 an10 an10 st3 an13 st4 an16 st5 st5"
    "an1 an2 an3 an5 an6 an8 an9 an9 an11 an12 an14 an15 an17 an18";
}

@keyframes upNDown {
  0%,
  49% {
    transform: translateY(0px);
  }
  50%,
  100% {
    transform: translateY(-10px);
  }
}

#top0,
#top1,
#top2,
#top3,
#top4,
#st0,
#st1,
#st2,
#st3,
#st4,
#st5 {
  background-color: red;
}

#top0 {
  grid-area: top0;
}

#top1 {
  grid-area: top1;
}

#top2 {
  grid-area: top2;
}

#top3 {
  grid-area: top3;
}

#top4 {
  grid-area: top4;
}

#st0 {
  grid-area: st0;
}

#st1 {
  grid-area: st1;
}

#st2 {
  grid-area: st2;
}

#st3 {
  grid-area: st3;
}

#st4 {
  grid-area: st4;
}

#st5 {
  grid-area: st5;
}

#an1 {
  grid-area: an1;
  animation: flicker0 infinite 0.5s;
}

#an18 {
  grid-area: an18;
  animation: flicker0 infinite 0.5s;
}

#an2 {
  grid-area: an2;
  animation: flicker1 infinite 0.5s;
}

#an17 {
  grid-area: an17;
  animation: flicker1 infinite 0.5s;
}

#an3 {
  grid-area: an3;
  animation: flicker1 infinite 0.5s;
}

#an16 {
  grid-area: an16;
  animation: flicker1 infinite 0.5s;
}

#an4 {
  grid-area: an4;
  animation: flicker1 infinite 0.5s;
}

#an15 {
  grid-area: an15;
  animation: flicker1 infinite 0.5s;
}

#an6 {
  grid-area: an6;
  animation: flicker0 infinite 0.5s;
}

#an12 {
  grid-area: an12;
  animation: flicker0 infinite 0.5s;
}

#an7 {
  grid-area: an7;
  animation: flicker0 infinite 0.5s;
}

#an13 {
  grid-area: an13;
  animation: flicker0 infinite 0.5s;
}

#an9 {
  grid-area: an9;
  animation: flicker1 infinite 0.5s;
}

#an10 {
  grid-area: an10;
  animation: flicker1 infinite 0.5s;
}

#an8 {
  grid-area: an8;
  animation: flicker0 infinite 0.5s;
}

#an11 {
  grid-area: an11;
  animation: flicker0 infinite 0.5s;
}

@keyframes flicker0 {
  0%,
  49% {
    background-color: red;
  }
  50%,
  100% {
    background-color: transparent;
  }
}

@keyframes flicker1 {
  0%,
  49% {
    background-color: transparent;
  }
  50%,
  100% {
    background-color: red;
  }
}

#eye {
  width: 40px;
  height: 50px;
  position: absolute;
  top: 30px;
  left: 10px;
}

#eye::before {
  content: "";
  background-color: white;
  width: 20px;
  height: 50px;
  transform: translateX(10px);
  display: block;
  position: absolute;
}

#eye::after {
  content: "";
  background-color: white;
  width: 40px;
  height: 30px;
  transform: translateY(10px);
  display: block;
  position: absolute;
}

#eye1 {
  width: 40px;
  height: 50px;
  position: absolute;
  top: 30px;
  right: 30px;
}

#eye1::before {
  content: "";
  background-color: white;
  width: 20px;
  height: 50px;
  transform: translateX(10px);
  display: block;
  position: absolute;
}

#eye1::after {
  content: "";
  background-color: white;
  width: 40px;
  height: 30px;
  transform: translateY(10px);
  display: block;
  position: absolute;
}

#pupil {
  width: 20px;
  height: 20px;
  background-color: blue;
  position: absolute;
  top: 50px;
  left: 10px;
  z-index: 1;
  animation: eyesMovement infinite 3s;
}

#pupil1 {
  width: 20px;
  height: 20px;
  background-color: blue;
  position: absolute;
  top: 50px;
  right: 50px;
  z-index: 1;
  animation: eyesMovement infinite 3s;
}

@keyframes eyesMovement {
  0%,
  49% {
    transform: translateX(0px);
  }
  50%,
  99% {
    transform: translateX(10px);
  }
  100% {
    transform: translateX(0px);
  }
}

#shadow {
  background-color: black;
  width: 140px;
  height: 140px;
  position: absolute;
  border-radius: 50%;
  transform: rotateX(80deg);
  filter: blur(20px);
  top: 80%;
  animation: shadowMovement infinite 0.5s;
}

@keyframes shadowMovement {
  0%,
  49% {
    opacity: 0.5;
  }
  50%,
  100% {
    opacity: 0.2;
  }
}

.ghost-container {
  display: flex;
  justify-content: center; /* optional: centers the row */
  gap: 20px; /* optional: space between ghosts */
}


  </style>

</head>
<body>
<!-- Navbar -->
<header id="navbar" class="w-full sticky top-0 z-50 transition-colors duration-300">
  <nav class="max-w-[1320px] mx-auto flex items-center justify-between py-6 px-4 md:px-12">
    
    <!-- Logo on the left -->
    <a href="#" class="flex items-center gap-2">
    <img src="Images/Logo/TaraDito.png" alt="TaraDito Logo" style="height: 30px; width: auto;" />


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
        <h1 class="title">
          Book spaces for <br />every kind of gathering
        </h1>
        <p class="info-text">
          Whether you're planning a cozy dinner, a professional meetup, a birthday bash, or a cause-driven event—find the perfect venue, fast and hassle-free.
        </p>
        <a href="login_user.php" class="login-button">Log in as User</a>
        <a href="login.php" class="login-button">Log in as Admin</a>


      </div>

<div class="ghost-container">
     <div id="ghost">
  <div id="red">
    <div id="pupil"></div>
    <div id="pupil1"></div>
    <div id="eye"></div>
    <div id="eye1"></div>
    <div id="top0"></div>
    <div id="top1"></div>
    <div id="top2"></div>
    <div id="top3"></div>
    <div id="top4"></div>
    <div id="st0"></div>
    <div id="st1"></div>
    <div id="st2"></div>
    <div id="st3"></div>
    <div id="st4"></div>
    <div id="st5"></div>
    <div id="an1"></div>
    <div id="an2"></div>
    <div id="an3"></div>
    <div id="an4"></div>
    <div id="an5"></div>
    <div id="an6"></div>
    <div id="an7"></div>
    <div id="an8"></div>
    <div id="an9"></div>
    <div id="an10"></div>
    <div id="an11"></div>
    <div id="an12"></div>
    <div id="an13"></div>
    <div id="an14"></div>
    <div id="an15"></div>
    <div id="an16"></div>
    <div id="an17"></div>
    <div id="an18"></div>
  </div>
  <div id="shadow"></div>
</div>


     <div id="ghost">
  <div id="red">
    <div id="pupil"></div>
    <div id="pupil1"></div>
    <div id="eye"></div>
    <div id="eye1"></div>
    <div id="top0"></div>
    <div id="top1"></div>
    <div id="top2"></div>
    <div id="top3"></div>
    <div id="top4"></div>
    <div id="st0"></div>
    <div id="st1"></div>
    <div id="st2"></div>
    <div id="st3"></div>
    <div id="st4"></div>
    <div id="st5"></div>
    <div id="an1"></div>
    <div id="an2"></div>
    <div id="an3"></div>
    <div id="an4"></div>
    <div id="an5"></div>
    <div id="an6"></div>
    <div id="an7"></div>
    <div id="an8"></div>
    <div id="an9"></div>
    <div id="an10"></div>
    <div id="an11"></div>
    <div id="an12"></div>
    <div id="an13"></div>
    <div id="an14"></div>
    <div id="an15"></div>
    <div id="an16"></div>
    <div id="an17"></div>
    <div id="an18"></div>
  </div>
  <div id="shadow"></div>
</div>



     <div id="ghost">
  <div id="red">
    <div id="pupil"></div>
    <div id="pupil1"></div>
    <div id="eye"></div>
    <div id="eye1"></div>
    <div id="top0"></div>
    <div id="top1"></div>
    <div id="top2"></div>
    <div id="top3"></div>
    <div id="top4"></div>
    <div id="st0"></div>
    <div id="st1"></div>
    <div id="st2"></div>
    <div id="st3"></div>
    <div id="st4"></div>
    <div id="st5"></div>
    <div id="an1"></div>
    <div id="an2"></div>
    <div id="an3"></div>
    <div id="an4"></div>
    <div id="an5"></div>
    <div id="an6"></div>
    <div id="an7"></div>
    <div id="an8"></div>
    <div id="an9"></div>
    <div id="an10"></div>
    <div id="an11"></div>
    <div id="an12"></div>
    <div id="an13"></div>
    <div id="an14"></div>
    <div id="an15"></div>
    <div id="an16"></div>
    <div id="an17"></div>
    <div id="an18"></div>
  </div>
  <div id="shadow"></div>
</div>

 </div>

    
    </div>
</section>

<!--SERVICES-->
<div class="wrapper mt-0">
    <div class="card-container">
        <!-- Heading -->
        <div class="section-header text-center mb-4">
            <h2 class="title">Find your perfect event space</h2>
            <p class="info-text">Browse venues and services to plan your next event with ease.</p>
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




</body>
</html>
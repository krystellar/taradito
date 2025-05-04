<!-- listing.html -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Villa Details</title>
  <script defer src="script.js"></script>
  <link href="output.css" rel="stylesheet">
  <script defer src="second.js"></script>
  <head>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>
<body class="bg-gray-50">

   <!-- Navbar -->
	<header id="navbar" class="w-full sticky top-0 z-50 transition-colors duration-300">
  <nav class="max-w-[1320px] mx-auto flex items-center justify-between py-6 px-4 md:px-12">
    <!-- Logo on the left, using absolute positioning -->
    <a href="#" class="flex items-center gap-2 absolute left-10 pl-4">
      <img src="Images/Logo/LogoNav.png" alt="TaraDito Logo" class="h-[60px] w-auto" /> <!-- Increase logo size if needed -->
    </a>
    <ul class="flex gap-6 md:gap-8 flex-grow justify-center">
      <li><a href="#" class="text-lg font-medium text-gray-800 hover:text-white hover:bg-[#a0c4ff] py-2 px-4 rounded-full transition-all duration-300">Home</a></li>
      <li><a href="product.php" class="text-lg font-medium text-gray-800 hover:text-white hover:bg-[#a0c4ff] py-2 px-4 rounded-full transition-all duration-300">Venues</a></li>
      <li><a href="#" class="text-lg font-medium text-gray-800 hover:text-white hover:bg-[#a0c4ff] py-2 px-4 rounded-full transition-all duration-300">Explore</a></li>
      <li><a href="#" class="text-lg font-medium text-gray-800 hover:text-white hover:bg-[#a0c4ff] py-2 px-4 rounded-full transition-all duration-300">Contact</a></li>
    </ul>
  </nav>
</header>

  <div class="max-w-6xl mx-auto p-6">
    <h1 id="villa-title" class="text-3xl font-bold mb-4"></h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
      <img id="main-image" src="" class="rounded-lg w-full object-cover h-80" />
      <div class="grid grid-cols-2 gap-2">
        <img id="img2" class="rounded-md" />
        <img id="img3" class="rounded-md" />
        <img id="img4" class="rounded-md" />
        <img id="img5" class="rounded-md" />
      </div>
    </div>

    <p id="villa-location" class="text-gray-700 text-lg mb-2"></p>
    <p id="villa-specs" class="text-sm mb-1"></p>
    <p id="villa-rating" class="mb-2 text-yellow-600"></p>
    <p id="villa-price" class="text-xl font-semibold text-pink-600"></p>

    <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
      <div>
        <p class="font-medium">Check-in</p>
        <p>5/4/2025</p>
      </div>
      <div>
        <p class="font-medium">Checkout</p>
        <p>5/9/2025</p>
      </div>
    </div>
  

  <!-- Host and Highlights -->
<div class="mt-10 border-t pt-6 space-y-4">
  <div class="flex items-center space-x-4">
    <img src="https://i.pravatar.cc/50?img=3" alt="Host avatar" class="w-12 h-12 rounded-full">
    <div>
      <p class="font-semibold">Hosted by PT Bali Superhost Management</p>
      <p class="text-sm text-gray-600">Superhost · 4 years hosting</p>
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
    <div class="flex items-start space-x-3">
      <span>🏊</span>
      <div>
        <p class="font-medium">Swim in the infinity pool</p>
        <p class="text-sm text-gray-600">It's one of the many things that makes this home special.</p>
      </div>
    </div>
    <div class="flex items-start space-x-3">
      <span>🔑</span>
      <div>
        <p class="font-medium">Self check-in</p>
        <p class="text-sm text-gray-600">You can check in with the building staff.</p>
      </div>
    </div>
    <div class="flex items-start space-x-3">
      <span>🏠</span>
      <div>
        <p class="font-medium">Extra spacious</p>
        <p class="text-sm text-gray-600">Guests love this home’s spaciousness for a comfortable stay.</p>
      </div>
    </div>
  </div>
</div>

<!-- Villa Description -->
<div class="mt-10 border-t pt-6">
  <h2 class="text-xl font-semibold mb-2">About this place</h2>
  <p class="text-gray-700 leading-relaxed">
    This villa encapsulates all that is great about Ubud within one place, its superbly large and spacious design dominates the verticality of its overall structure.
    With 6 bedrooms located in different portions of the house, this villa thrives in being wide open and perfect for your family gatherings.
    In each portion of the house you will find naturally cold breezes that drift and flow through the entirety of the villa with no effort.
  </p>
</div>

<!-- Where You'll Sleep -->
<div class="mt-10 border-t pt-6">
  <h2 class="text-xl font-semibold mb-4">Where you'll sleep</h2>
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="rounded-lg overflow-hidden shadow">
      <img src="https://source.unsplash.com/400x300/?bedroom" class="w-full h-48 object-cover" />
      <div class="p-3">
        <p class="font-medium">Bedroom 1</p>
        <p class="text-sm text-gray-500">1 king bed</p>
      </div>
    </div>
    <div class="rounded-lg overflow-hidden shadow">
      <img src="https://source.unsplash.com/400x300/?bedroom,2" class="w-full h-48 object-cover" />
      <div class="p-3">
        <p class="font-medium">Bedroom 2</p>
        <p class="text-sm text-gray-500">1 queen bed</p>
      </div>
    </div>
    <div class="rounded-lg overflow-hidden shadow">
      <img src="https://source.unsplash.com/400x300/?bedroom,3" class="w-full h-48 object-cover" />
      <div class="p-3">
        <p class="font-medium">Bedroom 3</p>
        <p class="text-sm text-gray-500">2 single beds</p>
      </div>
    </div>
  </div>
</div>
</div>

<div class="max-w-6xl mx-auto p-6">
  <!-- What this place offers -->
  <section class="mb-10">
    <h2 class="text-3xl font-semibold mb-6">What this place offers</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
      <!-- Amenity Item -->
      <div class="flex items-center space-x-3">
        <span class="w-6 h-6 bg-gray-300 rounded-full"></span>
        <span>Waterfront</span>
      </div>
      <div class="flex items-center space-x-3">
        <span class="w-6 h-6 bg-gray-300 rounded-full"></span>
        <span>Wifi</span>
      </div>
      <div class="flex items-center space-x-3">
        <span class="w-6 h-6 bg-gray-300 rounded-full"></span>
        <span>Pool</span>
      </div>
      <div class="flex items-center space-x-3">
        <span class="w-6 h-6 bg-gray-300 rounded-full"></span>
        <span>TV</span>
      </div>
      <div class="flex items-center space-x-3">
        <span class="w-6 h-6 bg-gray-300 rounded-full"></span>
        <span>EV Charger</span>
      </div>
      <div class="flex items-center space-x-3">
        <span class="w-6 h-6 bg-gray-300 rounded-full"></span>
        <span>Kitchen</span>
      </div>
      <div class="flex items-center space-x-3">
        <span class="w-6 h-6 bg-gray-300 rounded-full"></span>
        <span>Free Parking</span>
      </div>
      <div class="flex items-center space-x-3">
        <span class="w-6 h-6 bg-gray-300 rounded-full"></span>
        <span>Shared Sauna</span>
      </div>
      <div class="flex items-center space-x-3">
        <span class="w-6 h-6 bg-gray-300 rounded-full"></span>
        <span>Elevator</span>
      </div>
      <div class="flex items-center space-x-3">
        <span class="w-6 h-6 bg-gray-300 rounded-full"></span>
        <span>Air Conditioning</span>
      </div>
    </div>
    <button class="mt-6 px-6 py-2 border rounded-lg hover:bg-gray-100 mb-6">Show all 73 amenities</button>
</section>

  <section class="grid grid-cols-1 md:grid-cols-2 gap-6 my-10">
  <!-- Booking Section -->
  <div class="border rounded-lg p-6 shadow-md bg-white">
    <h2 class="text-2xl font-bold mb-4">Reserve your stay</h2>
    <p class="text-xl font-semibold mb-6" id="villa-price"></p>

    <div class="flex flex-col gap-4 mb-6">
      <input id="checkin" type="text" placeholder="Check-in" class="border p-2 rounded">
      <input id="checkout" type="text" placeholder="Check-out" class="border p-2 rounded">
    </div>

    <button class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded w-full">
      Reserve
    </button>
    <p class="text-center text-gray-500 text-sm mt-2">You won't be charged yet</p>
  </div>

  <!-- Ratings and Reviews Section -->
  <div class="border rounded-lg p-6 shadow-md bg-white">
    <div class="mb-6">
      <h2 class="text-3xl font-bold mb-4">Ratings</h2>
      <div class="grid grid-cols-2 gap-4">
        <div class="flex flex-col items-center">
          <span class="text-4xl font-bold text-pink-600">5.0</span>
          <span class="text-gray-600">Cleanliness</span>
        </div>
        <div class="flex flex-col items-center">
          <span class="text-4xl font-bold text-pink-600">5.0</span>
          <span class="text-gray-600">Accuracy</span>
        </div>
        <div class="flex flex-col items-center">
          <span class="text-4xl font-bold text-pink-600">5.0</span>
          <span class="text-gray-600">Check-in</span>
        </div>
        <div class="flex flex-col items-center">
          <span class="text-4xl font-bold text-pink-600">5.0</span>
          <span class="text-gray-600">Communication</span>
        </div>
        <div class="flex flex-col items-center">
          <span class="text-4xl font-bold text-pink-600">5.0</span>
          <span class="text-gray-600">Location</span>
        </div>
        <div class="flex flex-col items-center">
          <span class="text-4xl font-bold text-pink-600">4.9</span>
          <span class="text-gray-600">Value</span>
        </div>
      </div>
    </div>

    <div class="border-t my-6"></div>

    <div>
      <h2 class="text-2xl font-bold mb-4">Guest Reviews</h2>
      <div class="space-y-6">
        <!-- Review 1 -->
        <div class="flex items-start gap-4">
          <img src="https://i.pravatar.cc/40?img=1" class="rounded-full" alt="User" />
          <div>
            <p class="font-bold">SShefali</p>
            <p class="text-gray-500 text-sm">2 days ago</p>
            <p class="mt-2">Beautiful space, high Rise, very spacious, beautiful little balcony, very responsive.</p>
          </div>
        </div>
        <!-- Review 2 -->
        <div class="flex items-start gap-4">
          <img src="https://i.pravatar.cc/40?img=2" class="rounded-full" alt="User" />
          <div>
            <p class="font-bold">Alex</p>
            <p class="text-gray-500 text-sm">1 week ago</p>
            <p class="mt-2">Very pleasant experience. Pool is amazing and all eateries are nearby.</p>
          </div>
        </div>
        <!-- More reviews if you want -->
      </div>

      <button class="mt-6 w-full border border-gray-400 text-gray-700 py-2 rounded hover:bg-gray-100">
        Show all 96 reviews
      </button>
    </div>
  </div>
</section>

<section class="mt-10">
  <h2 class="text-2xl font-bold mb-4">Where you'll be</h2>
  <p id="villa-location" class="text-gray-600 mb-6">Gurugram, Haryana, India</p>

  <div id="map" style="height: 400px;" class="w-full rounded-lg shadow-md"></div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const map = L.map('map').setView([28.4183, 77.0473], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.marker([28.4183, 77.0473], {
      icon: L.icon({
        iconUrl: "https://cdn-icons-png.flaticon.com/512/1946/1946433.png",
        iconSize: [50, 50],
        iconAnchor: [25, 50],
        popupAnchor: [0, -50]
      })
    }).addTo(map)
      .bindPopup('Your Location')
      .openPopup();
  });
</script>

<!-- Meet Your Host Section -->
<section class="mt-10">
  <h2 class="text-2xl font-bold mb-4">Meet your host</h2>
  <div class="flex flex-col lg:flex-row gap-8">
    <!-- Host Card -->
    <div class="bg-white rounded-xl shadow-md p-6 w-full max-w-sm text-center">
      <div class="relative w-24 h-24 mx-auto rounded-full overflow-hidden">
        <img src="https://a0.muscache.com/im/pictures/user/8e58f167-e5d3-4cd3-aed1-47f7e6b10977.jpg" alt="Host profile" class="object-cover w-full h-full">
        <div class="absolute bottom-0 right-0 w-6 h-6 bg-white rounded-full flex items-center justify-center">
          <img src="https://a0.muscache.com/pictures/0da0021c-97a8-42c1-834b-f9cb4c558b07.jpg" alt="Superhost badge" class="w-5 h-5">
        </div>
      </div>
      <h3 class="mt-3 text-lg font-semibold">Sumit</h3>
      <p class="text-sm text-gray-500">Superhost</p>

      <div class="flex justify-center gap-6 mt-4 text-sm text-gray-700">
        <div>
          <p class="font-bold text-lg">695</p>
          <p class="text-xs text-gray-500">Reviews</p>
        </div>
        <div>
          <p class="font-bold text-lg">4.98★</p>
          <p class="text-xs text-gray-500">Rating</p>
        </div>
        <div>
          <p class="font-bold text-lg">1</p>
          <p class="text-xs text-gray-500">Year hosting</p>
        </div>
      </div>

      <div class="mt-6 text-sm text-gray-700 text-left">
        <p class="flex items-center mb-1">📍 Born in the 80s</p>
        <p class="flex items-center">💼 My work: Hospitality</p>
      </div>

      <p class="mt-4 text-sm text-gray-600 text-left">
        Hello, Welcome to my profile. I am Sumit. I love travelling, exploring, adventuring, researching,...
      </p>

      <a href="#" class="text-blue-600 text-sm mt-2 inline-block">Show more</a>

      <button class="mt-4 bg-black text-white px-4 py-2 rounded hover:bg-gray-800 w-full">
        Message host
      </button>

      <p class="text-xs text-gray-400 mt-3">
        To help protect your payment, always use Airbnb to send money and communicate with hosts.
      </p>
    </div>

    <!-- Host Info -->
    <div class="flex-1 mt-6 lg:mt-0">
      <h3 class="text-lg font-semibold mb-2">Sumit is a Superhost</h3>
      <p class="text-sm text-gray-600 mb-4">
        Superhosts are experienced, highly rated hosts who are committed to providing great stays for guests.
      </p>
      <p class="text-sm text-gray-600"><strong>Response rate:</strong> 100%</p>
      <p class="text-sm text-gray-600"><strong>Responds within:</strong> an hour</p>
    </div>
  </div>
</section>

<!-- Things to Know Section -->
<section class="mt-10">
  <h2 class="text-2xl font-bold mb-4">Things to know</h2>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm text-gray-700">
    <!-- House Rules -->
    <div>
      <h3 class="font-semibold text-base mb-2">House rules</h3>
      <p>Check-in after 2:00 PM</p>
      <p>Checkout before 11:00 AM</p>
      <p>2 guests maximum</p>
      <a href="#" class="text-blue-600 mt-2 inline-block">Show more</a>
    </div>
    <!-- Safety -->
    <div>
      <h3 class="font-semibold text-base mb-2">Safety & property</h3>
      <p>Carbon monoxide alarm</p>
      <p>Smoke alarm</p>
      <a href="#" class="text-blue-600 mt-2 inline-block">Show more</a>
    </div>
    <!-- Cancellation -->
    <div>
      <h3 class="font-semibold text-base mb-2">Cancellation policy</h3>
      <p>Free cancellation before May 11. Cancel before check-in on May 12 for a partial refund.</p>
      <a href="#" class="text-blue-600 mt-2 inline-block">Show more</a>
    </div>
  </div>
</section>



</body>
</html>

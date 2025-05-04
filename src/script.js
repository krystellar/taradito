// script.js
const listings = {
    1: {
      title: "Ahava 6BR with Gym, Massage Room & Yoga Shala",
      location: "Kecamatan Ubud, Indonesia",
      specs: "12 guests · 6 bedrooms · 6 beds · 6 baths",
      rating: "⭐ 4.87 · 82 reviews",
      price: "₱118,426 for 5 nights (includes all fees)",
      images: [
        "https://source.unsplash.com/800x600/?villa",
        "https://source.unsplash.com/400x300/?resort-lounge",
        "https://source.unsplash.com/400x300/?resort-dining",
        "https://source.unsplash.com/400x300/?resort-view",
        "https://source.unsplash.com/400x300/?spa-room"
      ]
    },

    2: {
        title: "Mountain View Retreat in Balamban",
        location: "Balamban, Philippines",
        specs: "8 guests · 4 bedrooms · 4 beds · 3 baths",
        rating: "⭐ 4.92 · 67 reviews",
        price: "₱68,000 for 5 nights (May 27 – Jun 1)",
        images: [
          "https://source.unsplash.com/800x600/?villa,balamban",
          "https://source.unsplash.com/400x300/?forest-villa",
          "https://source.unsplash.com/400x300/?villa-dining",
          "https://source.unsplash.com/400x300/?villa-balcony",
          "https://source.unsplash.com/400x300/?villa-bathroom"
        ]
      }
      
    // Add more listings using IDs like 2, 3, etc.
  };
  
  const params = new URLSearchParams(window.location.search);
  const id = params.get("id");
  const data = listings[id];
  
  if (data) {
    document.getElementById("villa-title").textContent = data.title;
    document.getElementById("villa-location").textContent = data.location;
    document.getElementById("villa-specs").textContent = data.specs;
    document.getElementById("villa-rating").textContent = data.rating;
    document.getElementById("villa-price").textContent = data.price;
  
    const [img1, img2, img3, img4, img5] = data.images;
    document.getElementById("main-image").src = img1;
    document.getElementById("img2").src = img2;
    document.getElementById("img3").src = img3;
    document.getElementById("img4").src = img4;
    document.getElementById("img5").src = img5;
  } else {
    document.body.innerHTML = "<div class='text-center mt-10 text-xl text-red-600'>Listing not found.</div>";
  }
  
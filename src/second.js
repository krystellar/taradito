const navbar = document.getElementById('navbar');

  function updateNavbar() {
    if (window.scrollY > 50) {
      navbar.classList.remove('bg-blue-50');
      navbar.classList.add('bg-white', 'shadow-md');
    } else {
      navbar.classList.remove('bg-white', 'shadow-md');
      navbar.classList.add('bg-blue-50');
    }
  }

  window.addEventListener('scroll', updateNavbar);
  window.addEventListener('load', updateNavbar);

 document.addEventListener("DOMContentLoaded", function () {
    const buttons = document.querySelectorAll(".category-nav .button");

    buttons.forEach(button => {
      button.addEventListener("click", function () {
        buttons.forEach(btn => btn.classList.remove("active")); // remove from all
        this.classList.add("active"); // add to clicked one
      });
    });
  });


  document.addEventListener('DOMContentLoaded', function() {
    const categoryLabels = document.querySelectorAll('.category-label');
    
    categoryLabels.forEach(label => {
        const checkbox = label.querySelector('input[type="checkbox"]');
        const button = label.querySelector('.category-button');

        // Add click event to toggle the "active" class
        label.addEventListener('click', function() {
            // Toggle the active state of the label (which persists the color)
            label.classList.toggle('active');
            
            // Also toggle the checkbox checked state to keep it consistent
            checkbox.checked = !checkbox.checked;
        });

        // Optional: Update the button color dynamically based on the "data-color" attribute
        button.style.backgroundColor = label.getAttribute('data-color');
    });
});

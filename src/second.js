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

  
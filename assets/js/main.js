document.addEventListener('DOMContentLoaded', function () {
  const links = document.querySelectorAll('a[href]');

  links.forEach((link) => {
    const href = link.getAttribute('href');
    if (href && href.endsWith('.html')) {
      link.setAttribute('data-page', href);
    }
  });

  console.log('Hotel Booking System initialized');
});

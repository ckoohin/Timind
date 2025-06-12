console.log('Dashboard script loaded.');

<<<<<<< HEAD
document.addEventListener('DOMContentLoaded', function () {
  // Add hover effects for schedule items
  const scheduleItems = document.querySelectorAll('.schedule-item');
  scheduleItems.forEach(item => {
    item.addEventListener('mouseenter', function () {
      this.style.transform = 'translateY(-2px)';
      this.style.boxShadow = '0 8px 20px rgba(0, 0, 0, 0.15)';
    });

    item.addEventListener('mouseleave', function () {
=======
document.addEventListener('DOMContentLoaded', function() {
  const scheduleItems = document.querySelectorAll('.schedule-item');
  scheduleItems.forEach(item => {
    item.addEventListener('mouseenter', function() {
      this.style.transform = 'translateY(-2px)';
      this.style.boxShadow = '0 8px 20px rgba(0, 0, 0, 0.15)';
    });
        
    item.addEventListener('mouseleave', function() {
>>>>>>> be490f0617e04cab9bb59357c07635e0ab0bb723
      this.style.transform = 'translateY(0)';
      this.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.08)';
    });
  });
<<<<<<< HEAD

  // Add click handlers for navigation
  const navLinks = document.querySelectorAll('.nav-link');
  navLinks.forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      navLinks.forEach(l => l.classList.remove('active'));
      this.classList.add('active');
    });
  });

  // Add click handler for new event button
  document.querySelector('.new-event-btn').addEventListener('click', function () {
=======
      
  document.addEventListener('DOMContentLoaded', function() {
  const navLinks = document.querySelectorAll('.nav-link');
  const currentUrl = window.location.pathname; 

  navLinks.forEach(link => {
    if (link.getAttribute('href') === currentUrl) {
      link.classList.add('active');
    }
    link.addEventListener('click', function(e) {
      navLinks.forEach(l => l.classList.remove('active'));
      this.classList.add('active');
    });
    });
  });
      
  document.querySelector('.new-event-btn').addEventListener('click', function() {
>>>>>>> be490f0617e04cab9bb59357c07635e0ab0bb723
    alert('Chức năng thêm sự kiện mới sẽ được triển khai!');
  });
});
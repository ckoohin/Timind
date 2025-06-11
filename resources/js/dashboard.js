console.log('Dashboard script loaded.');

document.addEventListener('DOMContentLoaded', function () {
  // Add hover effects for schedule items
  const scheduleItems = document.querySelectorAll('.schedule-item');
  scheduleItems.forEach(item => {
    item.addEventListener('mouseenter', function () {
      this.style.transform = 'translateY(-2px)';
      this.style.boxShadow = '0 8px 20px rgba(0, 0, 0, 0.15)';
    });

    item.addEventListener('mouseleave', function () {
      this.style.transform = 'translateY(0)';
      this.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.08)';
    });
  });

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
    alert('Chức năng thêm sự kiện mới sẽ được triển khai!');
  });
});
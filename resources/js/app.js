import './bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});

// Auto-hide alerts
setTimeout(function() {
    let alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        let bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);

console.log('Timind App Loaded!');
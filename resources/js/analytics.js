document.addEventListener('DOMContentLoaded', function() {
  const scheduleItems = document.querySelectorAll('.schedule-item');
  scheduleItems.forEach(item => {
    item.addEventListener('mouseenter', function() {
      this.style.transform = 'translateY(-2px)';
      this.style.boxShadow = '0 8px 20px rgba(0, 0, 0, 0.15)';
    });
        
    item.addEventListener('mouseleave', function() {
      this.style.transform = 'translateY(0)';
      this.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.08)';
    });
  });
      
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
    alert('Chức năng thêm sự kiện mới sẽ được triển khai!');
  });
});

document.addEventListener('DOMContentLoaded', function() {
    initializeChart();
    
    setupPeriodDropdown();
    
    setupCategoryPeriodDropdown();
    
    loadCategoryStats();
});

let studyChart = null;

function initializeChart() {
    const ctx = document.getElementById('studyChart');
    if (!ctx) return;
    
    const initialLabels = window.analyticsData?.days || [];
    const initialData = window.analyticsData?.studyHours || [];
    
    studyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: initialLabels,
            datasets: [{
                label: 'Giờ học',
                data: initialData,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#0d6efd',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return `${context.parsed.y}h học tập`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    },
                    ticks: {
                        callback: function(value) {
                            return value + 'h';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            elements: {
                point: {
                    hoverBackgroundColor: '#0d6efd'
                }
            }
        }
    });
}

function setupPeriodDropdown() {
    const dropdownItems = document.querySelectorAll('[data-period]');
    const dropdownButton = document.querySelector('#periodDropdown');
    
    dropdownItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const period = this.getAttribute('data-period');
            const periodText = this.textContent.trim();
            
            // Cập nhật text của button
            dropdownButton.textContent = periodText;
            
            // Load dữ liệu mới
            loadChartData(period);
        });
    });
}

function setupCategoryPeriodDropdown() {
    const dropdownItems = document.querySelectorAll('[data-category-period]');
    const dropdownButton = document.querySelector('#categoryPeriodDropdown');
    
    dropdownItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const period = this.getAttribute('data-category-period');
            const periodText = this.textContent.trim();
            
            // Cập nhật text của button
            dropdownButton.textContent = periodText;
            
            // Load dữ liệu category mới
            loadCategoryStats(period);
        });
    });
}

async function loadChartData(period) {
    try {
        showLoading();
        
        const response = await fetch(`${window.analyticsData.routes.analyticsData}?period=${period}`);
        const data = await response.json();
        
        // Cập nhật biểu đồ
        studyChart.data.labels = data.labels;
        studyChart.data.datasets[0].data = data.data;
        studyChart.update('active');
        
        // Cập nhật thống kê
        updateStats(data.data);
        
        hideLoading();
    } catch (error) {
        console.error('Error loading chart data:', error);
        hideLoading();
        showError('Không thể tải dữ liệu biểu đồ');
    }
}

function updateStats(data) {
    const total = data.reduce((sum, hours) => sum + hours, 0);
    const average = total > 0 ? (total / data.length).toFixed(1) : 0;
    const max = Math.max(...data);
    const studyDays = data.filter(hours => hours > 0).length;
    
    // Cập nhật các thẻ thống kê
    document.querySelector('.stat-total').textContent = total.toFixed(1) + 'h';
    document.querySelector('.stat-average').textContent = average + 'h';
    document.querySelector('.stat-max').textContent = max.toFixed(1) + 'h';
    document.querySelector('.stat-days').textContent = `${studyDays}/${data.length}`;
}

async function loadCategoryStats(period = '30days') {
    try {
        const response = await fetch(`${window.analyticsData.routes.categoryStats}?period=${period}`);
        const categories = await response.json();
        
        displayCategoryStats(categories);
    } catch (error) {
        console.error('Error loading category stats:', error);
        // Show error message in category stats container
        const container = document.getElementById('categoryStats');
        if (container) {
            container.innerHTML = `
                <div class="text-center text-muted">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p class="mt-2 mb-0">Không thể tải dữ liệu</p>
                </div>
            `;
        }
    }
}

function displayCategoryStats(categories) {
    const container = document.getElementById('categoryStats');
    if (!container) return;
    
    container.innerHTML = '';
    
    if (categories.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted">
                <i class="fas fa-chart-pie"></i>
                <p class="mt-2 mb-0">Chưa có dữ liệu</p>
            </div>
        `;
        return;
    }
    
    categories.forEach(category => {
        const categoryElement = document.createElement('div');
        categoryElement.className = 'category-stat-item d-flex justify-content-between align-items-center mb-2';
        categoryElement.innerHTML = `
            <div class="d-flex align-items-center">
                <div class="category-color-dot me-2" style="background-color: ${category.color || '#6c757d'}"></div>
                <span class="category-name">${category.name}</span>
            </div>
            <span class="category-hours fw-bold">${category.hours}h</span>
        `;
        container.appendChild(categoryElement);
    });
}

function showLoading() {
    const loadingOverlay = document.createElement('div');
    loadingOverlay.id = 'loadingOverlay';
    loadingOverlay.className = 'loading-overlay';
    loadingOverlay.innerHTML = `
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    `;
    document.body.appendChild(loadingOverlay);
}

function hideLoading() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        loadingOverlay.remove();
    }
}

function showError(message) {
    const toast = document.createElement('div');
    toast.className = 'toast align-items-center text-white bg-danger border-0 position-fixed top-0 end-0 m-3';
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 5000);
}

window.analyticsApp = {
    loadChartData,
    loadCategoryStats,
    updateStats
};
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timind Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    @vite(['resources/css/components/analytics.css', 'resources/js/app.js', 'resources/js/analytics.js'])
</head>
<body>
    <div class="container-fluid">
        <div class="main-card">
            <div class="row g-0">
                <div class="col-md-3">
                    <div class="sidebar">
                        <div class="logo">
                            Timind
                            <span class="logo-badge">CodeX</span>
                        </div>

                        <button class="new-event-btn">
                            <i class="fas fa-plus"></i>
                            Sự kiện mới
                        </button>

                        <ul class="nav-menu">
                            <li class="nav-item">
                                <a href="{{ route('dashboard') }}" class="nav-link">
                                    <i class="fas fa-chart-pie"></i>
                                    Tổng quan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('activities.index') }}" class="nav-link">
                                    <i class="fas fa-calendar-alt"></i>
                                    Thời gian biểu
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('goals.index') }}" class="nav-link">
                                    <i class="fas fa-bullseye"></i>
                                    Mục tiêu học
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('analytics.index') }}" class="nav-link active">
                                    <i class="fas fa-chart-line"></i>
                                    Phân tích
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-cog"></i>
                                    Cài đặt
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="main-content">
                        <div class="header d-flex justify-content-between align-items-center px-4 py-3 shadow-sm bg-white">
                            <h1 class="h4 mb-0">Phân tích</h1>

                            @auth
                                <div class="user-info d-flex align-items-center position-relative">
                                    <div class="dropdown">
                                        <button class="btn d-flex align-items-center border-0 bg-transparent"
                                            id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <div class="user-avatar rounded-circle bg-primary text-white fw-bold d-flex justify-content-center align-items-center me-2"
                                                style="width: 35px; height: 35px;">
                                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                            </div>
                                            <span class="me-1">{{ Auth::user()->name }}</span>
                                            <i class="fas fa-chevron-down text-muted"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                            <li><a class="dropdown-item" href="#">Cài đặt</a></li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <form method="POST" action="{{ route('logout') }}">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item">Đăng xuất</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            @endauth
                        </div>

                        <div class="analytics-container">
                            <div class="row g-4 mb-4">
                                <div class="col-lg-3 col-md-6">
                                    <div class="card border-0 shadow-sm h-100 card-hover stat-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="stat-icon bg-primary text-white rounded-3 me-3">
                                                    <i class="fas fa-clock"></i>
                                                </div>
                                                <div>
                                                    <h6 class="text-muted mb-1 fw-normal">Trung bình/ngày</h6>
                                                    <h3 class="mb-0 fw-bold text-primary stat-number stat-average">{{ $averageHours }}h</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-3 col-md-6">
                                    <div class="card border-0 shadow-sm h-100 card-hover stat-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="stat-icon bg-success text-white rounded-3 me-3">
                                                    <i class="fas fa-chart-line"></i>
                                                </div>
                                                <div>
                                                    <h6 class="text-muted mb-1 fw-normal">Tổng tuần này</h6>
                                                    <h3 class="mb-0 fw-bold text-success stat-number stat-total">{{ $totalHours }}h</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-3 col-md-6">
                                    <div class="card border-0 shadow-sm h-100 card-hover stat-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="stat-icon bg-warning text-white rounded-3 me-3">
                                                    <i class="fas fa-trophy"></i>
                                                </div>
                                                <div>
                                                    <h6 class="text-muted mb-1 fw-normal">Cao nhất</h6>
                                                    <h3 class="mb-0 fw-bold text-warning stat-number stat-max">{{ $maxHours }}h</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-3 col-md-6">
                                    <div class="card border-0 shadow-sm h-100 card-hover stat-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="stat-icon bg-info text-white rounded-3 me-3">
                                                    <i class="fas fa-calendar-check"></i>
                                                </div>
                                                <div>
                                                    <h6 class="text-muted mb-1 fw-normal">Ngày học</h6>
                                                    <h3 class="mb-0 fw-bold text-info stat-number stat-days">{{ $studyDays }}/7</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="col-lg-8">
                                    <div class="card border-0 shadow-sm chart-card">
                                        <div class="card-header bg-white border-0 chart-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5 class="mb-1 fw-bold">Thời gian học theo ngày</h5>
                                                    <p class="text-muted mb-0 small">Biểu đồ thể hiện số giờ học mỗi ngày</p>
                                                </div>
                                                <div class="dropdown">
                                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" 
                                                            id="periodDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                        7 ngày
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="periodDropdown">
                                                        <li><a class="dropdown-item" href="#" data-period="7days">7 ngày</a></li>
                                                        <li><a class="dropdown-item" href="#" data-period="30days">30 ngày</a></li>
                                                        <li><a class="dropdown-item" href="#" data-period="3months">3 tháng</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body chart-body">
                                            <div class="chart-container">
                                                <canvas id="studyChart" height="350"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    <div class="col-lg-4">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-header bg-white border-0 pb-0">
                                                <h5 class="mb-1 fw-bold">Tháng {{ $currentMonth }}</h5>
                                                <p class="text-muted mb-0 small">Tổng quan tháng này</p>
                                            </div>
                                            <div class="card-body">
                                                <div class="text-center mb-4">
                                                    <div class="progress-circle mx-auto mb-3">
                                                        <svg width="120" height="120">
                                                            <circle cx="60" cy="60" r="50" fill="none" stroke="#e9ecef" stroke-width="8"/>
                                                            <circle cx="60" cy="60" r="50" fill="none" stroke="#0d6efd" stroke-width="8" 
                                                                    stroke-dasharray="314" stroke-dashoffset="78.5" stroke-linecap="round"/>
                                                        </svg>
                                                        <div class="progress-text">
                                                            <h3 class="mb-0 fw-bold text-primary">{{ $monthlyTotal }}h</h3>
                                                            <small class="text-muted">Tổng giờ học</small>
                                                        </div>
                                                    </div>
                                                    <p class="text-muted mb-0">Mục tiêu: 100h</p>
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-6">
                                                        <div class="bg-light rounded-3 p-3 text-center">
                                                            <h6 class="mb-1 fw-bold text-success">{{ round($monthlyTotal/30, 1) }}h</h6>
                                                            <small class="text-muted">TB/ngày</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="bg-light rounded-3 p-3 text-center">
                                                            <h6 class="mb-1 fw-bold text-info">{{ round(($monthlyTotal/100)*100) }}%</h6>
                                                            <small class="text-muted">Hoàn thành</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.analyticsData = {
            days: @json($days),
            studyHours: @json($studyHours),
            routes: {
                analyticsData: '{{ route("analytics.data") }}',
                categoryStats: '{{ route("analytics.categories") }}',
                monthlyData: '{{ route("analytics.monthly") }}'
            }
        };
    </script>
</body>
</html>
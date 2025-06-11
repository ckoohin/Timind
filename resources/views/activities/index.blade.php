<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timind - Thời gian biểu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.9/index.global.min.js"></script>
    @vite(['resources/css/components/activities.css', 'resources/js/app.js','resources/js/components/activities.js'])
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
                        <button class="new-event-btn" data-bs-toggle="modal" data-bs-target="#eventModal">
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
                                <a href="{{ route('activities.index') }}" class="nav-link active">
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
                                <a href="{{ route('analytics.index') }}" class="nav-link">
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
                        <div class="header">
                            <h1 class="header-title">Thời gian biểu</h1>
                            <div class="header-controls">
                                <div class="date">
                                    <input type="date" id="currentDate" class="form-control d-inline-block" style="width: 160px;" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                </div>
                                <select class="form-select" style="width: auto;">
                                    <option>Tuần</option>
                                    <option>Tháng</option>
                                    <option>Ngày</option>
                                </select>
                                @auth
                                <div class="user-info d-flex align-items-center position-relative">
                                    <div class="dropdown me-3">
                                        <button class="btn border-0 bg-transparent p-0" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-bell notification-bell text-primary fs-5"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown" style="min-width: 300px;">
                                            <li class="dropdown-header">Thông báo</li>
                                            <li><hr class="dropdown-divider"></li>
                                            @php
                                                $activities = $activities ?? [];
                                                $totalTasks = count($activities);
                                                $pendingTasks = ($statusCounts['planned'] ?? 0) + ($statusCounts['in_progress'] ?? 0);
                                                $completedTasks = $statusCounts['completed'] ?? 0;
                                            @endphp

                                            @if($pendingTasks > 0)
                                            <li>
                                                <div class="px-3 py-2">
                                                Bạn còn {{ $pendingTasks }} task cần thực hiện hôm nay
                                                </div>
                                            </li>
                                            @endif
                                            @if($completedTasks > 0)
                                            <li>
                                                <div class="px-3 py-2">
                                                Bạn đã hoàn thành {{ $completedTasks }} task hôm nay. Tuyệt vời!
                                                </div>
                                            </li>
                                            @endif
                                            @if(($studyHours * 60 + $studyRemain) >= 120)
                                            <li>
                                                <div class="px-3 py-2">
                                                Bạn đã học liên tục {{ $studyHours > 0 ? $studyHours . ' giờ ' : '' }}{{ $studyRemain > 0 ? $studyRemain . ' phút' : '' }}, nên nghỉ ngơi 15 phút nhé!
                                                </div>
                                            </li>
                                            @endif
                                            @if($totalTasks == 0)
                                            <li>
                                                <div class="px-3 py-2">
                                                Hôm nay bạn chưa có task nào, hãy thêm mục tiêu mới!
                                                </div>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>

                                    <div class="dropdown">
                                        <button class="btn d-flex align-items-center border-0 bg-transparent" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <div class="user-avatar rounded-circle bg-primary text-white fw-bold d-flex justify-content-center align-items-center me-2" style="width: 35px; height: 35px;">
                                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                            </div>
                                            <span class="me-1">{{ Auth::user()->name }}</span>
                                            <i class="fas fa-chevron-down text-muted"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                            <li><a class="dropdown-item" href="#">Cài đặt</a></li>
                                            <li><hr class="dropdown-divider"></li>
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
                                <button class="add-btn" data-bs-toggle="modal" data-bs-target="#eventModal">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
            
                        <div class="calendar-container">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="eventModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle me-2"></i>
                        Sự kiện mới
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="eventForm">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-edit me-2"></i>Tiêu đề
                            </label>
                            <input type="text" class="form-control" id="eventTitle" placeholder="Nhập tiêu đề sự kiện">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-calendar me-2"></i>Ngày
                                    </label>
                                    <input type="date" class="form-control" id="eventDate">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Bắt đầu</label>
                                    <input type="time" class="form-control" id="startTime">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Kết thúc</label>
                                    <input type="time" class="form-control" id="endTime">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-tags me-2"></i>Loại sự kiện
                            </label>
                            <select class="form-select" id="eventType">
                                <option value="study">Học tập</option>
                                <option value="english">Học tiếng Anh</option>
                                <option value="javascript">Học JavaScript</option>
                                <option value="webdev">Thiết kế Web</option>
                                <option value="exercise">Chạy bộ</option>
                                <option value="entertainment">Giải trí</option>
                                <option value="personal">Cá nhân</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-palette me-2"></i>Màu sắc
                            </label>
                            <div class="color-picker">
                                <div class="color-option blue selected" data-color="#3b82f6"></div>
                                <div class="color-option orange" data-color="#f59e0b"></div>
                                <div class="color-option green" data-color="#10b981"></div>
                                <div class="color-option purple" data-color="#8b5cf6"></div>
                                <div class="color-option pink" data-color="#ec4899"></div>
                                <div class="color-option red" data-color="#ef4444"></div>
                                <div class="color-option yellow" data-color="#eab308"></div>
                                <div class="color-option indigo" data-color="#6366f1"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-sticky-note me-2"></i>Ghi chú
                            </label>
                            <textarea class="form-control" id="eventNote" rows="3" placeholder="Thêm ghi chú..."></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="isRecurring">
                                <label class="form-check-label" for="isRecurring">
                                    <i class="fas fa-redo me-2"></i>Lặp lại
                                </label>
                            </div>
                        </div>
                        
                        <div id="recurringOptions" style="display: none;">
                            <div class="mb-3">
                                <select class="form-select" id="recurringType">
                                    <option value="daily">Hàng ngày</option>
                                    <option value="weekly">Hàng tuần</option>
                                    <option value="monthly">Hàng tháng</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="saveEvent">
                        <i class="fas fa-save me-2"></i>Lưu sự kiện
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/index.global.min.js"></script>
</body>
</html>
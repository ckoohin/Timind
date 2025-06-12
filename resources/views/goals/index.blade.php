<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Timind Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  @vite(['resources/css/components/goals.css', 'resources/js/app.js','resources/js/component/goals.js'])
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
                <a href="{{ route('dashboard') }}" class="nav-link active">
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
            <div class="header d-flex justify-content-between align-items-center px-4 py-3 shadow-sm bg-white">
                <h1 class="h4 mb-0">Mục tiêu học</h1>

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
                            $activities ??= collect();
                            $totalTasks = count($activities);
                            $pendingTasks = $statusCounts['planned'] + $statusCounts['in_progress'];
                            $completedTasks = $statusCounts['completed'];
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
                              Bạn đã học liên tục {{ ($studyHours > 0 ? "{$studyHours} giờ " : '') . ($studyRemain > 0 ? "{$studyRemain} phút" : '') }}, nên nghỉ ngơi 15 phút nhé!
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
            </div>
            
            <div class="main-content flex h-screen">
                <div class="container mx-auto px-4 max-w-4xl">
        
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card-container h-100 d-flex flex-column justify-content-center">
                                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">🎯 Thiết Lập Mục Tiêu Học Tập</h2>
                                <form id="goal-form">
                                    <div class="input">
                                        <div class="icon-input">
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <input type="text" id="title" name="title" class="input-field w-full" placeholder="Nhập tiêu đề mục tiêu..." value="{{ old('title', $goal->title ?? '') }}" required>
                                        </div>
                                        <div class="icon-input">
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            <input type="text" id="category" name="category" class="input-field w-full" placeholder="Nhập loại mục tiêu..." value="{{ old('category', $goal->category ?? '') }}" required>
                                        </div>
                                        <div class="icon-input">
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                            </svg>
                                            <input type="number" id="target_value" name="target_value" class="input-field w-full" placeholder="Giá trị mục tiêu..." value="{{ old('target_value', $goal->target_value ?? '') }}" required>
                                        </div>
                                        <div class="icon-input">
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                                            </svg>
                                            <input type="number" id="current_progress" name="current_progress" class="input-field w-full" placeholder="Tiến độ hiện tại..." value="{{ old('current_progress', $goal->current_progress ?? '') }}" required>
                                        </div>
                                    </div>
                                    <div class="date-range-row">
                                        <div class="date-group">
                                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Hạn hoàn thành</h4>
                                            <div class="flex space-x-2">
                                                <input type="date" id="deadline" name="deadline" class="date-input" value="{{ old('deadline', isset($goal->deadline) ? \Carbon\Carbon::parse($goal->deadline)->format('Y-m-d') : '') }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn-primary">
                                            ✨ Xem đề xuất
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div id="results-section" class="result-container w-100" style="display: none;">
                                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">📋 Kết Quả Đề Xuất</h2>
                                <div id="schedule-results" class="space-y-4">
                                    
                                </div>
                                <div class="text-center mt-6">
                                    <button id="save-schedule" class="btn-primary">
                                        📅 Chèn lịch
                                    </button>
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
</body>
</html>
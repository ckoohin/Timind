<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Timind Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  @vite(['resources/css/components/dashboard.css', 'resources/js/app.js','resources/js/dashboard.js'])
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
                <h1 class="h4 mb-0">Tổng quan</h1>

                @auth
                <div class="user-info d-flex align-items-center position-relative">
                    <i class="fas fa-bell notification-bell me-3 text-primary fs-5"></i>

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

            <div class="content-grid">
              <div class="schedule-card">
                <h3>Lịch hôm nay</h3>
                  @php
                    $iconList = [
                      'completed' => 'fas fa-check-circle status-completed',
                      'planned' => 'fas fa-clock status-pending',
                    ];
                  @endphp

                  @foreach ($activities as $activity)
                    @php
                      $start = new DateTime($activity->start_time);
                      $startTime = $start->format('H:i');
                      $end = new DateTime($activity->end_time);
                      $endTime = $end->format('H:i');
                      $icon = $iconList[$activity->status];
                    @endphp
                    <div class="schedule-item">
                      <div>
                          <div class="schedule-time">{{ $startTime }} - {{ $endTime }}</div>
                          <div class="schedule-subject">{{ $activity->description }}</div>
                      </div>
                      <i class="{{ $icon }}"></i>
                    </div>
                  @endforeach
              </div>
              
              <div class="stats-section">
                <div class="left-stats">
                  <div class="summary-card">
                    <div class="summary-item">
                      <div class="summary-icon completed"></div>
                      <span>5 đã xong</span>
                    </div>
                    <div class="summary-item">
                      <div class="summary-icon missed"></div>
                      <span>1 không hoàn thành</span>
                    </div>
                    <div class="summary-item">
                      <div class="summary-icon pending"></div>
                      <span>3 chưa thực hiện</span>
                    </div>
                  </div>
                  
                  <div class="notification-card">
                    <h4>Thông báo</h4>
                    <div class="notification-item">
                      Bạn còn 3 task cần thực hiện hôm nay
                    </div>
                    <div class="notification-item">
                      Bạn đã học liên tục 2 giờ, nên nghỉ 15 phút
                    </div>
                  </div>
                </div>
                
                <div class="stats-grid">
                  <div class="stat-card">
                    <i class="fas fa-book stat-icon primary"></i>
                    <div class="stat-title">Tổng thời gian học</div>
                    <div class="stat-value">{{ $todayActivities->sum('duration') }} phút</div>
                  </div>
                  
                  <div class="stat-card">
                    <i class="fas fa-bed stat-icon success"></i>
                    <div class="stat-title">Tổng thời gian ngủ</div>
                    <div class="stat-value">8 giờ</div>
                  </div>
                  
                  <div class="stat-card">
                    <i class="fas fa-gamepad stat-icon warning"></i>
                    <div class="stat-title">Tổng thời gian giải trí</div>
                    <div class="stat-value">1 tiếng 30 phút</div>
                  </div>
                  
                  <div class="stat-card">
                    <i class="fas fa-running stat-icon danger"></i>
                    <div class="stat-title">Tổng thời gian tập thể dục</div>
                    <div class="stat-value">0</div>
                  </div>
                  
                  <div class="advice-card">
                    <i class="fas fa-lightbulb" style="font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                    <div>
                        {{ $feedback ?? 'Không có nhận xét nào cho hôm nay!' }}
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
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timind Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/components/dashboard.css', 'resources/js/app.js', 'resources/js/dashboard.js'])
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
                        <div
                            class="header d-flex justify-content-between align-items-center px-4 py-3 shadow-sm bg-white">
                            <h1 class="h4 mb-0">Tổng quan</h1>

                            @auth
                                <div class="user-info d-flex align-items-center position-relative">
                                    <div class="dropdown me-3">
                                        <button class="btn border-0 bg-transparent p-0" id="notificationDropdown"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-bell notification-bell text-primary fs-5"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown"
                                            style="min-width: 300px;">
                                            <li class="dropdown-header">Thông báo</li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            @php
                                                $totalTasks = count($activities);
                                                $pendingTasks =$statusCounts['planned'] + $statusCounts['in_progress'];
                                                $completedTasks = $statusCounts['completed'];
                                            @endphp

                                            @if ($pendingTasks > 0)
                                                <li>
                                                    <div class="px-3 py-2">
                                                        Bạn còn {{ $pendingTasks }} task cần thực hiện hôm nay
                                                    </div>
                                                </li>
                                            @endif
                                            @if ($completedTasks > 0)
                                                <li>
                                                    <div class="px-3 py-2">
                                                        Bạn đã hoàn thành {{ $completedTasks }} task hôm nay. Tuyệt vời!
                                                    </div>
                                                </li>
                                            @endif
                                            @if ($studyHours * 60 + $studyRemain >= 120)
                                                <li>
                                                    <div class="px-3 py-2">
                                                        Bạn đã học liên tục
                                                        {{ $studyHours > 0 ? $studyHours . ' giờ ' : '' }}{{ $studyRemain > 0 ? $studyRemain . ' phút' : '' }},
                                                        nên nghỉ ngơi 15 phút nhé!
                                                    </div>
                                                </li>
                                            @endif
                                            @if ($totalTasks == 0)
                                                <li>
                                                    <div class="px-3 py-2">
                                                        Hôm nay bạn chưa có task nào, hãy thêm mục tiêu mới!
                                                    </div>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>

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

                        <div class="content-grid">
                            <div class="schedule-card">
                                <h3>Lịch hôm nay</h3>
                                @php
                                    $iconList = [
                                        'completed' => 'fas fa-check-circle status-completed',
                                        'in_progress' => 'fas fa-spinner status-in-progress',
                                        'planned' => 'fas fa-clock status-pending',
                                        'cancelled' => 'fas fa-times-circle status-missed',
                                    ];
                                    $today = \Carbon\Carbon::today();
                                @endphp

                                @foreach ($activities as $activity)
                                    @php
                                        $start = new DateTime($activity->start_time);
                                        $end = new DateTime($activity->end_time);
                                        $startTime = $start->format('H:i');
                                        $endTime = $end->format('H:i');
                                        $icon = $iconList[$activity->status];
                                        $activityDate = \Carbon\Carbon::parse($activity->start_time)->toDateString();
                                    @endphp
                                    @if ($activityDate == $today->toDateString())
                                        <div class="schedule-item">
                                            <div>
                                                <div class="schedule-time">{{ $startTime }} - {{ $endTime }}</div>
                                                <div class="schedule-subject">{{ $activity->description }}</div>
                                            </div>
                                            <i class="{{ $icon }}"></i>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <div class="stats-section">
                                <div class="left-stats">
                                    @php
                                        $statusCounts = [
                                            'completed' => 0,
                                            'in_progress' => 0,
                                            'cancelled' => 0,
                                            'planned' => 0,
                                        ];
                                        $today = \Carbon\Carbon::today()->toDateString();
                                        foreach ($activities as $activity) {
                                            $activityDate = \Carbon\Carbon::parse($activity->start_time)->toDateString();
                                            if ($activityDate == $today && isset($statusCounts[$activity->status])) {
                                                $statusCounts[$activity->status]++;
                                            }
                                        }
                                    @endphp
                                    <div class="summary-card">
                                        <div class="summary-item">
                                            <div class="summary-icon completed"></div>
                                            <span>{{ $statusCounts['completed'] }} đã xong</span>
                                        </div>
                                        <div class="summary-item">
                                            <div class="summary-icon in_progress"></div>
                                            <span>{{ $statusCounts['in_progress'] }} đang thực hiện</span>
                                        </div>
                                        <div class="summary-item">
                                            <div class="summary-icon missed"></div>
                                            <span>{{ $statusCounts['cancelled'] }} không hoàn thành</span>
                                        </div>
                                        <div class="summary-item">
                                            <div class="summary-icon pending"></div>
                                            <span>{{ $statusCounts['planned'] }} chưa thực hiện</span>
                                        </div>
                                    </div>

                                </div>

                                <div class="stats-grid">
                                    <div class="stat-card">
                                        <i class="fas fa-book stat-icon primary"></i>
                                        <div class="stat-title">Tổng thời gian học</div>
                                        <div class="stat-value">
                                            @if ($studyHours > 0)
                                                {{ $studyHours }} giờ
                                            @endif
                                            @if ($studyRemain > 0)
                                                {{ $studyRemain }} phút
                                            @endif
                                            @if ($studyHours == 0 && $studyRemain == 0)
                                                0 phút
                                            @endif
                                        </div>
                                    </div>

                                    <div class="stat-card">
                                        <i class="fas fa-bed stat-icon success"></i>
                                        <div class="stat-title">Tổng thời gian ngủ</div>
                                        <div class="stat-value">
                                            @if ($sleepHours > 0)
                                                {{ $sleepHours }} giờ
                                            @endif
                                            @if ($sleepRemain > 0)
                                                {{ $sleepRemain }} phút
                                            @endif
                                            @if ($sleepHours == 0 && $sleepRemain == 0)
                                                0 phút
                                            @endif
                                        </div>
                                    </div>

                                    <div class="stat-card">
                                        <i class="fas fa-gamepad stat-icon warning"></i>
                                        <div class="stat-title">Tổng thời gian giải trí</div>
                                        <div class="stat-value">
                                            @if ($entertainHours > 0)
                                                {{ $entertainHours }} giờ
                                            @endif
                                            @if ($entertainRemain > 0)
                                                {{ $entertainRemain }} phút
                                            @endif
                                            @if ($entertainHours == 0 && $entertainRemain == 0)
                                                0 phút
                                            @endif
                                        </div>
                                    </div>

                                    <div class="stat-card">
                                        <i class="fas fa-running stat-icon danger"></i>
                                        <div class="stat-title">Tổng thời gian tập thể dục</div>
                                        <div class="stat-value">
                                            @if ($exerciseHours > 0)
                                                {{ $exerciseHours }} giờ
                                            @endif
                                            @if ($exerciseRemain > 0)
                                                {{ $exerciseRemain }} phút
                                            @endif
                                            @if ($exerciseHours == 0 && $exerciseRemain == 0)
                                                0 phút
                                            @endif
                                        </div>
                                    </div>
                                    <div class="advice-card">
                                        <i class="fas fa-lightbulb"
                                            style="font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
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

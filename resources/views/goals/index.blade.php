<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Timind Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  @vite(['resources/css/components/goals.css', 'resources/js/app.js','resources/js/components/goals.js'])
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
                <a href="#" class="nav-link">
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
            
            <div class="ai-chat-container p-4 rounded shadow-sm bg-light my-4">
                <h2 class="mb-4 text-primary">Thiết Lập Mục Tiêu Học Tập</h2>
                <form onsubmit="event.preventDefault(); sendMessage();" class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label for="aim" class="form-label">Mục tiêu</label>
                        <input type="text" id="aim" class="form-control" placeholder="Nhập mục tiêu">
                    </div>
                    <div class="col-md-6">
                        <label for="promise" class="form-label">Mong muốn đạt được</label>
                        <input type="text" id="promise" class="form-control" placeholder="Nhập mong muốn đạt được">
                    </div>
                    <div class="col-md-6">
                        <label for="currentProcess" class="form-label">Tiến độ hiện tại</label>
                        <input type="text" id="currentProcess" class="form-control" placeholder="Nhập tiến độ hiện tại">
                    </div>
                    <div class="col-md-6">
                        <label for="duration" class="form-label">Thời gian thực hiện</label>
                        <input type="text" id="duration" class="form-control" placeholder="Nhập thời gian thực hiện">
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary px-4">Đề Xuất</button>
                    </div>
                </form>
                <div id="suggestion" class="mt-4" style="max-height: 200px; overflow-y: auto;"></div>
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
        async function sendMessage() {
            const aim = document.getElementById('aim').value;
            const promise = document.getElementById('promise').value;
            const currentProcess = document.getElementById('currentProcess').value;
            const duration = document.getElementById('duration').value;

            let promt = `Bạn hãy là một trợ lý quản lý thời gian. Hiên tại tôi có mục tiêu ${aim} và mong muốn ${promise}. Tình trạng của tôi hiện
            là ${currentProcess}. Tôi mong muốn hoàn thành trong ${duration}. một cách ngắn gọn dễ hiểu nhất, trả về dạng thẻ HTML. có phân cách từng bước, 
            thẻ kích thước lớn nhất là h6, cấu trúc như sau đầu tiên là mục tiêu sau đó bên dưới là từng bước thực hiện, mỗi bước là một thẻ h6.`;

            const res = await fetch('/goals', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    'message': promt
                })
            });
            const data = await res.json();
            console.log(data);
            document.getElementById('suggestion').innerHTML += `${data.message} <hr>`;
        }

        document.querySelector('form').addEventListener('submit', function(event) {
            event.preventDefault();
            sendMessage();
        });
    </script>

</body>
</html>
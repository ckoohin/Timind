<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Timind Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/components/goals.css', 'resources/js/app.js', 'resources/js/components/goals.js'])
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
                                <a href="{{ route('goals.index') }}" class="nav-link active">
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
                            <h1 class="h4 mb-0">Mục tiêu học</h1>

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
                                                $activities ??= collect();
                                                $totalTasks = count($activities);
                                                $pendingTasks = $statusCounts['planned'] + $statusCounts['in_progress'];
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
                                                        {{ ($studyHours > 0 ? "{$studyHours} giờ " : '') . ($studyRemain > 0 ? "{$studyRemain} phút" : '') }},
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

                        <div class="ai-chat-container p-4 rounded shadow-sm bg-light my-4">
                            <h2 class="mb-4 text-primary">Thiết Lập Mục Tiêu Học Tập</h2>
                            <form id="goalForm" class="row g-3 align-items-end">
                                <div class="col-md-6">
                                    <label for="aim" class="form-label">Mục tiêu <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="aim" class="form-control"
                                        placeholder="Ví dụ: Học lập trình Python" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="promise" class="form-label">Mong muốn đạt được <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="promise" class="form-control"
                                        placeholder="Ví dụ: Có thể làm web app cơ bản" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="currentProcess" class="form-label">Tiến độ hiện tại <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="currentProcess" class="form-control"
                                        placeholder="Ví dụ: Mới bắt đầu, chưa biết gì" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="duration" class="form-label">Thời gian thực hiện <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="duration" class="form-control"
                                        placeholder="Ví dụ: 3 tháng" required>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-lightbulb me-2"></i>Tạo Lộ Trình
                                    </button>
                                </div>
                            </form>

                            <!-- Loading spinner -->
                            <div id="loadingSpinner" class="text-center mt-4 d-none">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Đang tạo lộ trình...</span>
                                </div>
                                <p class="mt-2">AI đang tạo lộ trình cho bạn...</p>
                            </div>

                            <!-- Suggestion result -->
                            <div id="suggestionContainer" class="mt-4 d-none">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0"><i class="fas fa-route me-2"></i>Lộ Trình Học Tập Được Đề
                                            Xuất</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="suggestion" style="max-height: 400px; overflow-y: auto;"></div>
                                        <div class="mt-3 text-end">
                                            <button type="button" class="btn btn-success" id="insertScheduleBtn">
                                                <i class="fas fa-calendar-plus me-2"></i>Chèn Vào Lịch Học
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Schedule result -->
                            <div id="scheduleContainer" class="mt-4 d-none">
                                <div class="card">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Lịch Học Được Tạo
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="scheduleResult" style="max-height: 400px; overflow-y: auto;"></div>
                                        <div class="mt-3 text-end">
                                            <button type="button" class="btn btn-primary" id="saveScheduleBtn">
                                                <i class="fas fa-save me-2"></i>Lưu Lịch Học
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
        let currentRoadmap = null;
        let currentSchedule = null;
        function createDetailedPrompt(formData) {
            return `Bạn là một chuyên gia tư vấn học tập chuyên nghiệp. Hãy tạo lộ trình học tập chi tiết cho sinh viên với thông tin sau:
              **THÔNG TIN SINH VIÊN:**
              - Mục tiêu: ${formData.aim}
              - Mong muốn đạt được: ${formData.promise}
              - Trình độ hiện tại: ${formData.currentProcess}
              - Thời gian thực hiện: ${formData.duration}

              **YÊU CẦU ĐỊNH DẠNG:**
              1. Trả về HTML với các thẻ tối đa là h6
              2. Cấu trúc rõ ràng với các phần:
                - <h5>MỤC TIÊU TỔNG QUÁT</h5>
                - <h5>KẾ HOẠCH CHI TIẾT</h5>
                - <h5>LỊCH TRÌNH THEO TUẦN</h5>
                - <h5>TÀI LIỆU THAM KHẢO</h5>
                - <h5>PHƯƠNG PHÁP ĐÁNH GIÁ</h5>

              **YÊU CẦU NỘI DUNG:**
              - Chia nhỏ thành các milestone cụ thể
              - Mỗi giai đoạn có mục tiêu rõ ràng, thời gian và kiến thức cần đạt
              - Đưa ra các bài tập thực hành concrete
              - Gợi ý tài liệu, khóa học, công cụ cần thiết
              - Phương pháp kiểm tra tiến độ

              Hãy tạo lộ trình chi tiết, thực tế và có thể thực hiện được.`;
        }
        function createSchedulePrompt(roadmapData, freeTime) {
            return `Bạn là chuyên gia lập lịch học tập. Dựa trên lộ trình học đã được tạo và thời gian rảnh, hãy tạo lịch học cụ thể:
              **LỘ TRÌNH ĐÃ TẠO:**
              ${roadmapData}
              **THỜI GIAN RẢNH:**
              ${JSON.stringify(freeTime)}
              **YÊU CẦU QUAN TRỌNG:**
              1. Tạo lịch học từ 7h đến 22h
              2. Mỗi buổi học từ 1-3 tiếng
              3. Có ngày cụ thể (16/06/2025, 17/06/2025, v.v.)
              4. Phân bổ hợp lý theo độ khó
              5. Xen kẽ lý thuyết và thực hành
              6. Có thời gian nghỉ ngơi

              **ĐỊNH DẠNG TRẢ VỀ BẮT BUỘC:**
              - Sử dụng HTML với thẻ tối đa h6
              - Cấu trúc chính xác: <h5>LỊCH HỌC TUẦN [số]</h5>
              - Mỗi ngày PHẢI theo format: <h6>Thứ [X] - [giờ bắt đầu]:[phút]-[giờ kết thúc]:[phút]</h6>
              - Nội dung: <p><strong>Chủ đề:</strong> [tên chủ đề] <br><strong>Mục tiêu:</strong> [mục tiêu cụ thể]</p>
              **VÍ DỤ FORMAT:**
              <h5>LỊCH HỌC TUẦN 1</h5>
              <h6>16/06/2025 - 8:00-10:00</h6>
              <p><strong>Chủ đề:</strong> Giới thiệu Python cơ bản <br><strong>Mục tiêu:</strong> Hiểu syntax và cài đặt môi trường</p>
              
              <h6>18/06/2025 - 14:00-16:00</h6>
              <p><strong>Chủ đề:</strong> Biến và kiểu dữ liệu <br><strong>Mục tiêu:</strong> Thực hành với các kiểu dữ liệu cơ bản</p>

              Hãy tạo lịch học chi tiết theo ĐÚNG format trên.`;
        }

        async function sendMessage(prompt) {
            try {
                const response = await fetch('/goals', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        'message': prompt
                    })
                });
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || 'Network response was not ok');
                }
                const data = await response.json();
                return data.message;
            } catch (error) {
                console.error('Error:', error);
                throw error;
            }
        }

        async function getFreeTime() {
            try {
                const response = await fetch('/api/free-times', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });
                if (!response.ok) {
                    throw new Error('Failed to fetch free time');
                }
                return await response.json();
            } catch (error) {
                console.error('Error fetching free time:', error);
                return {};
            }
        }
        async function parseAndSaveSchedule(aiResponse) {
            try {
                const response = await fetch('/goals/parse-and-save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        ai_response: aiResponse
                    })
                });
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.error || 'Failed to save schedule');
                }

                return data;
            } catch (error) {
                console.error('Error saving schedule:', error);
                throw error;
            }
        }

        function showNotification(message, type = 'success') {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show mt-3" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            const container = document.querySelector('.ai-chat-container');
            const existingAlert = container.querySelector('.alert');
            if (existingAlert) {
                existingAlert.remove();
            }
            container.insertAdjacentHTML('afterbegin', alertHtml);
            setTimeout(() => {
                const alert = container.querySelector('.alert');
                if (alert) {
                    alert.remove();
                }
            }, 5000);
        }

        // Form submission handler
        document.getElementById('goalForm').addEventListener('submit', async function(event) {
            event.preventDefault();

            const formData = {
                aim: document.getElementById('aim').value.trim(),
                promise: document.getElementById('promise').value.trim(),
                currentProcess: document.getElementById('currentProcess').value.trim(),
                duration: document.getElementById('duration').value.trim()
            };

            if (!formData.aim || !formData.promise || !formData.currentProcess || !formData.duration) {
                showNotification('Vui lòng điền đầy đủ các trường bắt buộc!', 'error');
                return;
            }

            document.getElementById('loadingSpinner').classList.remove('d-none');
            document.getElementById('suggestionContainer').classList.add('d-none');
            document.getElementById('scheduleContainer').classList.add('d-none');

            try {
                const prompt = createDetailedPrompt(formData);
                const result = await sendMessage(prompt);

                currentRoadmap = result;

                document.getElementById('loadingSpinner').classList.add('d-none');
                document.getElementById('suggestion').innerHTML = result;
                document.getElementById('suggestionContainer').classList.remove('d-none');

                showNotification('Lộ trình học tập đã được tạo thành công!');

            } catch (error) {
                document.getElementById('loadingSpinner').classList.add('d-none');
                showNotification('Có lỗi xảy ra khi tạo lộ trình. Vui lòng thử lại!', 'error');
                console.error('Error:', error);
            }
        });
        // Insert schedule button handler
        document.getElementById('insertScheduleBtn').addEventListener('click', async function() {
            if (!currentRoadmap) {
                showNotification('Không có lộ trình để tạo lịch!', 'error');
                return;
            }
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang tạo lịch...';
            try {
                const freeTime = await getFreeTime();
                const schedulePrompt = createSchedulePrompt(currentRoadmap, freeTime);
                const scheduleResult = await sendMessage(schedulePrompt);
                currentSchedule = scheduleResult;
                document.getElementById('scheduleResult').innerHTML = scheduleResult;
                document.getElementById('scheduleContainer').classList.remove('d-none');
                showNotification('Lịch học đã được tạo thành công!');

            } catch (error) {
                showNotification('Có lỗi xảy ra khi tạo lịch học. Vui lòng thử lại!', 'error');
                console.error('Error:', error);
            } finally {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-calendar-plus me-2"></i>Chèn Vào Lịch Học';
            }
        });
        // Save schedule button handler
        document.getElementById('saveScheduleBtn').addEventListener('click', async function() {
            if (!currentSchedule) {
                showNotification('Không có lịch học để lưu!', 'error');
                return;
            }
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang lưu...';

            try {
                const result = await parseAndSaveSchedule(currentSchedule);

                if (result.success) {
                    showNotification(`Lịch học đã được lưu thành công! Đã tạo ${result.activities} hoạt động.`);

                    // Reset form and hide containers
                    document.getElementById('goalForm').reset();
                    document.getElementById('suggestionContainer').classList.add('d-none');
                    document.getElementById('scheduleContainer').classList.add('d-none');
                    currentRoadmap = null;
                    currentSchedule = null;

                    // Optionally redirect to schedule page after a delay
                    setTimeout(() => {
                        if (confirm('Bạn có muốn xem lịch học vừa tạo không?')) {
                            window.location.href = '/activities';
                        }
                    }, 2000);
                } else {
                    throw new Error(result.error || 'Unknown error occurred');
                }

            } catch (error) {
                let errorMessage = 'Có lỗi xảy ra khi lưu lịch học.';
                if (error.message.includes('validation')) {
                    errorMessage += ' Dữ liệu không hợp lệ.';
                } else if (error.message.includes('conflict')) {
                    errorMessage += ' Có xung đột thời gian với lịch hiện tại.';
                }
                showNotification(errorMessage, 'error');
                console.error('Error:', error);
            } finally {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-save me-2"></i>Lưu Lịch Học';
            }
        });

        function debugScheduleData() {
            if (currentSchedule) {
                console.log('Current Schedule HTML:', currentSchedule);
                const testDiv = document.createElement('div');
                testDiv.innerHTML = currentSchedule;
                const h6Elements = testDiv.querySelectorAll('h6');
                const pElements = testDiv.querySelectorAll('p');
                console.log('Found H6 elements:', h6Elements.length);
                console.log('Found P elements:', pElements.length);
                h6Elements.forEach((h6, index) => {
                    console.log(`H6 ${index}:`, h6.textContent);
                });
                pElements.forEach((p, index) => {
                    console.log(`P ${index}:`, p.innerHTML);
                });
            }
        }
    </script>
</body>
</html>
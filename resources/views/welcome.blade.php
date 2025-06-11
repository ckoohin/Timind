<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timind - Quản Lý Thời Gian Thông Minh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/components/welcome.css', 'resources/js/components/welcome.js'])
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">
                Timind <span class="brand-accent">CodeX</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Tính năng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">Giới thiệu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Liên hệ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn-primary-custom ms-3" href="{{ route('showLoginForm') }}">Đăng nhập</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-animation">
            <div class="floating-card">
                <i class="fas fa-calendar-check text-white fs-4 mb-2"></i>
                <div class="text-white">Lịch học hôm nay</div>
                <small class="text-white-50">8 tiết đã hoàn thành</small>
            </div>
            <div class="floating-card">
                <i class="fas fa-clock text-white fs-4 mb-2"></i>
                <div class="text-white">Thời gian học</div>
                <small class="text-white-50">7 giờ 20 phút</small>
            </div>
            <div class="floating-card">
                <i class="fas fa-target text-white fs-4 mb-2"></i>
                <div class="text-white">Mục tiêu</div>
                <small class="text-white-50">85% hoàn thành</small>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="hero-content">
                        <h1 class="hero-title">
                            Quản Lý Thời Gian<br>
                            <span
                                style="background: linear-gradient(45deg, #ff6b6b, #feca57); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Thông
                                Minh</span>
                        </h1>
                        <p class="hero-subtitle">
                            Tối ưu hóa lịch học, theo dõi tiến độ và đạt được mục tiêu học tập với sự hỗ trợ của AI
                        </p>
                        <div class="hero-buttons">
                            <a href="{{ route('showRegisterForm') }}" class="btn-hero btn-hero-primary">
                                <i class="fas fa-rocket me-2"></i>
                                Bắt đầu ngay
                            </a>
                            <a href="#features" class="btn-hero btn-hero-secondary">
                                <i class="fas fa-play me-2"></i>
                                Xem demo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="features">
        <div class="container">
            <h2 class="section-title fade-in">Tính Năng Nổi Bật</h2>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card fade-in">
                        <div class="feature-icon gradient-1">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h3 class="feature-title">Lịch Học Thông Minh</h3>
                        <p class="feature-description">
                            Tự động sắp xếp lịch học tối ưu dựa trên mức độ ưu tiên và thời gian rảnh của bạn
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card fade-in">
                        <div class="feature-icon gradient-2">
                            <i class="fas fa-brain"></i>
                        </div>
                        <h3 class="feature-title">AI Cá Nhân Hóa</h3>
                        <p class="feature-description">
                            Nhận gợi ý học tập thông minh và phân tích hiệu suất dựa trên thói quen của bạn
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card fade-in">
                        <div class="feature-icon gradient-3">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="feature-title">Phân Tích Chi Tiết</h3>
                        <p class="feature-description">
                            Theo dõi tiến độ học tập với biểu đồ trực quan và báo cáo hiệu suất chi tiết
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="stats">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item fade-in">
                        <span class="stat-number" data-count="10000">0</span>
                        <span class="stat-label">Người dùng</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item fade-in">
                        <span class="stat-number" data-count="50000">0</span>
                        <span class="stat-label">Giờ học được quản lý</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item fade-in">
                        <span class="stat-number" data-count="95">0</span>
                        <span class="stat-label">% Hài lòng</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item fade-in">
                        <span class="stat-number" data-count="24">0</span>
                        <span class="stat-label">Hỗ trợ 24/7</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="container">
            <div class="fade-in">
                <h2 class="cta-title">Sẵn sàng tối ưu thời gian học?</h2>
                <p class="cta-subtitle">
                    Tham gia cùng hàng nghìn học sinh đã cải thiện hiệu suất học tập với Timind
                </p>
                <a href="/register" class="btn-hero btn-hero-primary">
                    <i class="fas fa-user-plus me-2"></i>
                    Đăng ký miễn phí
                </a>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="footer-brand">Timind CodeX</div>
                    <p class="footer-description">
                        Nền tảng quản lý thời gian học tập thông minh được phát triển bởi sinh viên, dành cho sinh viên.
                    </p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Sản phẩm</h5>
                    <ul class="footer-links">
                        <li><a href="#">Tính năng</a></li>
                        <li><a href="#">Giá cả</a></li>
                        <li><a href="#">API</a></li>
                        <li><a href="#">Tải app</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Hỗ trợ</h5>
                    <ul class="footer-links">
                        <li><a href="#">Trung tâm trợ giúp</a></li>
                        <li><a href="#">Liên hệ</a></li>
                        <li><a href="#">Báo lỗi</a></li>
                        <li><a href="#">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Công ty</h5>
                    <ul class="footer-links">
                        <li><a href="#">Về chúng tôi</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Tuyển dụng</a></li>
                        <li><a href="#">Tin tức</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Pháp lý</h5>
                    <ul class="footer-links">
                        <li><a href="#">Điều khoản</a></li>
                        <li><a href="#">Bảo mật</a></li>
                        <li><a href="#">Cookie</a></li>
                        <li><a href="#">Bản quyền</a></li>
                    </ul>
                </div>
            </div>
            <hr style="border-color: rgba(255,255,255,0.1); margin: 40px 0 20px;">
            <div class="row">
                <div class="col-12 text-center">
                    <p style="opacity: 0.7; margin: 0;">
                        © 2024 Timind CodeX. Tất cả quyền được bảo lưu.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

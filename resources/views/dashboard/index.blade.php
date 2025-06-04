@extends('layouts.app')

@section('title', 'Dashboard - Timind')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Dashboard</h1>
                <div class="text-muted">
                    <i class="fas fa-calendar me-1"></i>
                    {{ now()->format('d/m/Y') }}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Stats Cards -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-book-open fa-2x text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-muted">Hôm nay</div>
                            <div class="h5 mb-0">{{ $todayActivities->count() }} hoạt động</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock fa-2x text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-muted">Thời gian học</div>
                            <div class="h5 mb-0">{{ $todayActivities->sum('duration') }} phút</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-target fa-2x text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-muted">Mục tiêu</div>
                            <div class="h5 mb-0">3 đang thực hiện</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-chart-line fa-2x text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-muted">Hiệu suất</div>
                            <div class="h5 mb-0">85%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Upcoming Activities -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Hoạt động sắp tới
                    </h5>
                </div>
                <div class="card-body">
                    @if($upcomingActivities->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($upcomingActivities as $activity)
                                <div class="list-group-item d-flex align-items-center px-0">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-sm rounded-circle" 
                                             style="background-color: {{ $activity->category->color_code }}20;">
                                            <i class="fas fa-{{ $activity->category->icon ?? 'calendar' }}" 
                                               style="color: {{ $activity->category->color_code }};"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="fw-semibold">{{ $activity->title }}</div>
                                        <div class="small text-muted">
                                            {{ $activity->start_time->format('d/m H:i') }} - 
                                            {{ $activity->end_time->format('H:i') }}
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-primary">{{ $activity->category->name }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Chưa có hoạt động nào được lên lịch</p>
                            <a href="{{ route('activities.create') }}" class="btn btn-primary">
                                Thêm hoạt động mới
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- AI Suggestions -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-robot me-2"></i>
                        Gợi ý từ AI
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-lightbulb me-2"></i>
                        Bạn đã học được 4 giờ hôm nay. Hãy nghỉ ngơi 15 phút để tối ưu hiệu suất!
                    </div>
                    
                    <div class="alert alert-success">
                        <i class="fas fa-trophy me-2"></i>
                        Tuyệt vời! Bạn đã hoàn thành 80% mục tiêu tuần này.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
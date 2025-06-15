<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    public function index()
    {
        $days = [];
        $studyHours = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('d/m');
            
            // Lấy dữ liệu thực từ database
            $studyHours[] = $this->getStudyHoursForDate($date);
        }
        
        // Tính toán thống kê
        $totalHours = array_sum($studyHours);
        $averageHours = $totalHours > 0 ? round($totalHours / 7, 1) : 0;
        $maxHours = max($studyHours);
        $studyDays = count(array_filter($studyHours, function($hours) {
            return $hours > 0;
        }));
        
        // Lấy dữ liệu tháng này
        $currentMonth = Carbon::now()->format('m/Y');
        $monthlyTotal = $this->getMonthlyStudyHours();
        
        return view('analytics.index', compact(
            'days', 
            'studyHours', 
            'totalHours', 
            'averageHours', 
            'maxHours', 
            'studyDays',
            'currentMonth',
            'monthlyTotal'
        ));
    }
    
    /**
     * Lấy tổng số giờ học trong một ngày cụ thể
     */
    private function getStudyHoursForDate($date)
    {
        $totalMinutes = Activity::where('user_id', Auth::id())
            ->whereDate('start_time', $date->format('Y-m-d'))
            ->where('status', 'completed') // Chỉ tính các hoạt động đã hoàn thành
            ->get()
            ->sum(function($activity) {
                // Tính thời gian thực tế (phút) và chuyển sang giờ
                if ($activity->end_time && $activity->start_time) {
                    return $activity->start_time->diffInMinutes($activity->end_time);
                }
                return 0;
            });
        
        // Chuyển từ phút sang giờ, làm tròn 1 chữ số thập phân
        return round($totalMinutes / 60, 1);
    }
    
    /**
     * Lấy tổng số giờ học trong tháng hiện tại
     */
    private function getMonthlyStudyHours()
    {
        $totalMinutes = Activity::where('user_id', Auth::id())
            ->whereMonth('start_time', Carbon::now()->month)
            ->whereYear('start_time', Carbon::now()->year)
            ->where('status', 'completed')
            ->get()
            ->sum(function($activity) {
                if ($activity->end_time && $activity->start_time) {
                    return $activity->start_time->diffInMinutes($activity->end_time);
                }
                return 0;
            });
        
        return round($totalMinutes / 60, 1);
    }
    
    /**
     * API endpoint để lấy dữ liệu analytics theo khoảng thời gian
     */
    public function getAnalyticsData(Request $request)
    {
        $period = $request->get('period', '7days');
        
        switch ($period) {
            case '30days':
                return $this->get30DaysData();
            case '3months':
                return $this->get3MonthsData();
            default:
                return $this->get7DaysData();
        }
    }

    /**
     * Lấy dữ liệu 7 ngày gần nhất
     */
    private function get7DaysData()
    {
        $days = [];
        $studyHours = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('d/m');
            $studyHours[] = $this->getStudyHoursForDate($date);
        }
        
        return response()->json([
            'labels' => $days,
            'data' => $studyHours
        ]);
    }

    /**
     * Lấy dữ liệu 30 ngày gần nhất
     */
    private function get30DaysData()
    {
        $days = [];
        $studyHours = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('d/m');
            $studyHours[] = $this->getStudyHoursForDate($date);
        }
        
        return response()->json([
            'labels' => $days,
            'data' => $studyHours
        ]);
    }

    /**
     * Lấy dữ liệu 3 tháng gần nhất
     */
    private function get3MonthsData()
    {
        $days = [];
        $studyHours = [];
        $startDate = Carbon::now()->subMonths(3)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        $period = $endDate->diffInDays($startDate);

        for ($i = $period; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('d/m');
            $studyHours[] = $this->getStudyHoursForDate($date);
        }

        return response()->json([
            'labels' => $days,
            'data' => $studyHours
        ]);
    }

    /**
     * API endpoint để lấy dữ liệu theo tháng cụ thể
     */
    public function getMonthlyData(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);
        
        $monthlyData = $this->getMonthlyStudyHoursByMonth($month, $year);
        
        return response()->json($monthlyData);
    }

    /**
     * Lấy dữ liệu từng ngày trong tháng cụ thể
     */
    private function getMonthlyStudyHoursByMonth($month, $year)
    {
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $days = [];
        $studyHours = [];

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = Carbon::createFromDate($year, $month, $i);
            $days[] = $date->format('d/m');
            $studyHours[] = $this->getStudyHoursForDate($date);
        }

        return [
            'labels' => $days,
            'data' => $studyHours
        ];
    }

    /**
     * Lấy thống kê tổng quan
     */
    public function getOverviewStats()
    {
        $userId = Auth::id();
        $now = Carbon::now();
        
        // Thống kê tuần này
        $weekStart = $now->copy()->startOfWeek();
        $weekEnd = $now->copy()->endOfWeek();
        
        $weeklyStats = $this->getStatsForPeriod($weekStart, $weekEnd);
        
        // Thống kê tháng này
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();
        
        $monthlyStats = $this->getStatsForPeriod($monthStart, $monthEnd);
        
        // Thống kê năm này
        $yearStart = $now->copy()->startOfYear();
        $yearEnd = $now->copy()->endOfYear();
        
        $yearlyStats = $this->getStatsForPeriod($yearStart, $yearEnd);
        
        return response()->json([
            'weekly' => $weeklyStats,
            'monthly' => $monthlyStats,
            'yearly' => $yearlyStats
        ]);
    }
    
    /**
     * Lấy thống kê cho khoảng thời gian cụ thể
     */
    private function getStatsForPeriod($startDate, $endDate)
    {
        $activities = Activity::where('user_id', Auth::id())
            ->whereBetween('start_time', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();
        
        $totalMinutes = $activities->sum(function($activity) {
            if ($activity->end_time && $activity->start_time) {
                return $activity->start_time->diffInMinutes($activity->end_time);
            }
            return 0;
        });
        
        $totalHours = round($totalMinutes / 60, 1);
        $totalDays = $startDate->diffInDays($endDate) + 1;
        $studyDays = $activities->groupBy(function($activity) {
            return $activity->start_time->format('Y-m-d');
        })->count();
        
        return [
            'total_hours' => $totalHours,
            'average_hours' => $totalDays > 0 ? round($totalHours / $totalDays, 1) : 0,
            'study_days' => $studyDays,
            'total_days' => $totalDays,
            'total_activities' => $activities->count()
        ];
    }
    
    /**
     * Lấy thống kê theo danh mục
     */
    public function getCategoryStats(Request $request)
    {
        $period = $request->get('period', '30days');
        $startDate = $this->getStartDateByPeriod($period);
        
        $categoryStats = Activity::join('activity_categories', 'activities.category_id', '=', 'activity_categories.id')
            ->where('activities.user_id', Auth::id())
            ->where('activities.start_time', '>=', $startDate)
            ->where('activities.status', 'completed')
            ->select('activity_categories.name', 'activity_categories.color')
            ->selectRaw('SUM(TIMESTAMPDIFF(MINUTE, activities.start_time, activities.end_time)) as total_minutes')
            ->groupBy('activity_categories.id', 'activity_categories.name', 'activity_categories.color')
            ->orderBy('total_minutes', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->name,
                    'color' => $item->color,
                    'hours' => round($item->total_minutes / 60, 1),
                    'minutes' => $item->total_minutes
                ];
            });
        
        return response()->json($categoryStats);
    }
    
    /**
     * Helper method để lấy ngày bắt đầu theo period
     */
    private function getStartDateByPeriod($period)
    {
        switch ($period) {
            case '7days':
                return Carbon::now()->subDays(7);
            case '30days':
                return Carbon::now()->subDays(30);
            case '3months':
                return Carbon::now()->subMonths(3);
            case '6months':
                return Carbon::now()->subMonths(6);
            case '1year':
                return Carbon::now()->subYear();
            default:
                return Carbon::now()->subDays(30);
        }
    }
}
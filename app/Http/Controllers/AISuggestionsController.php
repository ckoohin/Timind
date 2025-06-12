<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenRouterService;
use App\Models\Activity;
use App\Models\GoalTask;
use Carbon\Carbon;

class AISuggestionsController extends Controller
{
    private $openRouterService;
    
    public function __construct(OpenRouterService $openRouterService)
    {
        $this->openRouterService = $openRouterService;
    }
    
    public function getScheduleSuggestions(Request $request)
    {
        try {
            $date = $request->input('date', Carbon::today()->format('Y-m-d'));
            $userId = auth()->id();
            
            $existingActivities = Activity::where('user_id', $userId)
                ->whereDate('start_time', $date)
                ->orderBy('start_time')
                ->get();
            
            $suggestions = $this->openRouterService->generateScheduleSuggestions(
                $existingActivities
            );
            
            return response()->json([
                'success' => true,
                'data' => [
                    'date' => $date,
                    'existing_activities' => $existingActivities->count(),
                    'suggestions' => $suggestions
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tạo gợi ý lịch: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getGoalsAnalysis(Request $request)
    {
        try {
            $userId = auth()->id();
            $timeRange = $request->input('time_range', '7');
            
            $startDate = Carbon::now()->subDays($timeRange);
            $activities = Activity::where('user_id', $userId)
                ->where('start_time', '>=', $startDate)
                ->get();
            
            $goals = GoalTask::where('user_id', $userId)
                ->where('status', 'active')
                ->get()
                ->map(function($goal) {
                    return [
                        'title' => $goal->title,
                        'category' => $goal->category,
                        'target_value' => $goal->target_value,
                        'current_progress' => $goal->current_progress,
                        'deadline' => $goal->deadline
                    ];
                })
                ->toArray();
            
            $analysis = $this->openRouterService->analyzeActivitiesForGoals($activities, $goals);
            
            $stats = $this->calculateActivityStats($activities);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'analysis' => $analysis,
                    'stats' => $stats,
                    'period' => [
                        'from' => $startDate->format('Y-m-d'),
                        'to' => Carbon::now()->format('Y-m-d'),
                        'days' => $timeRange
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể phân tích dữ liệu: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function createActivityFromSuggestion(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'suggested_time' => 'required|string',
            'duration' => 'required|integer|min:1',
            'category' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'date' => 'required|date'
        ]);
        
        try {
            $userId = auth()->id();
            
            $startDateTime = Carbon::parse($request->date . ' ' . $request->suggested_time);
            $endDateTime = $startDateTime->copy()->addMinutes($request->duration);
            
            $conflict = Activity::where('user_id', $userId)
                ->where(function($query) use ($startDateTime, $endDateTime) {
                    $query->whereBetween('start_time', [$startDateTime, $endDateTime])
                          ->orWhereBetween('end_time', [$startDateTime, $endDateTime])
                          ->orWhere(function($q) use ($startDateTime, $endDateTime) {
                              $q->where('start_time', '<=', $startDateTime)
                                ->where('end_time', '>=', $endDateTime);
                          });
                })
                ->first();
            
            if ($conflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thời gian này đã có hoạt động khác'
                ], 409);
            }
            
            $activity = Activity::create([
                'user_id' => $userId,
                'title' => $request->title,
                'description' => 'Được tạo từ gợi ý AI',
                'start_time' => $startDateTime,
                'end_time' => $endDateTime,
                'category' => $request->category,
                'priority' => $request->priority,
                'status' => 'planned'
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $activity,
                'message' => 'Đã thêm hoạt động vào lịch'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tạo hoạt động: ' . $e->getMessage()
            ], 500);
        }
    }
    
    
    private function calculateActivityStats($activities)
    {
        $totalActivities = $activities->count();
        $completedActivities = $activities->where('status', 'completed')->count();
        $totalTime = 0;
        $categoryStats = [];
        
        foreach ($activities as $activity) {
            $duration = Carbon::parse($activity->start_time)
                ->diffInMinutes(Carbon::parse($activity->end_time));
            $totalTime += $duration;
            
            $category = $activity->category ?? 'other';
            if (!isset($categoryStats[$category])) {
                $categoryStats[$category] = ['count' => 0, 'time' => 0];
            }
            $categoryStats[$category]['count']++;
            $categoryStats[$category]['time'] += $duration;
        }
        
        return [
            'total_activities' => $totalActivities,
            'completed_activities' => $completedActivities,
            'completion_rate' => $totalActivities > 0 ? round(($completedActivities / $totalActivities) * 100, 1) : 0,
            'total_time_minutes' => $totalTime,
            'total_time_hours' => round($totalTime / 60, 1),
            'average_activity_duration' => $totalActivities > 0 ? round($totalTime / $totalActivities, 1) : 0,
            'category_breakdown' => $categoryStats
        ];
    }
}
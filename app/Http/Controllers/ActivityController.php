<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Activity;

class ActivityController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();
        
        $todayActivities = Activity::where('user_id', Auth::id())
            ->whereDate('start_time', Carbon::today())
            ->orderBy('start_time')
            ->get();
        $weekStart = $today->copy()->startOfWeek();
        $weekEnd = $today->copy()->endOfWeek();

        $weekActivities = Activity::where('user_id', Auth::id())
            ->whereDate('start_time', '>=', $weekStart)
            ->whereDate('start_time', '<=', $weekEnd)
            ->orderBy('start_time')
            ->get();

        $stats = [
            'completed' => $todayActivities->where('status', 'completed')->count(),
            'pending' => $todayActivities->where('status', 'pending')->count(),
            'missed' => $todayActivities->where('status', 'missed')->count(),
            'total_study_time' => $todayActivities->where('category', 'study')->sum('duration'),
            'total_exercise_time' => $todayActivities->where('category', 'exercise')->sum('duration'),
        ];
        $statusCounts = [
            'planned' => $todayActivities->where('status', 'planned')->count(),
            'in_progress' => $todayActivities->where('status', 'in_progress')->count(),
            'completed' => $todayActivities->where('status', 'completed')->count(),
        ];
        $totalStudyMinutes = $todayActivities->filter(function ($activity) {
            return $activity->category && $activity->category->type === 'study';
        })->sum('duration');

        $totalExerciseMinutes = $todayActivities->filter(function ($activity) {
            return $activity->category && $activity->category->type === 'exercise';
        })->sum('duration');

        $totalEntertainmentMinutes = $todayActivities->filter(function ($activity) {
            return $activity->category && $activity->category->type === 'entertainment';
        })->sum('duration');

        $totalSleepMinutes = $todayActivities->filter(function ($activity) {
            return $activity->category && $activity->category->type === 'sleep';
        })->sum(callback: 'duration');

        $studyHours = intdiv($totalStudyMinutes, 60);
        $studyRemain = $totalStudyMinutes % 60;

        $exerciseHours = intdiv($totalExerciseMinutes, 60);
        $exerciseRemain = $totalExerciseMinutes % 60;

        $entertainHours = intdiv($totalEntertainmentMinutes, 60);
        $entertainRemain = $totalEntertainmentMinutes % 60;

        $sleepHours = intdiv($totalSleepMinutes, 60);
        $sleepRemain = $totalSleepMinutes % 60;

        $userId = Auth::user()->id;

        $activities = Activity::all()->map(function ($activity) {
            return [
                'id' => $activity->id,
                'title' => $activity->title,
                'start' => $activity->start_time, 
                'end'   => $activity->end_time,
                'description' => $activity->description,
                'recurrence_type' => $activity->recurrence_type,
            ];
        });
        return view('activities.index', 
        compact('todayActivities', 'weekActivities'
            , 'stats','statusCounts',
            'studyHours', 'studyRemain',
            'exerciseHours', 'exerciseRemain',
            'entertainHours', 'entertainRemain',
            'sleepHours', 'sleepRemain',
            'activities', 'userId'
        ));
    }

    public function create(): View
    {
        return view('activities.create');
    }

     public function store(Request $request)
    {
        $stringDate = $request->date;
        $stringStartTime = $request->start_time;
        $stringEndTime = $request->end_time;
        $startTime = Carbon::parse("$stringDate $stringStartTime");
        $endTime = Carbon::parse("$stringDate $stringEndTime");
        $recurrenceType = "None";
        if(isset($request->repeat)) {
            $recurrenceType = $request->recurrence_type;
        };

        Activity::create([
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'title' => $request->title,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'description' => $request->description,
            'recurrence_type' => $recurrenceType,
        ]);


        return redirect()->back()->with('success', 'Hoạt động đã được thêm!');
    }

    public function updateStatus(Request $request, Activity $activity)
    {
        $activity->update([
            'status' => $request->status
        ]);

        return response()->json(['success' => true]);
    }


    /**
     * Display the specified activity
     */
    // public function show(string $id): View|JsonResponse
    // {
    //     // In a real application: $activity = Activity::findOrFail($id);
    //     $activity = $this->getSampleActivity($id);

    //     if (request()->expectsJson()) {
    //         return response()->json([
    //             'success' => true,
    //             'activity' => $activity
    //         ]);
    //     }

    //     return view('activities.show', compact('activity'));
    // }

    /**
     * Show the form for editing the specified activity
     */
    // public function edit(string $id): View
    // {
    //     // In a real application: $activity = Activity::findOrFail($id);
    //     $activity = $this->getSampleActivity($id);

    //     return view('activities.edit', compact('activity'));
    // }

    /**
     * Update the specified activity
     */
    public function update(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'type' => 'required|string|in:study,english,javascript,webdev,exercise,entertainment,personal',
            'color' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        // In a real application: 
        // $activity = Activity::findOrFail($id);
        // $activity->update($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật sự kiện thành công!'
            ]);
        }

        return redirect()->route('activities.index')
            ->with('success', 'Đã cập nhật sự kiện thành công!');
    }

    /**
     * Remove the specified activity
     */
    public function destroy(string $id): JsonResponse|RedirectResponse
    {
        // In a real application:
        // $activity = Activity::findOrFail($id);
        // $activity->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa sự kiện thành công!'
            ]);
        }

        return redirect()->route('activities.index')
            ->with('success', 'Đã xóa sự kiện thành công!');
    }

    /**
     * Get activities for calendar display
     */
    // public function getCalendarEvents(Request $request): JsonResponse
    // {
    //     $start = $request->get('start');
    //     $end = $request->get('end');

    //     // In a real application, you would filter by date range:
    //     // $activities = Activity::where('user_id', Auth::id())
    //     //     ->whereBetween('start_datetime', [$start, $end])
    //     //     ->get();

    //     $activities = $this->getSampleActivities();

    //     $events = collect($activities)->map(function ($activity) {
    //         return [
    //             'id' => $activity['id'],
    //             'title' => $activity['title'],
    //             'start' => $activity['start'],
    //             'end' => $activity['end'],
    //             'backgroundColor' => $activity['backgroundColor'],
    //             'className' => $activity['className'],
    //             'extendedProps' => [
    //                 'type' => $activity['className'],
    //                 'note' => $activity['note'] ?? ''
    //             ]
    //         ];
    //     });

    //     return response()->json($events);
    // }

    public function move(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start' => 'required|date',
            'end' => 'required|date|after:start'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // In a real application:
        // $activity = Activity::findOrFail($id);
        // $activity->update([
        //     'start_datetime' => $request->start,
        //     'end_datetime' => $request->end
        // ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã di chuyển sự kiện thành công!'
        ]);
    }

    /**
     * Resize an activity (change duration)
     */
    public function resize(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start' => 'required|date',
            'end' => 'required|date|after:start'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // In a real application:
        // $activity = Activity::findOrFail($id);
        // $activity->update([
        //     'start_datetime' => $request->start,
        //     'end_datetime' => $request->end
        // ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã thay đổi thời lượng sự kiện!'
        ]);
    }

    /**
     * Get activity statistics
     */
    public function getStats(): JsonResponse
    {
        $user = Auth::user();

        // In a real application, you would calculate from database:
        // $stats = [
        //     'total_activities' => Activity::where('user_id', $user->id)->count(),
        //     'this_week' => Activity::where('user_id', $user->id)
        //         ->whereBetween('start_datetime', [now()->startOfWeek(), now()->endOfWeek()])
        //         ->count(),
        //     'by_type' => Activity::where('user_id', $user->id)
        //         ->selectRaw('type, COUNT(*) as count')
        //         ->groupBy('type')
        //         ->pluck('count', 'type'),
        //     'total_hours' => Activity::where('user_id', $user->id)
        //         ->selectRaw('SUM(TIMESTAMPDIFF(HOUR, start_datetime, end_datetime)) as total')
        //         ->value('total')
        // ];

        $stats = [
            'total_activities' => 20,
            'this_week' => 15,
            'by_type' => [
                'study' => 8,
                'exercise' => 5,
                'webdev' => 3,
                'english' => 2,
                'javascript' => 2
            ],
            'total_hours' => 45
        ];

        return response()->json($stats);
    }

    /**
     * Create recurring activities
     */
    private function createRecurringActivities(array $baseActivity, string $type): void
    {
        $startDate = Carbon::parse($baseActivity['start_datetime']);
        $endDate = Carbon::parse($baseActivity['end_datetime']);
        
        $occurrences = 10; 

        for ($i = 1; $i <= $occurrences; $i++) {
            $newStart = $startDate->copy();
            $newEnd = $endDate->copy();

            switch ($type) {
                case 'daily':
                    $newStart->addDays($i);
                    $newEnd->addDays($i);
                    break;
                case 'weekly':
                    $newStart->addWeeks($i);
                    $newEnd->addWeeks($i);
                    break;
                case 'monthly':
                    $newStart->addMonths($i);
                    $newEnd->addMonths($i);
                    break;
            }

            $recurringActivity = $baseActivity;
            $recurringActivity['start_datetime'] = $newStart;
            $recurringActivity['end_datetime'] = $newEnd;
            
        }
    }
}
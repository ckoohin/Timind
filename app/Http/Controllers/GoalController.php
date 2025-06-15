<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\ActivityCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoalController extends Controller
{
    public function index()
    {
        $user = User::find(Auth::user()->id);
        $today = Carbon::today();
        $activities = $user->activities;
        
        $todayActivities = Activity::with('category')
            ->where('user_id', Auth::id())
            ->whereDate('start_time', Carbon::today())
            ->orderBy('start_time')
            ->get();

        foreach ($todayActivities as $activity) {
            $activity->duration = Carbon::parse($activity->start_time)->diffInMinutes(Carbon::parse($activity->end_time));
        }

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
        })->sum('duration');

        $studyHours = intdiv($totalStudyMinutes, 60);
        $studyRemain = $totalStudyMinutes % 60;
        $exerciseHours = intdiv($totalExerciseMinutes, 60);
        $exerciseRemain = $totalExerciseMinutes % 60;
        $entertainHours = intdiv($totalEntertainmentMinutes, 60);
        $entertainRemain = $totalEntertainmentMinutes % 60;
        $sleepHours = intdiv($totalSleepMinutes, 60);
        $sleepRemain = $totalSleepMinutes % 60;

        return view('goals.index', compact(
            'todayActivities',
            'studyHours',
            'studyRemain',
            'exerciseHours',
            'exerciseRemain',
            'entertainHours',
            'entertainRemain',
            'sleepHours',
            'sleepRemain',
            'statusCounts',
            'activities'
        ));
    }

    public function postMessage(Request $request)
    {
        try {
            $message = $request->message;
            
            if (empty($message)) {
                return response()->json(['error' => 'Message cannot be empty'], 400);
            }

            $response = Http::timeout(60)->withHeaders([
                'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => 'google/gemini-2.0-flash-exp:free',
                'messages' => [
                    [
                        'role' => 'system', 
                        'content' => 'Bạn là một chuyên gia tư vấn học tập và quản lý thời gian chuyên nghiệp. Hãy trả lời bằng tiếng Việt và sử dụng HTML để định dạng nội dung một cách rõ ràng, dễ đọc.'
                    ],
                    [
                        'role' => 'user', 
                        'content' => $message
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 2000
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                if (isset($responseData['choices'][0]['message']['content'])) {
                    return response()->json([
                        'message' => $responseData['choices'][0]['message']['content']
                    ]);
                } else {
                    Log::error('Invalid AI response structure', ['response' => $responseData]);
                    return response()->json(['error' => 'Invalid response from AI service'], 500);
                }
            } else {
                Log::error('AI API call failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json(['error' => 'AI service unavailable'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error in postMessage', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An error occurred while processing your request'], 500);
        }
    }

    public function getFreeTime()
    {
        try {
            $user = Auth::user();
            $today = Carbon::today();
            $endDate = $today->copy()->addDays(7); // Get free time for next 7 days

            // Get all activities for the next 7 days
            $activities = Activity::where('user_id', $user->id)
                ->whereBetween('start_time', [$today, $endDate])
                ->orderBy('start_time')
                ->get();

            $freeTimeSlots = [];

            $workingStart = 7;
            $workingEnd = 22;

            for ($day = 0; $day < 7; $day++) {
                $currentDate = $today->copy()->addDays($day);
                $dayName = $currentDate->format('l'); 
                $dateString = $currentDate->format('Y-m-d');

                // Get activities for this specific day
                $dayActivities = $activities->filter(function ($activity) use ($currentDate) {
                    return $activity->start_time->format('Y-m-d') === $currentDate->format('Y-m-d');
                })->sortBy('start_time');

                $freeSlots = [];
                $currentHour = $workingStart;

                foreach ($dayActivities as $activity) {
                    $activityStart = $activity->start_time->hour;
                    $activityEnd = $activity->end_time->hour;

                    // Add free time before this activity
                    if ($currentHour < $activityStart) {
                        $freeSlots[] = [
                            'start' => $currentHour,
                            'end' => $activityStart,
                            'duration' => $activityStart - $currentHour
                        ];
                    }

                    $currentHour = max($currentHour, $activityEnd);
                }

                // Add remaining free time after last activity
                if ($currentHour < $workingEnd) {
                    $freeSlots[] = [
                        'start' => $currentHour,
                        'end' => $workingEnd,
                        'duration' => $workingEnd - $currentHour
                    ];
                }

                $freeTimeSlots[$dateString] = [
                    'day' => $dayName,
                    'date' => $dateString,
                    'free_slots' => $freeSlots,
                    'total_free_hours' => array_sum(array_column($freeSlots, 'duration'))
                ];
            }

            return response()->json($freeTimeSlots);

        } catch (\Exception $e) {
            Log::error('Error getting free time', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Unable to get free time'], 500);
        }
    }

    public function parseScheduleFromAI(Request $request)
    {
        try {
            $aiResponse = $request->input('ai_response');
            
            if (empty($aiResponse)) {
                return response()->json(['error' => 'AI response is required'], 400);
            }

            $scheduleItems = [];
            
            // Enhanced parsing for Vietnamese schedule format
            // Pattern 1: Match format like "📆 Thứ 2 - 8:00-10:00" or "Ngày 1 - 9:00-11:00"
            preg_match_all('/<h6[^>]*>.*?([A-Za-z\sÀ-ỹ]+)\s*-\s*(\d{1,2}):(\d{2})-(\d{1,2}):(\d{2})[^<]*<\/h6>\s*<p[^>]*>.*?<strong>Chủ đề:<\/strong>\s*([^<\n]+).*?<strong>(?:Thời gian|Mục tiêu):<\/strong>\s*([^<\n]+)/si', $aiResponse, $matches, PREG_SET_ORDER);

            // Pattern 2: Alternative format matching
            if (empty($matches)) {
                preg_match_all('/<h6[^>]*>(.*?)<\/h6>\s*<p[^>]*><strong>Chủ đề:<\/strong>\s*([^<\n]+).*?<strong>(?:Thời gian|Mục tiêu):<\/strong>\s*([^<\n]+)/si', $aiResponse, $altMatches, PREG_SET_ORDER);
                
                foreach ($altMatches as $match) {
                    $headerText = strip_tags($match[1]);
                    $subject = trim($match[2]);
                    $objective = trim($match[3]);
                    
                    // Try to extract time from header
                    if (preg_match('/(\d{1,2}):(\d{2})-(\d{1,2}):(\d{2})/', $headerText, $timeMatch)) {
                        $scheduleItems[] = $this->createScheduleItem($subject, $objective, $timeMatch[1], $timeMatch[2], $timeMatch[3], $timeMatch[4], $headerText);
                    }
                }
            } else {
                foreach ($matches as $match) {
                    $dayText = $match[1];
                    $startHour = $match[2];
                    $startMinute = $match[3];
                    $endHour = $match[4];
                    $endMinute = $match[5];
                    $subject = trim($match[6]);
                    $objective = trim($match[7]);
                    
                    $scheduleItems[] = $this->createScheduleItem($subject, $objective, $startHour, $startMinute, $endHour, $endMinute, $dayText);
                }
            }

            // Pattern 3: Fallback - extract any time patterns and subjects
            if (empty($scheduleItems)) {
                preg_match_all('/(\d{1,2}):(\d{2})\s*-\s*(\d{1,2}):(\d{2})/', $aiResponse, $timeMatches, PREG_SET_ORDER);
                preg_match_all('/<strong>Chủ đề:<\/strong>\s*([^<\n]+)/i', $aiResponse, $subjectMatches);
                preg_match_all('/<strong>Mục tiêu:<\/strong>\s*([^<\n]+)/i', $aiResponse, $objectiveMatches);

                $timeCount = count($timeMatches);
                $subjectCount = count($subjectMatches[1] ?? []);
                $objectiveCount = count($objectiveMatches[1] ?? []);

                for ($i = 0; $i < min($timeCount, $subjectCount); $i++) {
                    $subject = trim($subjectMatches[1][$i] ?? 'Học tập');
                    $objective = trim($objectiveMatches[1][$i] ?? 'Hoàn thành bài học');
                    
                    $scheduleItems[] = $this->createScheduleItem(
                        $subject, 
                        $objective, 
                        $timeMatches[$i][1], 
                        $timeMatches[$i][2], 
                        $timeMatches[$i][3], 
                        $timeMatches[$i][4],
                        "Ngày " . ($i + 1)
                    );
                }
            }

            return response()->json([
                'success' => true,
                'schedule' => $scheduleItems,
                'count' => count($scheduleItems)
            ]);

        } catch (\Exception $e) {
            Log::error('Error parsing AI schedule', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ai_response' => $request->input('ai_response')
            ]);
            return response()->json(['error' => 'Unable to parse schedule: ' . $e->getMessage()], 500);
        }
    }
    private function createScheduleItem($subject, $objective, $startHour, $startMinute, $endHour, $endMinute, $dayText)
    {
        // Calculate the target date based on day text
        $targetDate = $this->parseDayText($dayText);
        
        $startTime = $targetDate->copy()->setTime($startHour, $startMinute, 0);
        $endTime = $targetDate->copy()->setTime($endHour, $endMinute, 0);

        // Handle case where end time is next day
        if ($endTime->lt($startTime)) {
            $endTime->addDay();
        }

        return [
            'title' => $subject,
            'description' => $objective,
            'start_time' => $startTime->toDateTimeString(),
            'end_time' => $endTime->toDateTimeString(),
            'category_type' => 'study'
        ];
    }

    private function parseDayText($dayText)
    {
        $today = Carbon::today();
        
        // Map Vietnamese days to numbers
        $dayMap = [
            'chủ nhật' => 0, 'sunday' => 0,
            'thứ hai' => 1, 'thứ 2' => 1, 'monday' => 1,
            'thứ ba' => 2, 'thứ 3' => 2, 'tuesday' => 2,
            'thứ tư' => 3, 'thứ 4' => 3, 'wednesday' => 3,
            'thứ năm' => 4, 'thứ 5' => 4, 'thursday' => 4,
            'thứ sáu' => 5, 'thứ 6' => 5, 'friday' => 5,
            'thứ bảy' => 6, 'thứ 7' => 6, 'saturday' => 6,
        ];

        $dayText = strtolower($dayText);
        
        // Try to find day of week
        foreach ($dayMap as $day => $dayNumber) {
            if (strpos($dayText, $day) !== false) {
                $daysToAdd = ($dayNumber - $today->dayOfWeek + 7) % 7;
                if ($daysToAdd == 0) $daysToAdd = 7; // Next week if it's today
                return $today->copy()->addDays($daysToAdd);
            }
        }

        // Try to extract "Ngày X" pattern
        if (preg_match('/ngày\s*(\d+)/i', $dayText, $matches)) {
            $dayNumber = intval($matches[1]);
            return $today->copy()->addDays($dayNumber - 1);
        }

        // Try to extract "Tuần X, Ngày Y" pattern
        if (preg_match('/tuần\s*(\d+).*?ngày\s*(\d+)/i', $dayText, $matches)) {
            $weekNumber = intval($matches[1]);
            $dayNumber = intval($matches[2]);
            return $today->copy()->addWeeks($weekNumber - 1)->addDays($dayNumber - 1);
        }

        // Default to tomorrow if can't parse
        return $today->copy()->addDay();
    }

    public function saveSchedule(Request $request)
    {
        try {
            DB::beginTransaction();

            $scheduleData = $request->validate([
                'schedule' => 'required|array',
                'schedule.*.title' => 'required|string|max:255',
                'schedule.*.description' => 'nullable|string',
                'schedule.*.start_time' => 'required|date',
                'schedule.*.end_time' => 'required|date|after:schedule.*.start_time',
                'schedule.*.category_type' => 'nullable|string'
            ]);

            $user = Auth::user();
            $savedActivities = [];

            // Find or create study category
            $studyCategory = ActivityCategory::firstOrCreate([
                'name' => 'Học tập',
                'type' => 'study'
            ], [
                'color' => '#007bff',
                'description' => 'Các hoạt động học tập'
            ]);

            foreach ($scheduleData['schedule'] as $item) {
                // Check for time conflicts
                $startTime = Carbon::parse($item['start_time']);
                $endTime = Carbon::parse($item['end_time']);
                
                $conflictingActivity = Activity::where('user_id', $user->id)
                    ->where(function($query) use ($startTime, $endTime) {
                        $query->whereBetween('start_time', [$startTime, $endTime])
                              ->orWhereBetween('end_time', [$startTime, $endTime])
                              ->orWhere(function($q) use ($startTime, $endTime) {
                                  $q->where('start_time', '<=', $startTime)
                                    ->where('end_time', '>=', $endTime);
                              });
                    })
                    ->first();

                if ($conflictingActivity) {
                    Log::warning('Time conflict detected', [
                        'new_activity' => $item,
                        'conflicting_activity' => $conflictingActivity->toArray()
                    ]);
                    continue;
                }

                $activity = new Activity([
                    'user_id' => $user->id,
                    'category_id' => $studyCategory->id,
                    'title' => $item['title'],
                    'description' => $item['description'] ?? '',
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => 'planned',
                    'priority' => 'medium',
                    'is_all_day' => false,
                    'location' => null,
                    'recurrence_type' => null,
                    'recurrence_data' => null,
                    'reminder_settings' => json_encode([
                        'enabled' => true,
                        'minutes_before' => 15
                    ])
                ]);

                $activity->save();
                $savedActivities[] = $activity->load('category');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lịch học đã được lưu thành công!',
                'activities' => count($savedActivities),
                'saved_activities' => $savedActivities
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Dữ liệu không hợp lệ',
                'validation_errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving schedule', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'schedule_data' => $request->all()
            ]);
            return response()->json(['error' => 'Không thể lưu lịch học: ' . $e->getMessage()], 500);
        }
    }

    public function parseAndSaveSchedule(Request $request)
    {
        try {
            $parseRequest = new Request($request->only('ai_response'));
            $parseResponse = $this->parseScheduleFromAI($parseRequest);
            
            if (!$parseResponse->getData()->success) {
                return $parseResponse;
            }

            $schedule = $parseResponse->getData()->schedule;
            
            if (empty($schedule)) {
                return response()->json([
                    'error' => 'Không thể phân tích được lịch học từ AI response',
                    'ai_response' => $request->input('ai_response')
                ], 400);
            }

            $saveRequest = new Request(['schedule' => $schedule]);
            return $this->saveSchedule($saveRequest);

        } catch (\Exception $e) {
            Log::error('Error in parseAndSaveSchedule', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Có lỗi xảy ra khi xử lý lịch học'], 500);
        }
    }
}
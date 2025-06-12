<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OpenRouterService
{
    private $apiKey;
    private $baseUrl = 'https://openrouter.ai/api/v1';
    
    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key');
    }
    
    private function callAPI($messages, $model = 'openai/gpt-3.5-turbo')
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url'),
                'X-Title' => 'Timind Web - Smart Time Management'
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.7,
                'max_tokens' => 1000
            ]);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            Log::error('OpenRouter API Error: ' . $response->body());
            return null;
            
        } catch (\Exception $e) {
            Log::error('OpenRouter API Exception: ' . $e->getMessage());
            return null;
        }
    }
    
    public function generateScheduleSuggestions($existingActivities, $userPreferences = [])
    {
        $activitiesData = $existingActivities->map(function ($activity) {
            return [
                'title' => $activity->title,
                'start_time' => $activity->start_time,
                'end_time' => $activity->end_time,
                'category' => $activity->category ?? 'general',
                'priority' => $activity->priority ?? 'medium'
            ];
        })->toArray();
        
        $prompt = $this->buildSchedulePrompt($activitiesData, $userPreferences);
        
        $messages = [
            [
                'role' => 'system',
                'content' => 'Bạn là một AI chuyên gia quản lý thời gian thông minh. Nhiệm vụ của bạn là phân tích lịch hiện tại và đưa ra các gợi ý hoạt động hợp lý cho các khoảng thời gian trống. Trả lời bằng tiếng Việt và format JSON.'
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ];
        
        $response = $this->callAPI($messages);
        
        if ($response && isset($response['choices'][0]['message']['content'])) {
            return $this->parseScheduleSuggestions($response['choices'][0]['message']['content']);
        }
        
        return [];
    }
    
    public function analyzeActivitiesForGoals($activities, $goals = [])
    {
        $activitiesData = $activities->map(function ($activity) {
            return [
                'title' => $activity->title,
                'category' => $activity->category ?? 'general',
                'duration' => $this->calculateDuration($activity->start_time, $activity->end_time),
                'frequency' => $activity->frequency ?? 'once',
                'completed' => $activity->completed ?? false
            ];
        })->toArray();
        
        $prompt = $this->buildAnalysisPrompt($activitiesData, $goals);
        
        $messages = [
            [
                'role' => 'system',
                'content' => 'Bạn là một AI coach chuyên phân tích thói quen và mục tiêu cá nhân. Hãy phân tích các hoạt động và đưa ra nhận xét, gợi ý cải thiện. Trả lời bằng tiếng Việt.'
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ];
        
        $response = $this->callAPI($messages);
        
        if ($response && isset($response['choices'][0]['message']['content'])) {
            return $this->parseAnalysis($response['choices'][0]['message']['content']);
        }
        
        return [
            'overall_analysis' => 'Không thể phân tích được dữ liệu.',
            'suggestions' => [],
            'time_distribution' => []
        ];
    }
    
    private function buildSchedulePrompt($activities, $preferences)
    {
        $activitiesText = json_encode($activities, JSON_UNESCAPED_UNICODE);
        $preferencesText = json_encode($preferences, JSON_UNESCAPED_UNICODE);
        
        return "
        Dựa trên lịch hiện tại và sở thích của người dùng, hãy đề xuất các hoạt động cho khoảng thời gian trống.
        LỊCH HIỆN TẠI:
        {$activitiesText}

        SỞ THÍCH/YÊU CẦU:
        {$preferencesText}

        Hãy phân tích và đề xuất các hoạt động phù hợp cho các khoảng thời gian trống, bao gồm:
        1. Thời gian nghỉ ngơi
        2. Hoạt động thể chất  
        3. Học tập/phát triển bản thân
        4. Công việc/dự án
        5. Giải trí/thư giãn

        Trả về định dạng JSON với cấu trúc:
        {
        \"suggestions\": [
            {
            \"title\": \"Tên hoạt động\",
            \"suggested_time\": \"HH:MM\",
            \"duration\": \"số phút\",
            \"category\": \"danh mục\",
            \"priority\": \"low/medium/high\",
            \"reason\": \"lý do đề xuất\"
            }
        ]
        }
        ";
    }
    
    /**
     * Xây dựng prompt cho phân tích goals
     */
    private function buildAnalysisPrompt($activities, $goals)
    {
        $activitiesText = json_encode($activities, JSON_UNESCAPED_UNICODE);
        $goalsText = json_encode($goals, JSON_UNESCAPED_UNICODE);
        
        return "
        Hãy phân tích các hoạt động sau và đưa ra nhận xét về việc quản lý thời gian và tiến độ đạt mục tiêu:

        CÁC HOẠT ĐỘNG:
        {$activitiesText}

        MỤC TIÊU:
        {$goalsText}

        Hãy phân tích và đưa ra:
        1. Nhận xét tổng quan về cách phân bổ thời gian
        2. So sánh với mục tiêu đã đề ra
        3. Các điểm mạnh trong quản lý thời gian
        4. Các vấn đề cần cải thiện
        5. Gợi ý cụ thể để tối ưu hóa

        Trả về định dạng JSON với cấu trúc:
        {
        \"overall_analysis\": \"nhận xét tổng quan\",
        \"strengths\": [\"điểm mạnh 1\", \"điểm mạnh 2\"],
        \"weaknesses\": [\"điểm yếu 1\", \"điểm yếu 2\"],  
        \"suggestions\": [\"gợi ý 1\", \"gợi ý 2\"],
        \"goal_progress\": \"đánh giá tiến độ mục tiêu\",
        \"time_distribution\": {
            \"work\": \"phần trăm\",
            \"personal\": \"phần trăm\", 
            \"rest\": \"phần trăm\"
        }
        }
        ";
    }
    
    private function parseScheduleSuggestions($content)
    {
        try {
            // Tìm JSON trong response
            preg_match('/\{.*\}/s', $content, $matches);
            if (!empty($matches)) {
                $data = json_decode($matches[0], true);
                return $data['suggestions'] ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Error parsing schedule suggestions: ' . $e->getMessage());
        }
        
        return [];
    }
    
    private function parseAnalysis($content)
    {
        try {
            preg_match('/\{.*\}/s', $content, $matches);
            if (!empty($matches)) {
                return json_decode($matches[0], true);
            }
        } catch (\Exception $e) {
            Log::error('Error parsing analysis: ' . $e->getMessage());
        }
        
        return [
            'overall_analysis' => strip_tags($content),
            'suggestions' => [],
            'time_distribution' => []
        ];
    }
    
    private function calculateDuration($startTime, $endTime)
    {
        try {
            $start = Carbon::parse($startTime);
            $end = Carbon::parse($endTime);
            return $end->diffInMinutes($start);
        } catch (\Exception $e) {
            return 0;
        }
    }
}
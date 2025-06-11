<?php

namespace App\Http\Controllers\Api;

use App\Models\AiInteractionLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiController extends Controller
{
    public function processComment($id)
    {
        $comment = AiInteractionLog::find($id);
        if (!$comment) {
            return response()->json(['error' => 'Không tìm thấy bình luận'], 404);
        }

        $inputData = $comment->comment_text;

        $prompt = "Dựa trên bình luận sau: '$inputData', hãy đưa ra một lời tư vấn tích cực và ngắn gọn về quá trình hoạt động một ngày của người dùng.";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => 100,
        ]);

        if ($response->successful()) {
            $json = $response->json();
            $feedback = $json['choices'][0]['message']['content'] ?? null;

            if ($feedback) {
                $comment->feedback_text = $feedback;
                $comment->save();

                return response()->json([
                    'input' => $inputData,
                    'feedback' => $feedback,
                ]);
            } else {
                return response()->json(['error' => 'API không trả về dữ liệu hợp lệ'], 500);
            }
        }

        return response()->json(['error' => 'Không thể xử lý yêu cầu API'], 500);
    }
}
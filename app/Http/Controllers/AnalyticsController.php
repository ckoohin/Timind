<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AnalyticsController extends Controller
{
    public function index() {
        return view('chat');
    }

    public function postMessage(Request $request) {
        $message = $request->message;
        
        $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
        'Content-Type'  => 'application/json',
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'openai/gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $message]
            ]
        ]);

        if ($response->successful()) {
            return response()->json([
                'message' => $response->json()['choices'][0]['message']['content']
            ]);
        }

        return response()->json(['error' => 'API gọi thất bại'], 500);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class GoalController extends Controller
{
    public function index(){
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
        $statusCounts = $todayActivities->groupBy('status')->map->count()->toArray();
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

        return view('goals.index', compact(
            'todayActivities',
            'studyHours', 'studyRemain',
            'exerciseHours', 'exerciseRemain',
            'entertainHours', 'entertainRemain',
            'sleepHours', 'sleepRemain',
            'statusCounts','activities'
        ));
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

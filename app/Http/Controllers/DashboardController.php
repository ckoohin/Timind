<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = User::find(Auth::user()->id);
        $today = Carbon::today();
        $activities = $user->activities;
        $upcomingActivities = Activity::whereDate('start_time', '>', today())
            ->where('user_id', Auth::id())
            ->orderBy('start_time')
            ->limit(5)
            ->get();
        
        $todayActivities = Activity::with('category')
            ->where('user_id', Auth::id())
            ->whereDate('start_time', Carbon::today())
            ->orderBy('start_time')
            ->get();


        foreach ($todayActivities as $activity) {
            $activity->duration = Carbon::parse($activity->start_time)->diffInMinutes(Carbon::parse($activity->end_time));
        }
        // $statusCounts = $todayActivities->groupBy('status')->map->count()->toArray();
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

        return view('dashboard.index', compact(
            'todayActivities','upcomingActivities',
            'studyHours', 'studyRemain',
            'exerciseHours', 'exerciseRemain',
            'entertainHours', 'entertainRemain',
            'sleepHours', 'sleepRemain',
            'statusCounts','activities'
        ));
    }
}
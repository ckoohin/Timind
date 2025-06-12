<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
<<<<<<< HEAD
=======
use Carbon\Carbon;
>>>>>>> be490f0617e04cab9bb59357c07635e0ab0bb723

class DashboardController extends Controller
{
    public function index()
    {
        $user = User::find(Auth::user()->id);
<<<<<<< HEAD
        $activities = $user->activities;
        $todayActivities = Activity::whereDate('start_time', today())->get();
=======
        $today = Carbon::today();
        $activities = $user->activities;
>>>>>>> be490f0617e04cab9bb59357c07635e0ab0bb723
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

<<<<<<< HEAD
        return view('dashboard.index', compact('todayActivities', 'upcomingActivities', 'activities'));
=======

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

        return view('dashboard.index', compact(
            'todayActivities','upcomingActivities',
            'studyHours', 'studyRemain',
            'exerciseHours', 'exerciseRemain',
            'entertainHours', 'entertainRemain',
            'sleepHours', 'sleepRemain',
            'statusCounts','activities'
        ));
>>>>>>> be490f0617e04cab9bb59357c07635e0ab0bb723
    }
}
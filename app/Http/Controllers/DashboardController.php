<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\ActivityCategory;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get upcoming activities
        $upcomingActivities = Activity::where('user_id', $user->id)
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->take(5)
            ->with('category')
            ->get();

        // Get today's activities
        $todayActivities = Activity::where('user_id', $user->id)
            ->whereDate('start_time', today())
            ->with('category')
            ->get();

        return view('dashboard.index', compact('upcomingActivities', 'todayActivities'));
    }
}

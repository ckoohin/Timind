<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;

class DashboardController extends Controller
{
    public function index()
    {
        $todayActivities = Activity::whereDate('start_time', today())->get();
        $upcomingActivities = Activity::whereDate('start_time', '>', today())
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact('todayActivities', 'upcomingActivities'));
    }

}
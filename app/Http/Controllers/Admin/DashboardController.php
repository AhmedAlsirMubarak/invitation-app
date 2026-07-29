<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;

class DashboardController extends Controller
{
    public function index()
    {
        $events = Event::withCount('guests')
            ->orderByDesc('event_date')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('events'));
    }
}

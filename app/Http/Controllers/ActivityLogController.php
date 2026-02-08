<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        $activities = ActivityLog::with('user')
        ->latest()
        ->limit(20)
        ->get();

        return response()->json($activities);
    }
}

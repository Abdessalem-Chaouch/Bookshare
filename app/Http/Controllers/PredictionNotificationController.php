<?php

namespace App\Http\Controllers;

use App\Models\PredictionNotification;
use Illuminate\Http\Request;

class PredictionNotificationController extends Controller
{
    public function index()
    {
        $notifications = PredictionNotification::with('store')
                            ->latest()
                            ->take(20)
                            ->get();

        return view('notifications.index', compact('notifications'));
    }
}

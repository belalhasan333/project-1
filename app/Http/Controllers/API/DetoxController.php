<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetoxSession;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DetoxController extends Controller
{
    // Status
    public function updateProgress(Request $request)
    {
        $request->validate([
            'minutes' => 'required|integer|min:1|max:180',
        ]);

        $user = Auth::user();
        $today = now()->toDateString();

        $session = DetoxSession::firstOrCreate(
            [
                'user_id' => $user->id,
                'date'    => $today,
            ],
            [
                'duration_minutes' => 0,
            ]
        );

        $session->increment('duration_minutes', $request->minutes);
        $session->refresh();

        $hours   = floor($session->duration_minutes / 60);
        $minutes = $session->duration_minutes % 60;

        return response()->json([
            'message' => 'Progress updated',
            'data' => [
                'user_id'          => $user->id,
                'date'             => $session->date,
                'total_minutes'    => $session->duration_minutes,
                'total_human'      => "{$hours}h {$minutes}m",
                'progress_percent' => $session->progress_percent,
            ]
        ]);
    }
}

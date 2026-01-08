<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetoxSession;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DetoxController extends Controller
{
    public function status()
    {
        $user = Auth::user();

        // Active session
        $activeSession = DetoxSession::getActiveForUser($user->id);

        // Weekly total
        $startOfWeek = Carbon::now()->startOfWeek();
        $weeklyTotalMinutes = DetoxSession::where('user_id', $user->id)
            ->where('ended_at', '>=', $startOfWeek)
            ->sum('duration_minutes');

        return response()->json([
            'current_session' => $activeSession ? [
                'active'            => true,
                'id'                => $activeSession->id,
                'duration_minutes'  => $activeSession->duration_minutes,
                'started_at'        => $activeSession->started_at,
                'remaining_seconds' => $activeSession->remaining_seconds,
            ] : ['active' => false],

            'weekly_total_minutes' => (int) $weeklyTotalMinutes,
            // optional: 'weekly_goal_minutes' => 420, // 7 hours example
        ]);
    }

    public function start(Request $request)
    {
        $request->validate([
            'duration_minutes' => 'required|integer|min:1|max:1440', // max 24 hours
        ]);

        $user = Auth::user();

        // (optional logic)
        $existing = DetoxSession::getActiveForUser($user->id);
        if ($existing) {
            $existing->update(['ended_at' => now()]);
        }

        $session = DetoxSession::create([
            'user_id'         => $user->id,
            'duration_minutes' => $request->duration_minutes,
            'started_at'      => now(),
        ]);

        return response()->json([
            'message' => 'Detox session started',
            'session' => [
                'id'                => $session->id,
                'remaining_seconds' => $session->duration_minutes * 60,
            ]
        ], 201);
    }

    public function end(Request $request)
    {
        $user = Auth::user();

        $session = DetoxSession::getActiveForUser($user->id);

        if (!$session) {
            return response()->json(['message' => 'No active session'], 404);
        }

        $session->update(['ended_at' => now()]);

        return response()->json([
            'message' => 'Detox session ended',
            'total_minutes' => $session->duration_minutes
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Goal;
use App\Models\GoalProgress;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GoalController extends Controller
{
    // Goal list
    public function index(Request $request)
    {
        $status = $request->query('status', 'active');

        $goals = Goal::where('user_id', auth()->id())
            ->where('status', $status)
            ->with('category')
            ->withCount('progress')
            ->get()
            ->map(function ($goal) {
                return [
                    'id' => $goal->id,
                    'title' => $goal->title,
                    'goal_type' => $goal->goal_type,
                    'start_date' => $goal->start_date->toDateString(),
                    'end_date' => $goal->end_date->toDateString(),
                    'reminder_time' => $goal->reminder_time,
                    'motivation' => $goal->motivation,
                    'status' => $goal->status,
                    'category' => $goal->category ? $goal->category->name : null,
                    'progress_count' => $goal->progress_count,
                ];
            });

        return response()->json($goals);
    }

    // Create goal
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required',
            'title' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'goal_type' => 'required|in:daily,weekly,monthly',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'reminder_time' => 'nullable',
            'motivation' => 'nullable|string',
        ]);

        $data['user_id'] = auth()->id();

        $goal = Goal::create($data);

        return response()->json([
            'message' => 'Goal created successfully',
            'goal' => $goal
        ], 201);
    }

    // Goal details and progress
    public function show(Goal $goal)
    {
        $this->authorizeGoal($goal);

        $goal->load('category');  // load category relation

        $totalDays = $goal->start_date->diffInDays($goal->end_date) + 1;
        $completedDays = $goal->progress()->count();

        $progress = round($completedDays * 100 / $totalDays, 2);

        return response()->json([
            'goal' => [
                'id' => $goal->id,
                'title' => $goal->title,
                'goal_type' => $goal->goal_type,
                'start_date' => $goal->start_date->toDateString(),
                'end_date' => $goal->end_date->toDateString(),
                'reminder_time' => $goal->reminder_time,
                'motivation' => $goal->motivation,
                'status' => $goal->status,
                'category' => $goal->category ? $goal->category->name : null,
            ],
            'statistics' => [
                'total_days' => $totalDays,
                'completed_days' => $completedDays,
                'progress_percent' => $progress
            ]
        ]);
    }

    // Mark today complete
    public function completeToday(Goal $goal)
    {
        $this->authorizeGoal($goal);

        if ($goal->status === 'achieved') {
            return response()->json(['message' => 'Goal already achieved'], 409);
        }

        $today = Carbon::today();

        GoalProgress::firstOrCreate([
            'goal_id' => $goal->id,
            'date' => $today
        ]);

        return response()->json([
            'message' => 'Today marked as complete'
        ]);
    }

    // Achieve goal
    public function achieve(Goal $goal)
    {
        $this->authorizeGoal($goal);

        $goal->update(['status' => 'achieved']);

        return response()->json([
            'message' => 'Goal achieved successfully'
        ]);
    }

    private function authorizeGoal(Goal $goal)
    {
        abort_if($goal->user_id !== auth()->id(), 403, 'Unauthorized');
    }
}

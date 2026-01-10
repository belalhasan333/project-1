<?php

namespace App\Models;

use App\Traits\Favoritable;
use Illuminate\Database\Eloquent\Model;

class GoalProgress extends Model
{
    use Favoritable;

    protected $table = 'goal_progress';

    protected $fillable = [
        'goal_id',
        'date',
        'is_completed'
    ];

    protected $casts = [
        'date' => 'date',
        'is_completed' => 'boolean'
    ];

    // Relation with Goal
    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }
}

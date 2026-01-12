<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Favoritable;

class Goal extends Model
{
    use Favoritable;

    protected $fillable = [
        'user_id',
        'title',
        'category_id',
        'goal_type',
        'start_date',
        'end_date',
        'reminder_time',
        'motivation',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relation with GoalProgress
    public function progress()
    {
        return $this->hasMany(GoalProgress::class);
    }

    // Relation with Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use App\Traits\Favoritable;
use Illuminate\Database\Eloquent\Model;

class DetoxSession extends Model
{
    use Favoritable;
    protected $fillable = [
        'user_id',
        'date',
        'duration_minutes',
    ];

    protected $appends = ['progress_percent'];

    public function getProgressPercentAttribute()
    {
        $goal = 420; // weekly / daily goal changeable
        return min(100, round(($this->duration_minutes / $goal) * 100));
    }
}

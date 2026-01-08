<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetoxSession extends Model
{
    protected $fillable = ['user_id', 'duration_minutes', 'started_at', 'ended_at'];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper: remaining seconds calculate
    public function getRemainingSecondsAttribute()
    {
        if ($this->ended_at) {
            return 0;
        }

        $endTime = $this->started_at->addMinutes($this->duration_minutes);
        $remaining = $endTime->diffInSeconds(now());

        return max(0, $remaining);
    }

    // Active session check
    public static function getActiveForUser($userId)
    {
        return self::where('user_id', $userId)
            ->whereNull('ended_at')
            ->latest()
            ->first();
    }
}

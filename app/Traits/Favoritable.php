<?php

namespace App\Traits;

use App\Models\Favorite;

trait Favoritable
{
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function isFavoritedBy($user)
    {
        return $this->favorites()
            ->where('user_id', $user->id)
            ->exists();
    }
}

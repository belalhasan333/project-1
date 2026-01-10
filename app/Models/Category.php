<?php

namespace App\Models;

use App\Traits\Favoritable;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use Favoritable;

    protected $fillable = ['name','slug'];

    public function meditations()
    {
        return $this->hasMany(Meditation::class);
    }
}

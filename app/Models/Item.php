<?php

namespace App\Models;

use App\Traits\Favoritable;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use Favoritable;

    protected $fillable = [
        'title',
        'short_title',
        'description',
        'is_favorite'
    ];
}

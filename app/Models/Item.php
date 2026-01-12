<?php

namespace App\Models;

use App\Traits\Favoritable;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use Favoritable;

    protected $fillable = [
        'title',
        'user_id',
        'short_title',
        'description',
        'is_favorite'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

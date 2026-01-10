<?php

namespace App\Models;

use App\Traits\Favoritable;
use Illuminate\Database\Eloquent\Model;

class Meditation extends Model
{
    use Favoritable;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'cover_image',
        'audio',
        'duration'
    ];

    // Relation with Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

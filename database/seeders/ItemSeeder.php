<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Item;

class ItemSeeder extends Seeder
{
    public function run()
    {
        Item::insert([
            [
                'title' => 'Laravel Course',
                'short_title' => 'Laravel',
                'description' => 'Laravel backend development course',
                'is_favorite' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'React Course',
                'short_title' => 'React',
                'description' => 'Frontend React learning',
                'is_favorite' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'API Development',
                'short_title' => 'API',
                'description' => 'REST API with Laravel',
                'is_favorite' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

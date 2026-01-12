<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run()
    {
       
        $user = User::first();


        $userId = $user ? $user->id : 1;

        Item::insert([
            [
                'title'        => 'Laravel Course',
                'short_title'  => 'Laravel',
                'description'  => 'Laravel backend development course',
                'is_favorite'  => false,
                'user_id'      => $userId,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'title'        => 'React Course',
                'short_title'  => 'React',
                'description'  => 'Frontend React learning',
                'is_favorite'  => false,
                'user_id'      => $userId,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'title'        => 'API Development',
                'short_title'  => 'API',
                'description'  => 'REST API with Laravel',
                'is_favorite'  => false,
                'user_id'      => $userId,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }
}

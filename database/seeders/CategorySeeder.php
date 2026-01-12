<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
       
        $user = User::where('email', 'test1@example.com')->first();

        if (!$user) {
            $this->command->error('Test user not found! Run UserSeeder first.');
            return;
        }

        $categories = [
            'Fitness',
            'Health',
            'Meditation',
            'Self Care',
            'Productivity',
            'Mental Health',
            'Lifestyle',
            'Sleep',
            'Nutrition',
            'Personal Growth',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(
                [
                    'name'    => $name,
                    'user_id' => $user->id,
                ]
            );
        }

        $this->command->info(count($categories) . ' categories seeded successfully!');
    }
}

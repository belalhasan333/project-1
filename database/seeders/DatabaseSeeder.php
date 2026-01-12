<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test1@example.com',
        ]);

        // Seeder calls
        $this->call([
            CategorySeeder::class,
            ItemSeeder::class,
        ]);
    }
}

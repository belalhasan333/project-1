<?php

namespace Database\Seeders;

use App\Models\User;
use App\Notifications\UserNotification;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Notification;

class NotificationSeeder extends Seeder
{

    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->error('No user found');
            return;
        }

        for ($i = 1; $i <= 15; $i++) {
            $data = [
                'title' => 'Test Notification ' . $i,
                'body' => 'This is notification ' . $i,
                'url'  => url('/'),
            ];

            Notification::send($users, new UserNotification($data));
        }

        $this->command->info('15 notifications sent to all users');
    }
}

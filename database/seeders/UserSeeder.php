<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@timind.com',
            'password' => Hash::make('password'),
            'full_name' => 'Administrator',
            'status' => 'active',
            'timezone' => 'Asia/Ho_Chi_Minh',
            'preferences' => json_encode([
                'language' => 'vi',
                'theme' => 'light',
                'notifications' => true
            ])
        ]);

        User::create([
            'name' => 'Demo User',
            'email' => 'demo@timind.com',
            'password' => Hash::make('password'),
            'full_name' => 'Demo User',
            'status' => 'active',
            'timezone' => 'Asia/Ho_Chi_Minh',
            'preferences' => json_encode([
                'language' => 'vi',
                'theme' => 'light',
                'notifications' => true
            ])
        ]);
    }
}

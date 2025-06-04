<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ActivityCategory;

class ActivityCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Học tập',
                'color_code' => '#3B82F6',
                'icon' => 'book',
                'type' => 'study',
                'is_system_default' => true
            ],
            [
                'name' => 'Nghỉ ngơi',
                'color_code' => '#10B981',
                'icon' => 'coffee',
                'type' => 'rest',
                'is_system_default' => true
            ],
            [
                'name' => 'Giải trí',
                'color_code' => '#F59E0B',
                'icon' => 'gamepad-2',
                'type' => 'entertainment',
                'is_system_default' => true
            ],
            [
                'name' => 'Cá nhân',
                'color_code' => '#8B5CF6',
                'icon' => 'user',
                'type' => 'personal',
                'is_system_default' => true
            ]
        ];

        foreach ($categories as $category) {
            ActivityCategory::create($category);
        }
    }
}

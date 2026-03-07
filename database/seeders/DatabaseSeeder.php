<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // will be used to set user preferences
        $user = User::firstWhere('email', 'test@example.com');

        if (! $user) {
            $user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        $category = Category::first(['name']);
        if ($category) {
            $user->preferences()->create([
                'preference_type' => $category->name,
            ]);
        }

    }
}

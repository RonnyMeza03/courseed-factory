<?php

namespace Database\Seeders;

use App\Models\Categories;
use App\Models\Courses;
use App\Models\Reviews;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(2)->create();
    }
}

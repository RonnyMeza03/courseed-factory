<?php

namespace Database\Seeders;

use App\Models\Reviews;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Reviews::factory()->create([
            'createdAt' => now(),
            'updatedAt' => now(),
            'content' => 'Content de prueba',
            'rating' => 4,
            'courseId' => 2
        ]);
    }
}

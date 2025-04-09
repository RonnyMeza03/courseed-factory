<?php

namespace Database\Factories;

use App\Models\Courses;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Categories>
 */
class CategoriesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $interestAreas = [
            'Sistemas',
            'Redes',
            'Administracion',
            'Finanzas',
            'Medicina',
            'Matematicas',
            'Ciencias',
            'Filosofia',
            'Arte',
            'Historia',
        ];

        return [
            'name' => $this->faker->unique()->randomElement($interestAreas),
            'createdAt' => $this->faker->dateTime(),
            'updatedAt' => $this->faker->dateTime(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($category) {
            // Aquí puedes agregar lógica adicional después de crear la categoría
            Courses::factory()->create([
                'categoryId' => $category->id,
                'modality' => $this->faker->randomElement(['Online', 'Presencial']),
                'price' => $this->faker->randomFloat(2, 10, 1000),
                'url' => $this->faker->url(),
                'title' => $this->faker->sentence(3),
                'image' => $this->faker->imageUrl(),
                'description' => $this->faker->paragraph(),
                'duration' => $this->faker->numberBetween(1, 100),
                'createdAt' => $this->faker->dateTime(),
                'updatedAt' => $this->faker->dateTime(),
            ]);
        });
    }
}

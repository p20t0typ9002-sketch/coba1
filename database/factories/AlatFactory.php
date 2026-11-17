<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AlatFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'stok' => fake()->numberBetween(5, 50),
            'kondisi' => 'baik',
            'location' => fake()->randomElement([
                'Gudang A',
                'Gudang B',
                'Ruang Peralatan',
            ]),
            'is_active' => true,
        ];
    }
}
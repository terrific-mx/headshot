<?php

namespace Database\Factories;

use App\Models\Backdrop;
use App\Models\Headshot;
use App\Models\Outfit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Style>
 */
class StyleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'headshot_id' => Headshot::factory(),
            'backdrop_slug' => Backdrop::first()->slug,
            'outfit_slug' => Outfit::first()->slug,
            'status' => 'pending',
        ];
    }

    public function pending(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }
}

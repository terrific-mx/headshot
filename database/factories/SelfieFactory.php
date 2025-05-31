<?php

namespace Database\Factories;

use App\Models\Headshot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Selfie>
 */
class SelfieFactory extends Factory
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
            'path' => 'headshots/selfies/file-name.jpg',
            'original_name' => 'Test Selfie',
        ];
    }
}

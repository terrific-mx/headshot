<?php

namespace Database\Factories;

use App\Models\Headshot;
use App\Models\Style;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Photo>
 */
class PhotoFactory extends Factory
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
            'style_id' => Style::factory(),
            'prompt' => 'test-prompt',
        ];
    }

    public function pending(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function completed(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'complete',
        ]);
    }
}

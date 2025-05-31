<?php

namespace Database\Seeders;

use App\Models\Headshot;
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
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Headshot::factory()->withSelfies()->profileIncomplete()->for($user)->create([
            'name' => 'Test Headshot',
        ]);

        Headshot::factory()
            ->for($user)
            ->withSelfies()
            ->profileComplete()
            ->trainingCompleted()
            ->create([
                'name' => 'Test Headshot',
            ]);

        Headshot::factory()
            ->for($user)
            ->withSelfies()
            ->profileComplete()
            ->trainingCompleted()
            ->withStyleAndPendingPhotos()
            ->create([
                'name' => 'Test Headshot',
            ]);

        Headshot::factory()
            ->for($user)
            ->withSelfies()
            ->profileComplete()
            ->trainingCompleted()
            ->withStyleAndCompletedPhotos()
            ->create([
                'name' => 'Test Headshot',
            ]);
    }
}

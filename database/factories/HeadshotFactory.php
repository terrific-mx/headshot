<?php

namespace Database\Factories;

use App\Haiku;
use App\Models\Headshot;
use App\Models\Photo;
use App\Models\Selfie;
use App\Models\Style;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\File;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Headshot>
 */
class HeadshotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => 'Test Headshot',
            'trigger_word' => Haiku::withToken(),
        ];
    }

    public function pendingTraining(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'training_status' => 'pending',
            ];
        });
    }

    public function profileComplete(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'age' => 39,
                'eye_color' => 'Light brown',
                'gender' => 'Male',
            ];
        });
    }

    public function profileIncomplete(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'age' => null,
                'eye_color' => null,
                'gender' => null,
            ];
        });
    }

    public function paymentPending(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'payment_status' => 'pending',
            ];
        });
    }

    public function trainingCompleted(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'training_status' => 'trained',
            ];
        });
    }

    public function withSelfies(): Factory
    {
        return $this->has(Selfie::factory()->count(count($this->selfiesData()))->state(new Sequence(...$this->selfiesData())));
    }

    public function withStyleAndPendingPhotos(): Factory
    {
        return $this->has(Style::factory())->afterCreating(function (Headshot $headshot) {
            Photo::factory()->for($headshot)->for($headshot->styles()->first())->pending()->count(10)->create();
        });
    }

    public function withStyleAndCompletedPhotos(): Factory
    {
        return $this->has(Style::factory())->afterCreating(function (Headshot $headshot) {
            Photo::factory()
                ->for($headshot)
                ->for($headshot->styles()->first())
                ->completed()
                ->count(count($this->PhotosData()))
                ->state(new Sequence(...$this->PhotosData()))
                ->create();
        });
    }

    protected function selfiesData()
    {
        $directory = env('SELFIES_SEED_DIRECTORY');
        $files = File::files($directory);

        $selfiesData = [];

        foreach ($files as $file) {
            $filename = $file->getFilename();
            $path = 'headshots/selfies/'.$filename;

            File::copy($file->getPathname(), storage_path('app/public/'.$path));

            $selfiesData[] = [
                'path' => $path,
                'original_name' => $filename,
            ];
        }

        return $selfiesData;
    }

    protected function photosData()
    {
        return [
            ['url' => 'https://v3.fal.media/files/tiger/bogNP-Zvay3auMfFmIavk_0b6feed0631e442d9fd969d6af5de45b.jpg'],
            ['url' => 'https://v3.fal.media/files/lion/PqessQ1GmxngyMqzFQFTi_483fa8d4b9a04662863427c06c3d3737.jpg'],
            ['url' => 'https://v3.fal.media/files/rabbit/ji9GPOvqQwBdpwE59IhYW_a9cd393de97048008b6c0571a6546ecb.jpg'],
            ['url' => 'https://v3.fal.media/files/monkey/fFrRs9XsMbC8li3f92EIX_3ff9e45e2ff6423bb442cce414952edf.jpg'],
            ['url' => 'https://v3.fal.media/files/monkey/O-GyzjYSab6clyDvb9ZfE_49725756dfc948f28cd5bb8b186a5b36.jpg'],
            ['url' => 'https://v3.fal.media/files/zebra/iG28ZVg-wMLNFEyTgxZFa_e9627556fb7143848c7c7b03d508302a.jpg'],
            ['url' => 'https://v3.fal.media/files/panda/DuHXPvMiVRL1Ic20Zp8ig_da9177c221974a198a89137be1126c7e.jpg'],
            ['url' => 'https://v3.fal.media/files/koala/hqSs2sMUiFmh_NZfPPfUN_e0ace8d0871b4b0ca718d9a826045c77.jpg'],
            ['url' => 'https://v3.fal.media/files/kangaroo/w514PtSDLZDDQStMW3vZd_dd2b47e21a0348daaf6a048678155eb2.jpg'],
            ['url' => 'https://v3.fal.media/files/elephant/wmZK0Ut71wbVxGGsL_Z4L_4168fbafff5b44c1a1f71de58ba792ac.jpg'],
        ];
    }
}

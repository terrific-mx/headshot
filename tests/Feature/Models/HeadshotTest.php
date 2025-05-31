<?php

use App\Models\Headshot;
use Facades\App\Services\FalAIService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can determine if profile is complete', function () {
    $headshot = Headshot::factory()->create([
        'name' => 'Test',
        'age' => 30,
        'eye_color' => 'blue',
        'gender' => 'male',
    ]);

    expect($headshot->isProfileComplete())->toBeTrue();
});

it('returns false for incomplete profile', function () {
    $headshot = Headshot::factory()->create(['age' => null]);
    expect($headshot->isProfileComplete())->toBeFalse();
});

it('successfully starts training when conditions are met', function () {
    $headshot = Headshot::factory()->profileComplete()->pendingTraining()->create();

    FalAIService::shouldReceive('trainModel')->once()->andReturn('request-id');

    $headshot->startTraining();

    expect($headshot->fresh())
        ->train_request_id->toBe('request-id')
        ->training_status->toBe('training');
});

it('fails to start training when conditions are not met', function () {
    $headshot = Headshot::factory()->pendingTraining()->create(['age' => null]);

    $headshot->startTraining();

    expect($headshot->fresh()->training_status)->not->toBe('training');
});

<?php

use App\Models\Headshot;
use App\Models\Photo;
use App\Models\Style;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\post;

uses(RefreshDatabase::class);

describe('training webhook', function () {
    it('updates the traing status of the headshot by its train_request_id', function () {
        $headshot = Headshot::factory()->create(['train_request_id' => 'request-id']);

        post('/fal/training-webhook', [
            'request_id' => 'request-id',
            'status' => 'OK',
            'payload' => ['diffusers_lora_file' => ['url' => 'https://example.com/lora.safetensors']],
        ])->assertSuccessful();

        expect($headshot->fresh())
            ->training_status->toBe('trained')
            ->lora_file_url->toBe('https://example.com/lora.safetensors');
    });

    it('set the training status to failed if the webhook receives status not OK', function () {
        $headshot = Headshot::factory()->create(['train_request_id' => 'request-id']);

        post('/fal/training-webhook', [
            'request_id' => 'request-id',
            'status' => 'ERROR',
        ])->assertSuccessful();

        expect($headshot->fresh())
            ->training_status->toBe('failed')
            ->styles->toHaveCount(0);
    });
});

describe('generation webhook', function () {
    it('updates the traing status of the photo by its generate_request_id', function () {
        $photo = Photo::factory()->create(['generate_request_id' => 'request-id']);

        post('/fal/generation-webhook', [
            'request_id' => 'request-id',
            'status' => 'OK',
            'payload' => ['images' => [['url' => 'https://example.com/photo.jpg']]],
        ])->assertSuccessful();

        expect($photo->fresh())
            ->status->toBe('complete')
            ->url->toBe('https://example.com/photo.jpg');
    });

    it('set the training status to failed if the webhook receives status not OK', function () {
        $photo = Photo::factory()->pending()->create(['generate_request_id' => 'request-id']);

        post('/fal/generation-webhook', [
            'request_id' => 'request-id',
            'status' => 'ERROR',
        ])->assertSuccessful();

        expect($photo->fresh()->status)->toBe('failed');
    });

    it('sets the style to processed when there are no more pending photos', function () {
        $style = Style::factory()->create();
        $photo1 = Photo::factory()->for($style)->create(['generate_request_id' => 'request-id-1']);
        $photo2 = Photo::factory()->for($style)->create(['generate_request_id' => 'request-id-2']);

        post('/fal/generation-webhook', [
            'request_id' => 'request-id-1',
            'status' => 'OK',
            'payload' => ['images' => [['url' => 'https://example.com/photo1.jpg']]],
        ])->assertSuccessful();

        post('/fal/generation-webhook', [
            'request_id' => 'request-id-2',
            'status' => 'OK',
            'payload' => ['images' => [['url' => 'https://example.com/photo2.jpg']]],
        ])->assertSuccessful();

        expect($style->fresh()->status)->toBe('processed');
    });

    it('dosent sets the style to processed when there are more pending photos', function () {
        $style = Style::factory()->create();
        $photo1 = Photo::factory()->for($style)->pending()->create(['generate_request_id' => 'request-id-1']);
        $photo2 = Photo::factory()->for($style)->pending()->create(['generate_request_id' => 'request-id-2']);

        post('/fal/generation-webhook', [
            'request_id' => 'request-id-1',
            'status' => 'OK',
            'payload' => ['images' => [['url' => 'https://example.com/photo1.jpg']]],
        ])->assertSuccessful();

        expect($style->fresh()->status)->not()->toBe('processed');
    });
});

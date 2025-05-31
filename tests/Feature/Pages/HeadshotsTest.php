<?php

use App\Models\Headshot;
use App\Models\Style;
use App\Models\User;
use Facades\App\Services\FalAIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Livewire\Volt\Volt;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

describe('create', function () {
    it('can create a headshot by providing exactly 15 selfie photos and a name', function () {
        $user = User::factory()->create();
        $selfies = array_fill(0, 15,
            UploadedFile::fake()->image('selfie.jpg', 1024, 1024)->size(5000)
        );

        Volt::actingAs($user)
            ->test('pages.headshots.create')
            ->set('name', 'Test Headshot')
            ->set('selfies', $selfies)
            ->call('save');

        expect($user->headshots)->toHaveCount(1);
    });

    it('fails when name is not provided', function () {
        $user = User::factory()->create();
        $selfies = array_fill(0, 15,
            UploadedFile::fake()->image('selfie.jpg', 1024, 1024)
        );

        Volt::actingAs($user)
            ->test('pages.headshots.create')
            ->set('selfies', $selfies)
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    });

    it('fails when selfies are below minimum dimensions', function () {
        $user = User::factory()->create();

        Volt::actingAs($user)
            ->test('pages.headshots.create')
            ->set('selfies', [
                UploadedFile::fake()->image('selfie1.jpg', 800, 800),
            ])
            ->call('save')
            ->assertHasErrors(['selfies.0' => 'dimensions']);
    });

    it('fails when selfies exceed max size', function () {
        $user = User::factory()->create();

        Volt::actingAs($user)
            ->test('pages.headshots.create')
            ->set('selfies', [
                UploadedFile::fake()->image('selfie1.jpg', 1024, 1024)->size(11000), // 11MB
            ])
            ->call('save')
            ->assertHasErrors(['selfies.0' => 'max']);
    });

    it('fails when not providing exactly 15 photos', function () {
        $user = User::factory()->create();

        Volt::actingAs($user)
            ->test('pages.headshots.create')
            ->set('selfies', [
                UploadedFile::fake()->image('selfie.jpg', 1024, 1024),
            ])
            ->call('save')
            ->assertHasErrors(['selfies' => 'size']);
    });

    it('stores all 15 selfies with the headshot', function () {
        Storage::fake('public');
        $user = User::factory()->create();
        $selfies = array_fill(0, 15,
            UploadedFile::fake()->image('selfie.jpg', 1024, 1024)
        );

        Volt::actingAs($user)
            ->test('pages.headshots.create')
            ->set('name', 'Test headshot')
            ->set('selfies', $selfies)
            ->call('save');

        $headshot = $user->headshots()->first();
        expect($headshot->selfies)->toHaveCount(15);
    });

    it('redirects to the personal info page', function () {
        $user = User::factory()->create();
        $selfies = array_fill(0, 15,
            UploadedFile::fake()->image('selfie.jpg', 1024, 1024)->size(5000)
        );

        $component = Volt::actingAs($user)
            ->test('pages.headshots.create')
            ->set('name', 'Test headshot')
            ->set('selfies', $selfies)
            ->call('save');

        $headshot = $user->headshots()->first();
        $component->assertRedirect("/headshots/{$headshot->id}/settings");
    });
});

describe('details', function () {
    it('allows the headshot owner to access the personal details page', function () {
        $headshot = Headshot::factory()->create();

        actingAs($headshot->user)
            ->get("/headshots/{$headshot->id}/settings")
            ->assertSuccessful();
    });

    it('denies access to personal details page when requested by non-owner user', function () {
        $headshot = Headshot::factory()->create();
        /** @var User */
        $anotherUser = User::factory()->create();

        actingAs($anotherUser)
            ->get("/headshots/{$headshot->id}/settings")
            ->assertForbidden();
    });

    it('redirects unauthenticated users to login when attempting to access personal details', function () {
        $headshot = Headshot::factory()->create();

        get("/headshots/{$headshot->id}/settings")
            ->assertRedirect('/login');
    });

    it('can add personal details to a user headshot', function () {
        FalAIService::shouldReceive('trainModel');
        $headshot = Headshot::factory()->create();

        Volt::actingAs($headshot->user)->test('pages.headshot.settings', ['headshot' => $headshot])
            ->set('name', 'Oliver')
            ->set('age', '26-29')
            ->set('ethnicity', 'European')
            ->set('height', '171-180')
            ->set('weight', '71-80')
            ->set('body_type', 'Mesomorph')
            ->set('eye_color', 'Blue')
            ->set('gender', 'Male')
            ->set('glasses', 'Half')
            ->call('save');

        $headshot->refresh();

        expect($headshot)
            ->name->toBe('Oliver')
            ->age->toBe('26-29')
            ->ethnicity->toBe('European')
            ->height->toBe('171-180')
            ->weight->toBe('71-80')
            ->body_type->toBe('Mesomorph')
            ->eye_color->toBe('Blue')
            ->gender->toBe('Male')
            ->glasses->toBe('Half');
    });

    it('marks training status as training', function () {
        FalAIService::shouldReceive('trainModel')->once()->andReturn('request-id');
        $headshot = Headshot::factory()->create();

        Volt::actingAs($headshot->user)->test('pages.headshot.settings', ['headshot' => $headshot])
            ->set('name', 'Oliver')
            ->set('age', '26-29')
            ->set('ethnicity', 'European')
            ->set('height', '171-180')
            ->set('weight', '71-80')
            ->set('body_type', 'Mesomorph')
            ->set('eye_color', 'Blue')
            ->set('gender', 'Male')
            ->set('glasses', 'Half')
            ->call('save');

        expect($headshot->fresh())
            ->isTrainingInProgress()->toBeTrue()
            ->train_request_id->toBe('request-id');
    });
});

describe('download selfies', function () {
    it('return a response to download all selfie phtoso for the headshot', function () {
        $headshot = Headshot::factory()->create();

        $response = get(URL::signedRoute('headshots.selfies.download', ['headshot' => $headshot]));

        $response->assertDownload();
    });

    it('validates the signed url', function () {
        $headshot = Headshot::factory()->create();

        $response = get(URL::route('headshots.selfies.download', ['headshot' => $headshot]));

        $response->assertForbidden();
    });
});

describe('download photos', function () {
    it('return a response to download all generated photos for the headshot', function () {
        $headshot = Headshot::factory()->create();

        $response = get(URL::signedRoute('headshots.download', ['headshot' => $headshot]));

        $response->assertDownload();
    });

    it('validates the signed url', function () {
        $headshot = Headshot::factory()->create();

        $response = get(URL::route('headshots.download', ['headshot' => $headshot]));

        $response->assertForbidden();
    });
});

describe('download style photos zip file', function () {
    it('return a response to download all generated photos for the style', function () {
        $style = Style::factory()->create();

        $response = get(URL::signedRoute('headshots.styles.download', ['headshot' => $style->headshot, 'style' => $style]));

        $response->assertDownload();
    });

    it('validates the signed url', function () {
        $style = Style::factory()->create();

        $response = get(URL::route('headshots.styles.download', ['headshot' => $style->headshot, 'style' => $style]));

        $response->assertForbidden();
    });
});

describe('styles', function () {
    it('create style', function () {
        FalAIService::shouldReceive('generatePhoto')->andReturn('request-id');
        $headshot = Headshot::factory()->create([
            'lora_file_url' => 'https://example.com/lora.safetensors',
        ]);

        Volt::actingAs($headshot->user)->test('pages.headshots.styles.create', [
            'headshot' => $headshot,
        ])->set('backdrop_slug', 'building')
            ->set('outfit_slug', 'fitted-black-turtleneck-men')
            ->call('save');

        expect($headshot->styles()->first())
            ->backdrop_slug->toBe('building')
            ->outfit_slug->toBe('fitted-black-turtleneck-men');
    });

    it('adds photos when creating a new style', function () {
        FalAIService::shouldReceive('generatePhoto')->andReturn('request-id');
        $headshot = Headshot::factory()->create([
            'lora_file_url' => 'https://example.com/lora.safetensors',
        ]);

        Volt::actingAs($headshot->user)->test('pages.headshots.styles.create', [
            'headshot' => $headshot,
        ])->set('backdrop_slug', 'building')
            ->set('outfit_slug', 'fitted-black-turtleneck-men')
            ->call('save');

        expect($headshot->styles()->first()->photos->count())->toBeGreaterThan(0);
    });

    it('allows process style pending photos', function () {
        FalAIService::shouldReceive('generatePhoto')->times(10)->andReturn('request-id');
        $headshot = Headshot::factory()->create([
            'lora_file_url' => 'https://example.com/lora.safetensors',
        ]);

        Volt::actingAs($headshot->user)->test('pages.headshots.styles.create', [
            'headshot' => $headshot,
        ])->set('backdrop_slug', 'building')
            ->set('outfit_slug', 'fitted-black-turtleneck-men')
            ->call('save');

        expect($style = $headshot->styles()->first())->status->toBe('processing');

        $style->photos->each(function ($photo) {
            expect($photo)
                ->generate_request_id->toBe('request-id')
                ->status->toBe('processing');
        });
    });
});

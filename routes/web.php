<?php

use App\Models\Headshot;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Webhooks
|--------------------------------------------------------------------------
*/

Route::prefix('/')->group(function () {
    Route::post('/fal/training-webhook', function (Request $request) {
        $headshot = Headshot::where('train_request_id', $request->request_id)->firstOrFail();

        if ($request->status !== 'OK') {
            $headshot->markAsTrainingFailed();

            return;
        }

        $headshot->markAsTrained($request->payload['diffusers_lora_file']['url']);
    });

    Route::post('/fal/generation-webhook', function (Request $request) {
        $photo = Photo::where('generate_request_id', $request->request_id)->firstOrFail();
        $style = $photo->style;

        $photo->update([
            'status' => $request->status === 'OK'
                ? 'complete' : 'failed',
            'url' => $request->status === 'OK'
                ? $request->payload['images'][0]['url'] : null,
        ]);

        if (! $style->hasPendingPhotos()) {
            $style->markProcessed();
        }
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

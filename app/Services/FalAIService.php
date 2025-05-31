<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FalAIService
{
    public function trainModel(string $zipped_images_url, string $triggerWord)
    {
        $webhookUrl = url('/fal/training-webhook');
        $endpoint = 'https://queue.fal.run/fal-ai/flux-lora-portrait-trainer?fal_webhook='.urlencode($webhookUrl);

        $response = Http::withHeaders([
            'Authorization' => 'Key '.config('services.fal.api_key'),
            'Content-Type' => 'application/json',
        ])->retry(3, 100)->post($endpoint, [
            'images_data_url' => $zipped_images_url,
            'trigger_phrase' => $triggerWord,
            'steps' => 1000,
            'learning_rate' => 0.0002,
            'multiresolution_training' => true,
            'subject_crop' => true,
            'create_masks' => false,
            'data_archive_format' => 'zip',
        ]);

        $responseData = $response->json();

        return $responseData['request_id'] ?? null;
    }

    public function generatePhoto(string $prompt, string $loraUrl)
    {
        $webhookUrl = url('/fal/generation-webhook');
        $endpoint = 'https://queue.fal.run/fal-ai/flux-lora?fal_webhook='.urlencode($webhookUrl);

        $response = Http::withHeaders([
            'Authorization' => 'Key '.config('services.fal.api_key'),
            'Content-Type' => 'application/json',
        ])->retry(3, 100)->post($endpoint, [
            'prompt' => $prompt,
            'image_size' => 'square_hd',
            'model_name' => null,
            'loras' => [
                ['path' => $loraUrl, 'scale' => 0.9],
            ],
        ]);

        $responseData = $response->json();

        return $responseData['request_id'] ?? null;
    }
}

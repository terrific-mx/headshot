<?php

namespace App\Models;

use Facades\App\Services\FalAIService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipStream\ZipStream;

class Headshot extends Model
{
    /** @use HasFactory<\Database\Factories\HeadshotFactory> */
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function selfies()
    {
        return $this->hasMany(Selfie::class);
    }

    public function styles()
    {
        return $this->hasMany(Style::class)->latest();
    }

    public function pendingStyles()
    {
        return $this->hasMany(Style::class)->where('status', 'pending')->latest();
    }

    public function hasStyles()
    {
        return $this->styles()->count() > 0;
    }

    public function hasPendingStyles()
    {
        return $this->pendingStyles()->count() > 0;
    }

    public function processedStyles()
    {
        return $this->hasMany(Style::class)->where('status', 'processed')->latest();
    }

    public function photos()
    {
        return $this->hasMany(Photo::class)->latest();
    }

    public function markAsTrainingFailed()
    {
        $this->update(['training_status' => 'failed']);
    }

    public function markAsTrained($loraUrl)
    {
        $this->update([
            'training_status' => 'trained',
            'lora_file_url' => $loraUrl,
        ]);
    }

    public function isProfileComplete()
    {
        return ! empty($this->name)
            && ! empty($this->age)
            && ! empty($this->eye_color)
            && ! empty($this->gender);
    }

    public function startTraining()
    {
        if (! $this->canStartTraining()) {
            return;
        }

        return $this->update([
            'train_request_id' => FalAIService::trainModel(
                URL::signedRoute('headshots.selfies.download', ['headshot' => $this]),
                $this->trigger_word,
            ),
            'training_status' => 'training',
        ]);
    }

    public function canStartTraining()
    {
        return $this->isProfileComplete() && $this->training_status === 'pending';
    }

    public function isTrained()
    {
        return $this->training_status === 'trained';
    }

    public function isTrainingPending()
    {
        return $this->training_status === 'pending';
    }

    public function isTrainingInProgress()
    {
        return $this->training_status === 'training';
    }

    public function downloadSelfies()
    {
        $zipName = Str::of($this->trigger_word)->append('-selfies.zip');

        $headers = [
            'Content-Disposition' => "attachment; filename=\"{$zipName}\"",
            'Content-Type' => 'application/octet-stream',
        ];

        return new StreamedResponse(fn () => $this->getPhotosZipStream($this->selfies, $zipName), 200, $headers);
    }

    protected function getPhotosZipStream(Collection $selfies, string $zipName)
    {
        $zip = new ZipStream(outputName: $zipName);

        $selfies->each(function (Selfie $selfie) use ($zip) {
            $stream = Storage::disk('public')->readStream($selfie->path);

            $zip->addFileFromStream($selfie->original_name, $stream);

            if (is_resource($stream)) {
                fclose($stream);
            }
        });

        $zip->finish();

        return $zip;
    }

    public function downloadPhotos()
    {
        $zipName = Str::of($this->name)->append('-photos.zip');

        $headers = [
            'Content-Disposition' => "attachment; filename=\"{$zipName}\"",
            'Content-Type' => 'application/octet-stream',
        ];

        return new StreamedResponse(fn () => $this->getPhotosZipStreamFromUrls($this->photos, $zipName), 200, $headers);
    }

    protected function getPhotosZipStreamFromUrls(Collection $photos, string $zipName)
    {
        $zip = new ZipStream(outputName: $zipName);

        $photos->each(function (Photo $photo) use ($zip) {
            $url = $photo->url;
            $filename = basename($url) ?: 'image_'.Str::random(8).'.jpg';

            if (! $url) {
                return;
            }

            $imageContent = file_get_contents($url);

            if ($imageContent !== false) {
                $zip->addFile($filename, $imageContent);
            }
        });

        $zip->finish();
    }
}

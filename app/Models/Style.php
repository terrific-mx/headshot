<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipStream\ZipStream;

class Style extends Model
{
    /** @use HasFactory<\Database\Factories\StyleFactory> */
    use HasFactory;

    public function headshot()
    {
        return $this->belongsTo(Headshot::class);
    }

    public function backdrop()
    {
        return $this->belongsTo(Backdrop::class, 'backdrop_slug', 'slug');
    }

    public function outfit()
    {
        return $this->belongsTo(Outfit::class, 'outfit_slug', 'slug');
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    public function pendingPhotos()
    {
        return $this->hasMany(Photo::class)->where('status', 'pending');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    public function isProcessed()
    {
        return $this->status === 'processed';
    }

    public function isConfigured()
    {
        return $this->status === 'configured';
    }

    public function addPhotos($count)
    {
        $pendingPhotos = array_fill(0, $count, [
            'headshot_id' => $this->headshot->id,
            'prompt' => $this->formatted_prompt,
            'status' => 'pending',
        ]);

        return $this->photos()->createMany($pendingPhotos);
    }

    public function markAsConfigured()
    {
        $this->update(['status' => 'configured']);
    }

    public function markProcessed()
    {
        $this->update(['status' => 'processed']);
    }

    public function markAsProcessing()
    {
        $this->update(['status' => 'processing']);
    }

    public function hasPendingPhotos()
    {
        return $this->pendingPhotos()->count() > 0;
    }

    public function processPhotos()
    {
        $this->markAsProcessing();

        $this->pendingPhotos->each(function ($photo) {
            $photo->process();
        });
    }

    protected function formattedPrompt(): Attribute
    {
        return Attribute::make(
            get: function () {
                return <<<PROMPT
                Professional headshot of a {$this->headshot->gender}, aged {$this->headshot->age}, with a {$this->headshot->body_type} body type, height {$this->headshot->height} cm, weight {$this->headshot->weight} kg, and {$this->headshot->eye_color} eyes, {$this->outfit->prompt}, {$this->backdrop->prompt}
                PROMPT;
            }
        );
    }

    public function downloadPhotos()
    {
        $zipName = Str::of($this->backdrop->slug)->append('-')->append($this->outfit->slug)->append('-photos.zip');

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

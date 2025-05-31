<?php

namespace App\Models;

use Facades\App\Services\FalAIService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    /** @use HasFactory<\Database\Factories\PhotoFactory> */
    use HasFactory;

    public function headshot()
    {
        return $this->belongsTo(Headshot::class);
    }

    public function style()
    {
        return $this->belongsTo(Style::class);
    }

    public function process()
    {
        $this->loadMissing('headshot');

        $this->update([
            'generate_request_id' => FalAIService::generatePhoto(
                $this->prompt,
                $this->headshot->lora_file_url,
            ),
            'status' => 'processing',
        ]);
    }
}

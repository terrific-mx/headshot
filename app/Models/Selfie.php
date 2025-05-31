<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Selfie extends Model
{
    /** @use HasFactory<\Database\Factories\SelfieFactory> */
    use HasFactory;

    public function headshot()
    {
        return $this->belongsTo(Headshot::class);
    }

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn () => Storage::disk('public')->url($this->path)
        );
    }
}

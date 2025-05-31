<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;
use Livewire\Volt\Component;

use function Laravel\Folio\middleware;

middleware(['auth', ValidateSessionWithWorkOS::class]);

new class extends Component {
    public ?Collection $photos;

    public function mount()
    {
        $this->photos = Auth::user()->photos;
    }
}; ?>

<x-layouts.app>
    @volt('pages.photos')
        <div class="max-w-6xl mx-auto" wire:poll>
            <flux:heading size="xl" level="1">{{ __('All Photos') }}</flux:heading>

            <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($photos as $photo)
                <flux:card class="p-0 relative group overflow-hidden">
                    <a href="/styles/{{ $photo->style->id }}">
                        @if ($photo->url)
                        <div class="aspect-square w-full overflow-hidden">
                            <img src="{{ $photo->url }}" alt="Photo" class="w-full h-full object-cover">
                        </div>
                        @else
                        <div class="aspect-square w-full bg-zinc-200 animate-pulse"></div>
                        @endif
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/50 transition-all flex items-end p-4">
                            <div class="transform opacity-0 translate-y-4 group-hover:translate-y-0 group-hover:opacity-100 transition-transform w-full">
                                <h3 class="text-sm font-medium text-zinc-200">
                                    {{ $photo->headshot->name }}
                                </h3>
                                <p class="text-xs text-zinc-200 mt-1">{{ $photo->style->backdrop->name }} - {{ $photo->style->outfit->name }}</p>
                            </div>
                        </div>
                    </a>
                </flux:card>
                @endforeach
            </div>
        </div>
    @endvolt
</x-layouts.app>

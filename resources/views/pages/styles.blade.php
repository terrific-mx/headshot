<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;
use Livewire\Volt\Component;

use function Laravel\Folio\middleware;

middleware(['auth', ValidateSessionWithWorkOS::class]);

new class extends Component {
    public ?Collection $styles;

    public function mount()
    {
        $this->styles = Auth::user()->styles;
    }
}; ?>

<x-layouts.app>
    @volt('pages.styles')
        <div class="max-w-6xl mx-auto" wire:poll>
            <flux:heading size="xl" level="1">{{ __('All Styles') }}</flux:heading>

            @if($styles->isNotEmpty())
                <div class="mt-8 grid grid-cols-1 gap-6 mt-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($styles as $style)
                        <flux:card>
                            <div class="flex items-center justify-between mb-4">
                                <flux:heading level="4">
                                    {{ $style->backdrop->name }} - {{ $style->outfit->name }}
                                </flux:heading>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                @foreach ($style->photos->take(4) as $photo)
                                    <div class="relative aspect-square overflow-hidden rounded-md">
                                        @if($photo->url)
                                            <img src="{{ $photo->url }}" alt="Style Photo" class="object-cover w-full h-full">
                                        @else
                                            <div class="w-full h-full bg-zinc-200 animate-pulse"></div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4">
                                <flux:button href="/styles/{{ $style->id }}" size="sm" variant="primary" class="w-full">
                                    {{ __('View All Photos') }}
                                </flux:button>
                            </div>
                        </flux:card>
                    @endforeach
                </div>
            @endif
        </div>
    @endvolt
</x-layouts.app>

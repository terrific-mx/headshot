<?php

use App\Models\Headshot;
use App\Models\Style;
use Illuminate\Database\Eloquent\Collection;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;
use Livewire\Volt\Component;

use function Laravel\Folio\middleware;

middleware(['auth', ValidateSessionWithWorkOS::class, 'can:view,style']);

new class extends Component {
    public Headshot $headshot;
    public Style $style;
    public ?Collection $photos;

    public function mount()
    {
        $this->headshot = $this->style->headshot;
        $this->photos = $this->style->photos;
    }
}; ?>

<x-layouts.app>
    @volt('pages.styles.show')
        <div class="max-w-6xl mx-auto" wire:poll>
            <div class="max-lg:hidden">
                <flux:link href="/styles" variant="subtle" class="text-sm font-normal inline-flex items-center gap-2">
                    <flux:icon.chevron-left variant="micro" />
                    {{ __('Styles') }}
                </flux:link>
            </div>

            <div class="mt-4 lg:mt-8">
                <div class="flex items-center gap-4">
                    <flux:heading size="xl" level="1">{{ $style->backdrop->name }} + {{ $style->outfit->name }}</flux:heading>
                </div>

                <div class="mt-2.5 flex flex-wrap justify-between gap-x-6 gap-y-4">
                    <div class="flex flex-wrap gap-x-10 gap-y-4 py-1.5 items-center">
                        <flux:link href="/headshots/{{ $headshot->id }}" variant="ghost" class="text-sm font-normal">{{ $headshot->name }}</flux:link>
                    </div>
                    <div class="flex gap-4">
                        <flux:button
                            :href="URL::signedRoute('headshots.styles.download', ['headshot' => $headshot, 'style' => $style])"
                            variant="primary"
                        >
                            {{ __('Download All Photos') }}
                        </flux:button>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($photos as $photo)
                    <flux:card class="p-0 relative group overflow-hidden">
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
                    </flux:card>
                    @endforeach
                </div>
            </div>
        </div>
    @endvolt
</x-layouts.app>

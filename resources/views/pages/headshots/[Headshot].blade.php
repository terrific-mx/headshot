<?php

use App\Models\Headshot;
use Facades\App\Services\FalAIService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\URL;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;
use Livewire\Volt\Component;

use function Laravel\Folio\middleware;

middleware(['auth', ValidateSessionWithWorkOS::class, 'can:view,headshot']);

new class extends Component {
    public Headshot $headshot;
    public ?Collection $selfies;
    public ?Collection $photos;

    public function mount()
    {
        $this->selfies = $this->headshot->selfies()->take(3)->get();
        $this->photos = $this->headshot->photos;
    }
}; ?>

<x-layouts.app>
    @volt('pages.headshots.show')
        <div class="max-w-6xl mx-auto" wire:poll>
            <div class="max-lg:hidden">
                <flux:link href="/headshots" variant="subtle" class="text-sm font-normal inline-flex items-center gap-2">
                    <flux:icon.chevron-left variant="micro" />
                    {{ __('Headshots') }}
                </flux:link>
            </div>

            <div class="mt-4 lg:mt-8">
                <div class="flex items-center gap-4">
                    <flux:heading size="xl" level="1">{{ $headshot->name }}</flux:heading>

                    @if(! $headshot->isProfileComplete())
                    <flux:badge color="amber" size="sm">{{ __('Profile Incomplete') }}</flux:badge>
                    @endif

                    @if($headshot->isTrainingInProgress())
                    <flux:badge color="emerald" icon="loading" size="sm">{{ __('Traning AI') }}</flux:badge>
                    @elseif($headshot->isTrained())
                    <flux:badge color="emerald" size="sm">{{ __('AI Trained') }}</flux:badge>
                    @endif
                </div>

                <div class="isolate mt-2.5 flex flex-wrap justify-between gap-x-6 gap-y-4">
                    <div class="flex items-center flex-wrap gap-x-10 gap-y-4 py-1.5">
                        @if(!$headshot->isTrained())
                        <flux:avatar.group>
                            @foreach ($selfies as $selfie)
                            <flux:avatar src="{{ $selfie->url }}" size="xs" />
                            @endforeach
                            <flux:avatar size="xs">12+</flux:avatar>
                        </flux:avatar.group>
                        @endif
                        @if($headshot->hasStyles())
                        <flux:text variant="strong">{{ __(':count styles', ['count' => "{$headshot->styles()->count()}"]) }}</flux:text>
                        @endif
                    </div>
                    <div class="flex gap-4">
                        @if($headshot->isTrained() && $headshot->hasStyles())
                        <flux:button :href="Url::signedRoute('headshots.download', ['headshot' => $headshot])">
                            {{ __('Download All Photos') }}
                        </flux:button>
                        <flux:button href="/headshots/{{ $headshot->id }}/styles/create" variant="primary">
                            {{ __('New Style') }}
                        </flux:button>
                        @endif
                    </div>
                </div>
            </div>

            @unless($headshot->isProfileComplete())
            <div class="mt-8">
                <flux:callout icon="exclamation-triangle" variant="secondary" inline>
                    <flux:callout.heading>{{ __('Your Profile Needs Attention') }}</flux:callout.heading>
                    <flux:callout.text class="max-w-prose">{{ __('To get the best AI-generated results, we need a little more info from you. Please complete your profile—it only takes a minute!') }}</flux:callout.text>
                    <x-slot name="actions">
                        <flux:button href="/headshots/{{ $headshot->id }}/settings" variant="primary">{{ __('Finish Profile') }}</flux:button>
                    </x-slot>
                </flux:callout>
            </div>
            @endunless

            @if($headshot->isTrainingInProgress())
            <div class="mt-8">
                <flux:callout icon="loading" variant="secondary" inline>
                    <flux:callout.heading>{{ __('Training in progress') }}</flux:callout.heading>
                    <flux:callout.text class="max-w-prose">{{ __('Thank you for your payment! We have received your chosen headshot package and are now training the AI to create professional headshots. This process usually takes about 5 minutes to complete.') }}</flux:callout.text>
                </flux:callout>
            </div>
            @endif

            @if($headshot->isTrained() && ! $headshot->hasStyles())
            <div class="mt-8">
                <flux:callout icon="sparkles" variant="secondary" inline>
                    <flux:callout.heading>{{ __('AI Training Complete!') }}</flux:callout.heading>
                    <flux:callout.text class="max-w-prose">
                        {{ __('Your AI has been successfully trained! A Style is a unique combination of a backdrop and an outfit. You can now create a new style.') }}
                    </flux:callout.text>
                    <x-slot name="actions">
                        <flux:button href="/headshots/{{ $headshot->id }}/styles/create" variant="primary">
                            {{ __('New Style') }}
                        </flux:button>
                    </x-slot>
                </flux:callout>
            </div>
            @endif

            @if ($photos->isNotEmpty())
            <div class="mt-8">
                <flux:heading level="2">{{ __('All Photos') }}</flux:heading>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
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
            @endif
        </div>
    @endvolt
</x-layouts.app>

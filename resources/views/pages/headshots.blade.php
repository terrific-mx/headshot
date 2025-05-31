<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;
use Livewire\Volt\Component;

use function Laravel\Folio\middleware;

middleware(['auth', ValidateSessionWithWorkOS::class]);

new class extends Component {
    public ?Collection $headshots;

    public function mount()
    {
        $this->headshots = Auth::user()->headshots;
    }
}; ?>

<x-layouts.app>
    @volt('pages.headshots')
        <div class="mx-auto max-w-6xl space-y-8" wire:poll>
            <div class="flex items-end justify-between gap-4">
                <div class="space-y-2">
                    <flux:heading size="xl" level="1">{{ __('Headshots') }}</flux:heading>
                </div>

                @unless($headshots->isEmpty())
                <flux:button
                    href="/headshots/create"
                    variant="primary"
                    class="-my-2"
                >
                    {{ __('New Headshot') }}
                </flux:button>
                @endunless
            </div>

            @if($headshots->isEmpty())
                <flux:callout icon="sparkles" variant="secondary">
                    <flux:callout.heading>{{ __('Welcome to :app_name!', ['app_name' => config('app.name')]) }}</flux:callout.heading>
                    <flux:callout.text>
                        {{ __('Get started by creating your first AI-generated professional headshot. Our system will guide you through the simple process.') }}
                    </flux:callout.text>
                    <x-slot name="actions">
                        <flux:button href="/headshots/create" variant="primary">
                            {{ __('Create First Headshot') }}
                        </flux:button>
                    </x-slot>
                </flux:callout>
            @else
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ __('Headshot name') }}</flux:table.column>
                        <flux:table.column>{{ __('Styles') }}</flux:table.column>
                        <flux:table.column />
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach($headshots as $headshot)
                            <flux:table.row>
                                <flux:table.cell>
                                    <div class="flex items-center gap-4">
                                        <flux:link href="/headshots/{{ $headshot->id }}">{{ $headshot->name }}</flux:link>
                                        @if(! $headshot->isProfileComplete())
                                        <flux:badge color="amber" size="sm">{{ __('Profile Incomplete') }}</flux:badge>
                                        @endif

                                        @if($headshot->isTrainingInProgress())
                                        <flux:badge icon="loading" color="sky" size="sm">{{ __('Training AI') }}</flux:badge>
                                        @endif

                                        @if($headshot->isTrained())
                                        <flux:badge color="emerald" size="sm">{{ __('AI Trained') }}</flux:badge>
                                        @endif
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell>
                                    {{ $headshot->styles()->count() }}
                                </flux:table.cell>
                                <flux:table.cell align="end">
                                    @if(! $headshot->isProfileComplete())
                                    <flux:button href="/headshots/{{ $headshot->id }}/settings" size="sm">{{ __('Finish Profile') }}</flux:badge>
                                    @endif

                                    @if($headshot->isTrained())
                                    <flux:button href="/headshots/{{ $headshot->id }}/styles/create" size="sm">{{ __('New Style') }}</flux:badge>
                                    @endif
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            @endif
        </div>
    @endvolt
</x-layouts.app>

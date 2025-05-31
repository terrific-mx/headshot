<?php

use App\Models\Backdrop;
use App\Models\Headshot;
use App\Models\Outfit;
use App\Models\Style;
use Illuminate\Database\Eloquent\Collection;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

use function Laravel\Folio\middleware;

middleware(['auth', ValidateSessionWithWorkOS::class, 'can:createStyle,headshot']);

new class extends Component {
    public Headshot $headshot;
    public ?Collection $backdrops;
    public ?Collection $outfits;

    #[Validate(['required', 'exists:App\Models\Backdrop,slug'])]
    public ?string $backdrop_slug = '';

    #[Validate(['required', 'exists:App\Models\Outfit,slug'])]
    public ?string $outfit_slug = '';

    public function mount()
    {
        $this->backdrops = Backdrop::all();
        $this->outfits = Outfit::where('gender', $this->headshot->gender)->get();
    }

    public function save()
    {
        $this->authorize('createStyle', $this->headshot);

        $this->validate();

        $style = $this->headshot->styles()->create([
            'backdrop_slug' => $this->backdrop_slug,
            'outfit_slug' => $this->outfit_slug,
        ]);

        $style->addPhotos(10);

        $style->processPhotos();

        return $this->redirect("/styles/{$style->id}");
    }
}; ?>

<x-layouts.app>
    @volt('pages.headshots.styles.create')
        <div class="mx-auto max-w-3xl">
            <div class="max-lg:hidden">
                <flux:link href="/headshots/{{ $headshot->id }}" variant="subtle" class="text-sm font-normal inline-flex items-center gap-2">
                    <flux:icon.chevron-left variant="micro" />
                    {{ $headshot->name }}
                </flux:link>
            </div>

            <div class="lg:mt-8">
                <flux:heading size="xl" level="1">{{ __('New Style') }}</flux:heading>
            </div>

            <form wire:submit="save" class="space-y-8 mt-12">
                <div>
                    <flux:radio.group wire:model="backdrop_slug"
                        :label="__('Backdrop')"
                        variant="cards">
                        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($backdrops as $backdrop)
                                <flux:radio :value="$backdrop->slug" class="group" x-show="$wire.backdrop_slug === '' || $wire.backdrop_slug === '{{ $backdrop->slug }}'">
                                    <div class="flex-1 flex flex-col gap-2">
                                        <flux:heading class="leading-4 flex-1">{{ $backdrop->name }}</flux:heading>
                                        <div class="aspect-square overflow-hidden rounded-lg bg-gray-100 mt-2">
                                            <img
                                                src="{{ $headshot->gender === 'Male'
                                                                            ? $backdrop->male_preview_path
                                                                            : $backdrop->female_preview_path }}"
                                                alt="{{ $backdrop->name }}"
                                                class="object-cover w-full h-full group-hover:scale-105 transition-transform"
                                                onerror="this.style.display='none';
                                                    this.parentNode.style.background='linear-gradient(135deg, #f4f4f5 0%, #d4d4d8 100%)';
                                            ">
                                        </div>
                                    </div>
                                    <flux:radio.indicator />
                                </flux:radio>
                            @endforeach
                        </div>
                    </flux:radio.group>

                    <div x-show="$wire.backdrop_slug !== ''" x-cloak class="mt-4">
                        <flux:button x-on:click.prevent="$wire.backdrop_slug = ''" icon="arrow-path">
                            {{ __('Change') }}
                        </flux:button>
                    </div>
                </div>

                <div>
                    <flux:radio.group wire:model="outfit_slug"
                        :label="__('Outfit')"
                        variant="cards">
                        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($outfits as $outfit)
                                <flux:radio :value="$outfit->slug" class="group" x-show="$wire.outfit_slug === '' || $wire.outfit_slug === '{{ $outfit->slug }}'">
                                    <div class="flex-1 flex flex-col gap-2">
                                        <flux:heading class="leading-4 flex-1">{{ $outfit->name }}</flux:heading>
                                        <div class="aspect-square overflow-hidden rounded-lg bg-gray-100">
                                            <img
                                                src="{{ $outfit->preview_path }}"
                                                alt="{{ $outfit->name }}"
                                                class="object-cover w-full h-full group-hover:scale-105 transition-transform"
                                                onerror="this.style.display='none';
                                                    this.parentNode.style.background='linear-gradient(135deg, #f4f4f5 0%, #d4d4d8 100%)';
                                            ">
                                        </div>
                                    </div>
                                    <flux:radio.indicator />
                                </flux:radio>
                            @endforeach
                        </div>
                    </flux:radio.group>

                    <div x-show="$wire.outfit_slug !== ''" x-cloak class="mt-4">
                        <flux:button x-on:click.prevent="$wire.outfit_slug = ''" icon="arrow-path">
                            {{ __('Change') }}
                        </flux:button>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-end gap-4 mt-12">
                    <flux:button variant="ghost" href="/headshots/{{ $headshot->id }}">{{ __('Cancel') }}</flux:button>
                    <flux:button variant="primary" type="submit">
                        {{ __('Save & Generate Photos') }}
                    </flux:button>
                </div>
            </form>
        </div>
    @endvolt
</x-layouts.app>

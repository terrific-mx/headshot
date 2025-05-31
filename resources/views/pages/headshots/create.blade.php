<?php

use App\Haiku;
use Illuminate\Support\Facades\Auth;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Spatie\LivewireFilepond\WithFilePond;

use function Laravel\Folio\middleware;

middleware(['auth', ValidateSessionWithWorkOS::class]);

new class extends Component {
    use WithFileUploads;

    use WithFilePond;

    #[Validate('required|string|max:255')]
    public string $name = '';

    public array $selfies = [];

    protected function rules()
    {
       return [
            'selfies' => 'required|array|size:15',
            'selfies.*' => 'image|dimensions:min_width=1024,min_height=1024|max:10240',
       ];
    }

    public function validateUploadedFile()
    {
        $this->validate([
            'selfies' => 'array|max:15',
            'selfies.*' => 'image|dimensions:min_width=1024,min_height=1024|max:10240',
        ]);

        return true;
    }

    public function save()
    {
        $this->validate();

        $headshot = Auth::user()->headshots()->create([
            'name' => $this->name,
            'status' => 'pending',
            'trigger_word' => Haiku::withToken(),
            'training_status' => 'pending',
        ]);

        foreach ($this->selfies as $file) {
            $path = $file->store('headshots/selfies', 'public');

            $headshot->selfies()->create([
                'path' => $path,
                'original_name' => $file->getClientOriginalName()
            ]);
        }

        $this->redirect("/headshots/{$headshot->id}/settings");
    }
}; ?>

<x-layouts.app>
    @volt('pages.headshots.create')
        <div class="mx-auto max-w-xl">
            <div class="max-lg:hidden">
                <flux:link href="/headshots" variant="subtle" class="text-sm font-normal inline-flex items-center gap-2">
                    <flux:icon.chevron-left variant="micro" />
                    {{ __('Headshots') }}
                </flux:link>
            </div>

            <div class="lg:mt-8">
                <flux:heading size="xl" level="1">{{ __('Create a Professional Headshot') }}</flux:heading>
            </div>

            <form wire:submit="save" class="space-y-8 mt-12">
                <flux:input
                    wire:model="name"
                    :label="__('Name')"
                    :badge="__('Required')"
                />

                <flux:callout icon="camera" variant="secondary">
                    <flux:callout.heading>{{ __('Photo Requirements') }}</flux:callout.heading>
                    <flux:callout.text>
                        {{ __('For best results:') }}
                        <ul class="list-disc pl-5 mt-2 space-y-1">
                            <li>{{ __('Look directly at the camera') }}</li>
                            <li>{{ __('Include multiple angles') }}</li>
                            <li>{{ __('White background preferred') }}</li>
                            <li>{{ __('Natural smile') }}</li>
                        </ul>
                    </flux:callout.text>
                </flux:callout>

                <flux:field>
                    <flux:label :badge="__('15 required')">{{ __('Selfies') }}</flux:label>
                    <flux:description>{{ __(':count of 15 photos uploaded', ['count' => count($selfies)]) }}</flux:description>
                    <x-filepond::upload
                        wire:model="selfies"
                        :accepted-file-types="['image/png', 'image/jpeg', 'image/jpg']"
                        max-parallel-uploads="4"
                        multiple
                        allow-reorder="true"
                        allow-drop="true"
                        credits="false"
                    />
                    <flux:error name="selfies" />
                </flux:field>

                <div class="flex flex-col-reverse sm:flex-row justify-end gap-4 mt-12">
                    <flux:button variant="ghost" href="/headshots">{{ __('Cancel') }}</flux:button>
                    <flux:button variant="primary" type="submit" :disabled="count($selfies) < 15">
                        {{ __('Save Headshot & Configure Profile') }}
                    </flux:button>
                </div>
            </form>
            @filepondScripts
        </div>
    @endvolt
</x-layouts.app>

<?php

use App\Models\Headshot;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

use function Laravel\Folio\middleware;

middleware(['auth', ValidateSessionWithWorkOS::class, 'can:updatePersonalDetails,headshot']);

new class extends Component {
    public Headshot $headshot;

    #[Validate('required')]
    public $name = '';

    #[Validate('required|in:11-18,19-25,26-29,30-35,36-45,46-55,56-65,66-75,76+')]
    public $age = '';

    #[Validate('nullable|in:African,Arabic,Asian,Black or African American,Caribbean,Indian,Melansean,Polynesian,European,Caucasian,Latin American,Hispanic,Other')]
    public $ethnicity = '';

    #[Validate('nullable|in:<150,150-160,161-170,171-180,181-190,>190')]
    public $height = '';

    #[Validate('nullable|in:<40,40-50,51-60,61-70,71-80,81-90,91-100,>100')]
    public $weight = '';

    #[Validate('nullable|in:Ectomorph,Mesomorph,Endomorph')]
    public $body_type = '';

    #[Validate('required|in:Hazel,Gray,Light brown,Blue,Green,Dark brown')]
    public $eye_color = '';

    #[Validate('required|in:Male,Female')]
    public $gender = '';

    #[Validate('nullable|in:No,Half,Always')]
    public $glasses = '';

    public function mount()
    {
        $this->name = $this->headshot->name ?: '';
        $this->age = $this->headshot->age ?: '';
        $this->ethnicity = $this->headshot->ethnicity ?: '';
        $this->height = $this->headshot->height ?: '';
        $this->weight = $this->headshot->weight ?: '';
        $this->body_type = $this->headshot->body_type ?: '';
        $this->eye_color = $this->headshot->eye_color ?: '';
        $this->gender = $this->headshot->gender ?: '';
        $this->glasses = $this->headshot->glasses ?: '';
    }

    public function save()
    {
        $this->validate();

        $this->headshot->update([
            'name' => $this->name,
            'age' => $this->age,
            'ethnicity' => $this->ethnicity,
            'height' => $this->height,
            'weight' => $this->weight,
            'body_type' => $this->body_type,
            'eye_color' => $this->eye_color,
            'gender' => $this->gender,
            'glasses' => $this->glasses,
        ]);

        $this->headshot->startTraining();

        return redirect("/headshots/{$this->headshot->id}");
    }
}; ?>

<x-layouts.app>
    @volt('pages.headshot.settings')
        <div class="max-w-xl mx-auto">
            <div class="max-lg:hidden">
                <flux:link href="/headshots/{{ $headshot->id }}" variant="subtle" class="text-sm font-normal inline-flex items-center gap-2">
                    <flux:icon.chevron-left variant="micro" />
                    {{ $headshot->name }}
                </flux:link>
            </div>

            <div class="mt-4 lg:mt-8">
                <flux:heading size="xl" level="1">{{ __('Complete Your Personal Profile') }}</flux:heading>
            </div>

            <div class="mt-12">
                <form wire:submit="save" class="space-y-8 max-w-lg">
                    <flux:input wire:model="name" :label="__('Name')" :badge="__('Required')" required />

                    <flux:select wire:model="age" :label="__('Age Range')" :badge="__('Required')" :placeholder="__('Select your age range...')" required>
                        <flux:select.option value="11-18">{{ __('11 - 18 years') }}</flux:select.option>
                        <flux:select.option value="19-25">{{ __('19 - 25 years') }}</flux:select.option>
                        <flux:select.option value="26-29">{{ __('26 - 29 years') }}</flux:select.option>
                        <flux:select.option value="30-35">{{ __('30 - 35 years') }}</flux:select.option>
                        <flux:select.option value="36-45">{{ __('36 - 45 years') }}</flux:select.option>
                        <flux:select.option value="46-55">{{ __('46 - 55 years') }}</flux:select.option>
                        <flux:select.option value="56-65">{{ __('56 - 65 years') }}</flux:select.option>
                        <flux:select.option value="66-75">{{ __('66 - 75 years') }}</flux:select.option>
                        <flux:select.option value="76+">{{ __('76+ years') }}</flux:select.option>
                    </flux:select>

                    <flux:select wire:model="ethnicity" :label="__('Ethnic Background')" :placeholder="__('Select your ethnic background...')">
                        <flux:select.option value="African">{{ __('African') }}</flux:select.option>
                        <flux:select.option value="Arabic">{{ __('Arabic') }}</flux:select.option>
                        <flux:select.option value="Asian">{{ __('Asian') }}</flux:select.option>
                        <flux:select.option value="Black or African American">{{ __('Black or African American') }}</flux:select.option>
                        <flux:select.option value="Caribbean">{{ __('Caribbean') }}</flux:select.option>
                        <flux:select.option value="Indian">{{ __('Indian') }}</flux:select.option>
                        <flux:select.option value="Melansean">{{ __('Melansean') }}</flux:select.option>
                        <flux:select.option value="Polynesian">{{ __('Polynesian') }}</flux:select.option>
                        <flux:select.option value="European">{{ __('European') }}</flux:select.option>
                        <flux:select.option value="Caucasian">{{ __('Caucasian') }}</flux:select.option>
                        <flux:select.option value="Latin American">{{ __('Latin American') }}</flux:select.option>
                        <flux:select.option value="Hispanic">{{ __('Hispanic') }}</flux:select.option>
                        <flux:select.option value="Other">{{ __('Other') }}</flux:select.option>
                    </flux:select>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:select wire:model="height" :label="__('Height Range')" :placeholder="__('Select height range...')">
                            <flux:select.option value="<150">{{ __('<150 cm') }}</flux:select.option>
                            <flux:select.option value="150-160">{{ __('150 - 160 cm') }}</flux:select.option>
                            <flux:select.option value="161-170">{{ __('161 - 170 cm') }}</flux:select.option>
                            <flux:select.option value="171-180">{{ __('171 - 180 cm') }}</flux:select.option>
                            <flux:select.option value="181-190">{{ __('181 - 190 cm') }}</flux:select.option>
                            <flux:select.option value=">190">{{ __('>190 cm') }}</flux:select.option>
                        </flux:select>
                        <flux:select wire:model="weight" :label="__('Weight Range')" :placeholder="__('Select weight range...')">
                            <flux:select.option value="<40">{{ __('<40 kg') }}</flux:select.option>
                            <flux:select.option value="40-50">{{ __('40 - 50 kg') }}</flux:select.option>
                            <flux:select.option value="51-60">{{ __('51 - 60 kg') }}</flux:select.option>
                            <flux:select.option value="61-70">{{ __('61 - 70 kg') }}</flux:select.option>
                            <flux:select.option value="71-80">{{ __('71 - 80 kg') }}</flux:select.option>
                            <flux:select.option value="81-90">{{ __('81 - 90 kg') }}</flux:select.option>
                            <flux:select.option value="91-100">{{ __('91 - 100 kg') }}</flux:select.option>
                            <flux:select.option value=">100">{{ __('>100 kg') }}</flux:select.option>
                        </flux:select>
                    </div>

                    <flux:radio.group wire:model="body_type" :label="__('Body Type')" variant="cards" class="max-sm:flex-col">
                        <flux:radio value="Ectomorph" :label="__('Ectomorph')" :description="__('Slim')" />
                        <flux:radio value="Mesomorph" :label="__('Mesomorph')" :description="__('Athletic')" />
                        <flux:radio value="Endomorph" :label="__('Endomorph')" :description="__('Full')" />
                    </flux:radio.group>

                    <flux:select wire:model="eye_color" :label="__('Eye Color')" :placeholder="__('Select your eye color...')" :badge="__('Required')" required>
                        <flux:select.option value="Hazel">{{ __('Hazel') }}</flux:select.option>
                        <flux:select.option value="Gray">{{ __('Gray') }}</flux:select.option>
                        <flux:select.option value="Light brown">{{ __('Light brown') }}</flux:select.option>
                        <flux:select.option value="Blue">{{ __('Blue') }}</flux:select.option>
                        <flux:select.option value="Green">{{ __('Green') }}</flux:select.option>
                        <flux:select.option value="Dark brown">{{ __('Dark brown') }}</flux:select.option>
                    </flux:select>

                    <flux:radio.group wire:model="gender" :label="__('Gender Identity')" :badge="__('Required')" variant="cards" class="max-sm:flex-col" required>
                        <flux:radio value="Male" :label="__('Male')" />
                        <flux:radio value="Female" :label="__('Female')" />
                    </flux:radio.group>

                    <flux:radio.group wire:model="glasses" :label="__('Glasses Usage')" variant="cards" class="max-sm:flex-col">
                        <flux:radio value="No" :label="__('No glasses')" />
                        <flux:radio value="Half" :label="__('Half with glasses')" />
                        <flux:radio value="Always" :label="__('Always with glasses')" />
                    </flux:radio.group>

                    <div class="flex flex-col-reverse sm:flex-row justify-end gap-4 mt-12">
                        <flux:button variant="ghost" href="/headshots/{{ $headshot->id }}">{{ __('Cancel') }}</flux:button>
                        <flux:button variant="primary" type="submit">{{ __('Save & Choose Package') }}</flux:button>
                    </div>
                </form>
            </div>
        </div>
    @endvolt
</x-layouts.app>

<x-layouts.app.header :title="$title ?? null">
    <flux:main class="lg:p-10">
        {{ $slot }}
    </flux:main>
</x-layouts.app.header>

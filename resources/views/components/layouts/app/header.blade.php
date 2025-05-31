<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:header class="border-b border-zinc-950/5 bg-transparent dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:brand href="/" class="max-lg:hidden dark:hidden lg:me-8">
                <x-slot name="logo">
                    <svg class="h-full" width="34" height="48" viewBox="0 0 34 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.45659 17.2143L24.3027 8.64286L15.395 3.5L0.549161 12.0713L0.548828 29.2139L9.45652 34.3568L9.45659 17.2143Z" fill="#0A0D12"/>
                        <path d="M33.4453 17.7854L33.4453 34.9283L18.5991 43.4994L9.69141 38.3565L24.5376 29.7851L24.5376 12.6426L33.4453 17.7854Z" fill="#0A0D12"/>
                    </svg>
                </x-slot>
            </flux:brand>
            <flux:brand href="/" class="max-lg:hidden! hidden dark:flex">
                <x-slot name="logo">
                    <svg class="h-full" width="34" height="48" viewBox="0 0 34 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.45659 17.2143L24.3027 8.64286L15.395 3.5L0.549161 12.0713L0.548828 29.2139L9.45652 34.3568L9.45659 17.2143Z" fill="#0A0D12"/>
                        <path d="M33.4453 17.7854L33.4453 34.9283L18.5991 43.4994L9.69141 38.3565L24.5376 29.7851L24.5376 12.6426L33.4453 17.7854Z" fill="#0A0D12"/>
                    </svg>
                </x-slot>
            </flux:brand>

            <flux:separator vertical class="max-lg:hidden lg:me-4" />

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item href="/headshots">
                    {{ __('Headshots') }}
                </flux:navbar.item>

                <flux:navbar.item href="/styles">
                    {{ __('Styles') }}
                </flux:navbar.item>

                <flux:navbar.item href="/photos">
                    {{ __('Photos') }}
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="me-4">
                <flux:navbar.item class="max-lg:hidden" icon="question-mark-circle" href="/contact" label="Help" />
            </flux:navbar>

            <!-- Desktop User Menu -->
            @auth
                <flux:dropdown position="top" align="end">
                    <flux:profile
                        :avatar="auth()->user()->avatar ?: null"
                        :initials="auth()->user()->initials()"
                        class="cursor-pointer"
                    />

                    <flux:menu>
                        <flux:menu.radio.group>
                            <div class="p-0 text-sm font-normal">
                                <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                    <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-sm">
                                        @if (auth()->user()->avatar)
                                            <img src="{{ auth()->user()->avatar }}" />
                                        @else
                                            <span
                                                class="flex h-full w-full items-center justify-center rounded-sm bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                            >
                                                {{ auth()->user()->initials() }}
                                            </span>
                                        @endif
                                    </span>

                                    <div class="grid flex-1 text-start text-sm leading-tight">
                                        <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                        <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                    </div>
                                </div>
                            </div>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <flux:menu.radio.group>
                            <flux:menu.item :href="route('settings.profile')" icon="cog">{{ __('Settings') }}</flux:menu.item>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                                {{ __('Log Out') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            @endauth
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar stashable sticky class="lg:hidden border-e border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <flux:brand href="/" class="px-1 dark:hidden">
                <x-slot name="logo">
                    <svg class="h-full" width="34" height="48" viewBox="0 0 34 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.45659 17.2143L24.3027 8.64286L15.395 3.5L0.549161 12.0713L0.548828 29.2139L9.45652 34.3568L9.45659 17.2143Z" fill="#0A0D12"/>
                        <path d="M33.4453 17.7854L33.4453 34.9283L18.5991 43.4994L9.69141 38.3565L24.5376 29.7851L24.5376 12.6426L33.4453 17.7854Z" fill="#0A0D12"/>
                    </svg>
                </x-slot>
            </flux:brand>
            <flux:brand href="/" class="px-1 hidden dark:flex">
                <x-slot name="logo">
                    <svg class="h-full" width="34" height="48" viewBox="0 0 34 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.45659 17.2143L24.3027 8.64286L15.395 3.5L0.549161 12.0713L0.548828 29.2139L9.45652 34.3568L9.45659 17.2143Z" fill="#0A0D12"/>
                        <path d="M33.4453 17.7854L33.4453 34.9283L18.5991 43.4994L9.69141 38.3565L24.5376 29.7851L24.5376 12.6426L33.4453 17.7854Z" fill="#0A0D12"/>
                    </svg>
                </x-slot>
            </flux:brand>

            <flux:navlist>
                <flux:navlist.group :heading="__('Platform')">
                    <flux:navlist.item href="/headshots">
                        {{ __('Headshots') }}
                    </flux:navlist.item>

                    <flux:navlist.item href="/styles">
                        {{ __('Styles') }}
                    </flux:navlist.item>

                    <flux:navlist.item href="/photos">
                        {{ __('Photos') }}
                    </flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            <flux:navlist>
                <flux:navlist.item icon="information-circle" href="/contact">{{ __('Help') }}</flux:navlist.item>
            </flux:navlist>
        </flux:sidebar>

        {{ $slot }}

        @fluxScripts
    </body>
</html>

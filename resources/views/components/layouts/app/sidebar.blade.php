<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        @fluxAppearance
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
            <flux:sidebar.header>
                <flux:sidebar.brand
                    href="{{ route('dashboard') }}"
                    name="{{ \App\Models\Organization::first()->short_name ?? 'Aozora Education' }}"
                    wire:navigate
                >
                    <x-app-logo />
                </flux:sidebar.brand>

                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                @if(auth()->user()->hasRole('admin'))
                    <flux:sidebar.item icon="home" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')" wire:navigate tooltip="{{ __('Dashboard') }}">{{ __('Dashboard') }}</flux:sidebar.item>
                @elseif(auth()->user()->hasRole('parent'))
                    <flux:sidebar.item icon="home" :href="route('parent.dashboard')" :current="request()->routeIs('parent.dashboard')" wire:navigate tooltip="{{ __('Dashboard') }}">{{ __('Dashboard') }}</flux:sidebar.item>
                @elseif(auth()->user()->hasRole('teacher'))
                    <flux:sidebar.item icon="home" :href="route('teacher.dashboard')" :current="request()->routeIs('teacher.dashboard')" wire:navigate tooltip="{{ __('Dashboard') }}">{{ __('Dashboard') }}</flux:sidebar.item>
                @elseif(auth()->user()->hasRole('student'))
                    <flux:sidebar.item icon="home" :href="route('student.dashboard')" :current="request()->routeIs('student.dashboard')" wire:navigate tooltip="{{ __('Dashboard') }}">{{ __('Dashboard') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="user" :href="route('profile.show')" :current="request()->routeIs('profile.show*')" wire:navigate tooltip="{{ __('Profile') }}">{{ __('Profile') }}</flux:sidebar.item>
                @else
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate tooltip="{{ __('Dashboard') }}">{{ __('Dashboard') }}</flux:sidebar.item>
                @endif

                @if(auth()->user()->hasRole('admin'))
                    <flux:sidebar.group expandable heading="{{ __('User Management') }}" icon="users">
                        <flux:sidebar.item icon="users" :href="route('admin.users')" :current="request()->routeIs('admin.users*')" wire:navigate tooltip="{{ __('Admin') }}">{{ __('Admin') }}</flux:sidebar.item>
                        <flux:sidebar.item icon="user-group" :href="route('admin.teachers')" :current="request()->routeIs('admin.teachers*')" wire:navigate tooltip="{{ __('Teachers') }}">{{ __('Teachers') }}</flux:sidebar.item>
                        <flux:sidebar.item icon="user-plus" :href="route('admin.parents')" :current="request()->routeIs('admin.parents*')" wire:navigate tooltip="{{ __('Parents') }}">{{ __('Parents') }}</flux:sidebar.item>
                        <flux:sidebar.item icon="academic-cap" :href="route('admin.students')" :current="request()->routeIs('admin.students*')" wire:navigate tooltip="{{ __('Students') }}">{{ __('Students') }}</flux:sidebar.item>
                        
                        
                        
                    </flux:sidebar.group>

                    <flux:sidebar.group expandable heading="{{ __('Academic') }}" icon="academic-cap">
                        <flux:sidebar.item icon="book-open" :href="route('admin.subjects')" :current="request()->routeIs('admin.subjects*')" wire:navigate tooltip="{{ __('Subjects') }}">{{ __('Subjects') }}</flux:sidebar.item>
                        <flux:sidebar.item icon="user-plus" :href="route('enrollment.index')" :current="request()->routeIs('enrollment*')" wire:navigate tooltip="{{ __('Enrollments') }}">{{ __('Enrollments') }}</flux:sidebar.item>
                        <flux:sidebar.item icon="clock" :href="route('admin.time-schedules.index')" :current="request()->routeIs('admin.time-schedules*')" wire:navigate tooltip="{{ __('Time Schedules') }}">{{ __('Time Schedules') }}</flux:sidebar.item>
                        <flux:sidebar.item icon="calendar" :href="route('admin.time-schedules.calendar-fullcalendar')" :current="request()->routeIs('admin.time-schedules.calendar-fullcalendar')" tooltip="{{ __('Schedule Calendar') }}">{{ __('Schedule Calendar') }}</flux:sidebar.item>
                    </flux:sidebar.group>

                    <flux:sidebar.group expandable heading="{{ __('System') }}" icon="cog-6-tooth">
                        <flux:sidebar.item icon="building-office-2" :href="route('admin.organization')" :current="request()->routeIs('admin.organization*')" wire:navigate tooltip="{{ __('Organization') }}">{{ __('Organization') }}</flux:sidebar.item>
                        <flux:sidebar.item icon="shield-check" :href="route('admin.roles')" :current="request()->routeIs('admin.roles*')" wire:navigate tooltip="{{ __('Roles') }}">{{ __('Roles') }}</flux:sidebar.item>
                        <flux:sidebar.item icon="document-text" :href="route('admin.document-numbering.index')" :current="request()->routeIs('admin.document-numbering*')" wire:navigate tooltip="{{ __('Document Numbering') }}">{{ __('Document Numbering') }}</flux:sidebar.item>
                    </flux:sidebar.group>
                @endif
            </flux:sidebar.nav>

            <flux:sidebar.spacer />

            <!-- Desktop User Menu -->
            <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon:trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
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
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
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
        </flux:sidebar>

        <!-- Mobile Header -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
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
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
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
        </flux:header>

        {{ $slot }}

        <div class="fixed bottom-4 right-4 z-50">
            <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" icon="moon" variant="subtle" aria-label="Toggle dark mode" />
        </div>

        @livewireScripts
        @fluxScripts
    </body>
</html>
<?php

use App\Livewire\Actions\Logout;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    #[On('logout-requested')]
    public function logout(Logout $logout): void
    {
        $logout();

        session()->flash('status', 'You have been logged out successfully.');

        $this->redirect(route('login'), navigate: true);
    }
}; ?>

<div class="contents">
    <!-- Mobile overlay -->
    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 bg-black/50 z-30 lg:hidden" @click="sidebarOpen = false"></div>

    <aside
        x-cloak
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed z-40 inset-y-0 left-0 w-64 shrink-0 bg-gradient-to-b from-gray-900 to-black text-gray-200 flex flex-col transition-transform duration-200 ease-in-out lg:static lg:translate-x-0"
    >
        <div class="h-16 flex items-center gap-3 px-5 border-b border-white/10">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 text-white font-semibold text-lg">
                <img src="{{ asset('images/odn-icon.jpg') }}" alt="ODN" class="h-9 w-9 rounded-lg shrink-0">
                <span class="leading-tight">
                    <span class="block">{{ config('app.name') }}</span>
                    <span class="block text-[11px] font-normal text-gray-400 tracking-wide">by ODN Digital</span>
                </span>
            </a>
        </div>

        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 text-sm">
            <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <x-slot:icon>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 13h4v7H3v-7zM10 4h4v16h-4V4zM17 9h4v11h-4V9z" />
                    </svg>
                </x-slot:icon>
                {{ __('Dashboard') }}
            </x-sidebar-link>

            @canany(['manage-assets', 'manage-assignments', 'manage-maintenances'])
                <x-sidebar-link :href="route('assets.index')" :active="request()->routeIs('assets.*')">
                    <x-slot:icon>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25M21 7.5v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                        </svg>
                    </x-slot:icon>
                    {{ __('Assets') }}
                </x-sidebar-link>
                <x-sidebar-link :href="route('assignments.index')" :active="request()->routeIs('assignments.*')">
                    <x-slot:icon>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 3l4 4-4 4M20 7H8m0 10l-4-4 4-4m-4 4h12" />
                        </svg>
                    </x-slot:icon>
                    {{ __('Assign Assets') }}
                </x-sidebar-link>
                <x-sidebar-link :href="route('maintenances.index')" :active="request()->routeIs('maintenances.*')">
                    <x-slot:icon>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 4.5a3.5 3.5 0 104.596 4.596l4.652 4.652a1 1 0 001.414-1.414l-4.652-4.652A3.5 3.5 0 0011 4.5zM6 17l1.5 1.5L6 20l-1.5-1.5L6 17z" />
                        </svg>
                    </x-slot:icon>
                    {{ __('Maintenance') }}
                </x-sidebar-link>
            @endcanany

            <x-sidebar-link :href="route('my-assets')" :active="request()->routeIs('my-assets')">
                <x-slot:icon>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5z" />
                    </svg>
                </x-slot:icon>
                {{ __('My Assets') }}
            </x-sidebar-link>

            @canany(['manage-departments', 'manage-locations', 'manage-categories'])
                <div class="pt-4 pb-1 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Master Data') }}</div>
                <x-sidebar-link :href="route('departments.index')" :active="request()->routeIs('departments.*')">
                    <x-slot:icon>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21V5a1 1 0 011-1h9a1 1 0 011 1v16M3 21h18M9 21v-4h2v4M8 8h1m3 0h1m-5 4h1m3 0h1m3-8h2a1 1 0 011 1v14" />
                        </svg>
                    </x-slot:icon>
                    {{ __('Departments') }}
                </x-sidebar-link>
                <x-sidebar-link :href="route('locations.index')" :active="request()->routeIs('locations.*')">
                    <x-slot:icon>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </x-slot:icon>
                    {{ __('Locations') }}
                </x-sidebar-link>
                <x-sidebar-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                    <x-slot:icon>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M3 7.5v5.379a2 2 0 00.586 1.414l7.121 7.121a2 2 0 002.828 0l5.586-5.586a2 2 0 000-2.828l-7.121-7.121A2 2 0 0010.586 5H5.5A2.5 2.5 0 003 7.5z" />
                        </svg>
                    </x-slot:icon>
                    {{ __('Categories') }}
                </x-sidebar-link>
            @endcanany

            @can('view-reports')
                <div class="pt-4 pb-1 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Reports') }}</div>
                <x-sidebar-link :href="route('reports.depreciation')" :active="request()->routeIs('reports.depreciation')">
                    <x-slot:icon>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3v18h18M7 15l3-3 3 3 5-6" />
                        </svg>
                    </x-slot:icon>
                    {{ __('Depreciation') }}
                </x-sidebar-link>
            @endcan

            @can('manage-users')
                <div class="pt-4 pb-1 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Administration') }}</div>
                <x-sidebar-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                    <x-slot:icon>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-1a4 4 0 00-3-3.87M9 20H4v-1a4 4 0 013-3.87m5-5.13a4 4 0 100-8 4 4 0 000 8zm6 1a4 4 0 10-3-6.65" />
                        </svg>
                    </x-slot:icon>
                    {{ __('Users & Roles') }}
                </x-sidebar-link>
            @endcan
        </nav>

        <div class="border-t border-white/10 p-3">
            <div class="flex items-center gap-3 px-2 py-2">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-indigo-500 text-white font-semibold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
                <div class="min-w-0 flex-1">
                    <div class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-gray-400 truncate">{{ auth()->user()->getRoleNames()->first() }}</div>
                </div>
            </div>
        </div>
    </aside>
</div>

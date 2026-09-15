<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/jpeg" href="{{ asset('images/odn-icon.jpg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-gray-100 overflow-hidden">

            <livewire:layout.navigation />

            <div class="flex-1 flex flex-col overflow-hidden min-w-0">
                <!-- Topbar -->
                <header class="bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 py-3 shrink-0">
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = true" class="lg:hidden text-gray-500">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        @isset($header)
                            <div class="text-lg font-semibold text-gray-800">{{ $header }}</div>
                        @endisset
                    </div>

                    <x-dropdown align="right" width="w-56">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2 px-2 py-1.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition">
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 text-indigo-700 font-semibold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>
                                <span class="hidden sm:flex flex-col items-start leading-tight">
                                    <span>{{ auth()->user()->name }}</span>
                                    <span class="text-xs font-normal text-gray-400">{{ auth()->user()->getRoleNames()->first() }}</span>
                                </span>
                                <svg class="h-4 w-4 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile')" wire:navigate>
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <button x-on:click="$dispatch('logout-requested')" class="w-full text-start">
                                <x-dropdown-link>
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </button>
                        </x-slot>
                    </x-dropdown>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto overflow-x-hidden min-w-0 p-4 sm:p-6">
                    @if (session('welcome'))
                        <div class="mb-4 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-700 px-4 py-2 text-sm">
                            {{ session('welcome') }}
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>

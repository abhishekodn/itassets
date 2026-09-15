<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/jpeg" href="{{ asset('images/odn-icon.jpg') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex bg-gray-50">
            <!-- Branding panel -->
            <div class="hidden lg:flex lg:w-1/2 relative flex-col justify-between bg-gray-900 text-white p-12 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-700 via-gray-900 to-gray-900"></div>
                <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-indigo-500/20 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-80 h-80 rounded-full bg-indigo-400/10 blur-3xl"></div>

                <div class="relative flex items-center gap-3 text-lg font-semibold">
                    <img src="{{ asset('images/odn-icon.jpg') }}" alt="ODN" class="h-10 w-10 rounded-lg shrink-0">
                    <span class="leading-tight">
                        <span class="block">{{ config('app.name') }}</span>
                        <span class="block text-xs font-normal text-indigo-200/70 tracking-wide">by ODN Digital</span>
                    </span>
                </div>

                <div class="relative max-w-md">
                    <h1 class="text-3xl font-bold leading-tight mb-4">
                        Track every asset, from purchase to retirement.
                    </h1>
                    <p class="text-indigo-100/80 mb-8">
                        One place to register equipment, check assets out to employees, log maintenance, and see depreciation in real time.
                    </p>

                    <ul class="space-y-3 text-sm text-indigo-100/90">
                        <li class="flex items-center gap-3">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-white/10">✓</span>
                            Full asset registry with QR tags
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-white/10">✓</span>
                            Check-in / check-out tracking per employee
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-white/10">✓</span>
                            Maintenance schedules & depreciation reports
                        </li>
                    </ul>
                </div>

                <div class="relative text-xs text-indigo-200/60">
                    &copy; {{ date('Y') }} ODN Digital. All rights reserved.
                </div>
            </div>

            <!-- Form panel -->
            <div class="flex flex-1 flex-col justify-center items-center px-6 py-12 sm:px-10">
                <div class="w-full max-w-sm">
                    <div class="flex lg:hidden items-center gap-3 justify-center mb-8 text-gray-900 font-semibold text-lg">
                        <img src="{{ asset('images/odn-icon.jpg') }}" alt="ODN" class="h-9 w-9 rounded-lg shrink-0">
                        {{ config('app.name') }}
                    </div>

                    <div class="bg-white sm:shadow-xl sm:rounded-2xl sm:border sm:border-gray-100 sm:p-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

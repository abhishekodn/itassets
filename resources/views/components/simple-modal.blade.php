@props(['maxWidth' => '2xl'])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
    '4xl' => 'sm:max-w-4xl',
][$maxWidth];
@endphp

<div
    class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
    x-data x-on:keydown.escape.window="$wire.set('showModal', false)"
    x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
>
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" wire:click="$set('showModal', false)"></div>

    <div
        class="relative mb-6 bg-white rounded-2xl overflow-hidden shadow-2xl ring-1 ring-gray-900/5 transform transition-all sm:w-full {{ $maxWidth }} sm:mx-auto"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
    >
        {{ $slot }}
    </div>
</div>

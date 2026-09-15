@props(['active' => false])

@php
$classes = $active
    ? 'bg-white/10 text-white'
    : 'text-gray-400 hover:bg-white/5 hover:text-white';
@endphp

<a {{ $attributes->merge(['class' => "group relative flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition $classes"]) }} wire:navigate>
    @if ($active)
        <span class="absolute left-0 top-1.5 bottom-1.5 w-0.5 rounded-full bg-indigo-400"></span>
    @endif

    @isset($icon)
        <span class="shrink-0 {{ $active ? 'text-indigo-400' : 'text-gray-500 group-hover:text-gray-300' }}">
            {{ $icon }}
        </span>
    @endisset

    <span class="truncate">{{ $slot }}</span>
</a>

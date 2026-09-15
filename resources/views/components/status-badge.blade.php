@props(['status'])

@php
$colors = [
    'available' => 'bg-green-100 text-green-700',
    'assigned' => 'bg-indigo-100 text-indigo-700',
    'in_maintenance' => 'bg-amber-100 text-amber-700',
    'retired' => 'bg-gray-100 text-gray-600',
    'disposed' => 'bg-red-100 text-red-700',
    'completed' => 'bg-green-100 text-green-700',
    'scheduled' => 'bg-amber-100 text-amber-700',
][$status] ?? 'bg-gray-100 text-gray-600';
@endphp

<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $colors }}">
    {{ str($status)->headline() }}
</span>

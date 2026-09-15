<div>
    <div class="flex items-center justify-between flex-wrap gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Dashboard</h1>
            <p class="text-sm text-gray-500 mt-0.5">Welcome back, {{ auth()->user()->name }}. Here's what's happening with your assets.</p>
        </div>

        @canany(['manage-assets', 'manage-assignments', 'manage-maintenances'])
            <div class="flex flex-wrap gap-2">
                @can('manage-assets')
                    <a href="{{ route('assets.index', ['new' => 1]) }}" wire:navigate
                       class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-gray-700 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Add Asset
                    </a>
                @endcan
                @can('manage-assignments')
                    <a href="{{ route('assignments.index', ['new' => 1]) }}" wire:navigate
                       class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        Check In Asset
                    </a>
                @endcan
                @can('manage-maintenances')
                    <a href="{{ route('maintenances.index', ['new' => 1]) }}" wire:navigate
                       class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4.5a3.5 3.5 0 104.596 4.596l4.652 4.652a1 1 0 001.414-1.414l-4.652-4.652A3.5 3.5 0 0011 4.5z" /></svg>
                        Log Maintenance
                    </a>
                @endcan
            </div>
        @endcanany
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        @php
        $cards = [
            ['label' => 'Total Assets', 'value' => number_format($stats['total_assets']), 'accent' => 'bg-gray-900', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
            ['label' => 'Total Value', 'value' => '$'.number_format($stats['total_value'], 0), 'accent' => 'bg-emerald-600', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 10v2m0-2c-1.11 0-2.08-.402-2.599-1M12 22a10 10 0 100-20 10 10 0 000 20z'],
            ['label' => 'Available', 'value' => number_format($stats['available']), 'accent' => 'bg-green-600', 'icon' => 'M5 13l4 4L19 7'],
            ['label' => 'Assigned', 'value' => number_format($stats['assigned']), 'accent' => 'bg-indigo-600', 'icon' => 'M17 20h5v-1a4 4 0 00-3-3.87M9 20H4v-1a4 4 0 013-3.87m5-5.13a4 4 0 100-8 4 4 0 000 8z'],
            ['label' => 'In Maintenance', 'value' => number_format($stats['in_maintenance']), 'accent' => 'bg-amber-500', 'icon' => 'M11 4.5a3.5 3.5 0 104.596 4.596l4.652 4.652a1 1 0 001.414-1.414l-4.652-4.652A3.5 3.5 0 0011 4.5z'],
            ['label' => 'Retired/Disposed', 'value' => number_format($stats['retired']), 'accent' => 'bg-gray-400', 'icon' => 'M6 18L18 6M6 6l12 12'],
        ];
        @endphp

        @foreach ($cards as $card)
            <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs uppercase tracking-wide text-gray-500">{{ $card['label'] }}</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg {{ $card['accent'] }} text-white">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}" />
                        </svg>
                    </span>
                </div>
                <div class="text-2xl font-semibold text-gray-800">{{ $card['value'] }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-5">
            <h2 class="font-medium text-gray-800 mb-4">Assets by Category</h2>
            @forelse ($byCategory as $row)
                <div class="mb-3 last:mb-0">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">{{ $row->category?->name ?? 'Uncategorized' }}</span>
                        <span class="font-medium text-gray-800">{{ $row->total }}</span>
                    </div>
                    <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                        <div class="h-full rounded-full bg-indigo-500" style="width: {{ round($row->total / $maxCategoryTotal * 100) }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400">No assets yet.</p>
            @endforelse
        </div>

        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-5">
            <h2 class="font-medium text-gray-800 mb-4">Status Breakdown</h2>
            @if ($totalAssets > 0)
                <div class="h-3 w-full rounded-full overflow-hidden flex mb-4">
                    @foreach ($statusBreakdown as $row)
                        <div class="{{ $row['color'] }} h-full" style="width: {{ round($row['total'] / $totalAssets * 100) }}%"></div>
                    @endforeach
                </div>
                <div class="space-y-2">
                    @foreach ($statusBreakdown as $row)
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-2 text-gray-600">
                                <span class="h-2.5 w-2.5 rounded-full {{ $row['color'] }}"></span>
                                {{ $row['label'] }}
                            </span>
                            <span class="font-medium text-gray-800">{{ $row['total'] }} <span class="text-gray-400 font-normal">({{ round($row['total'] / $totalAssets * 100) }}%)</span></span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-400">No assets yet.</p>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-5">
            <h2 class="font-medium text-gray-800 mb-4">Recent Activity</h2>
            <ul class="space-y-4">
                @forelse ($activity as $event)
                    @php
                    $iconMap = [
                        'added' => ['bg-gray-100 text-gray-600', 'M12 4v16m8-8H4'],
                        'checkout' => ['bg-indigo-100 text-indigo-600', 'M17 16l4-4m0 0l-4-4m4 4H7'],
                        'checkin' => ['bg-green-100 text-green-600', 'M7 16l-4-4m0 0l4-4m-4 4h18'],
                        'maintenance' => ['bg-amber-100 text-amber-600', 'M11 4.5a3.5 3.5 0 104.596 4.596l4.652 4.652a1 1 0 001.414-1.414l-4.652-4.652A3.5 3.5 0 0011 4.5z'],
                    ];
                    [$iconClasses, $iconPath] = $iconMap[$event['type']];
                    @endphp
                    <li class="flex gap-3">
                        <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full {{ $iconClasses }}">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}" />
                            </svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <a href="{{ $event['url'] }}" wire:navigate class="text-sm text-gray-700 hover:text-indigo-600 leading-snug break-words">{{ $event['title'] }}</a>
                            <div class="text-xs text-gray-400">{{ $event['subtitle'] }} &middot; {{ $event['time']->diffForHumans() }}</div>
                        </div>
                    </li>
                @empty
                    <p class="text-sm text-gray-400">No recent activity.</p>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-5">
            <h2 class="font-medium text-gray-800 mb-4">Maintenance Due (30 days)</h2>
            @forelse ($upcomingMaintenance as $m)
                <div class="flex items-center justify-between py-2 text-sm border-b last:border-0">
                    <div>
                        <a href="{{ route('assets.show', $m->asset_id) }}" wire:navigate class="font-medium text-gray-700 hover:text-indigo-600">{{ $m->asset->name }}</a>
                        <span class="text-gray-400">({{ $m->asset->asset_tag }})</span>
                    </div>
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $m->next_due_at->isPast() ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                        {{ $m->next_due_at->isPast() ? 'Overdue' : 'Due' }} {{ $m->next_due_at->format('M j, Y') }}
                    </span>
                </div>
            @empty
                <p class="text-sm text-gray-400">Nothing due soon.</p>
            @endforelse
        </div>

        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-5">
            <h2 class="font-medium text-gray-800 mb-4">Warranty Expiring (60 days)</h2>
            @forelse ($expiringWarranty as $asset)
                <div class="flex items-center justify-between py-2 text-sm border-b last:border-0">
                    <div>
                        <a href="{{ route('assets.show', $asset) }}" wire:navigate class="font-medium text-gray-700 hover:text-indigo-600">{{ $asset->name }}</a>
                        <span class="text-gray-400">({{ $asset->asset_tag }})</span>
                    </div>
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">
                        Expires {{ $asset->warranty_expiry->format('M j, Y') }}
                    </span>
                </div>
            @empty
                <p class="text-sm text-gray-400">Nothing expiring soon.</p>
            @endforelse
        </div>
    </div>
</div>

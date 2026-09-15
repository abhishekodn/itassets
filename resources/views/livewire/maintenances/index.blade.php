<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Maintenance</h1>
        <x-primary-button wire:click="create">+ Log Maintenance</x-primary-button>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-2 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
        <div class="p-4 border-b flex flex-wrap gap-3 items-center">
            <x-text-input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by asset..." class="w-full max-w-sm" />

            <select wire:model.live="statusFilter" class="rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All statuses</option>
                <option value="completed">Completed</option>
                <option value="scheduled">Scheduled</option>
            </select>
        </div>

        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Asset</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Type</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Performed</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Next Due</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Cost</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($maintenances as $maintenance)
                    <tr class="hover:bg-gray-50/70 transition-colors" wire:key="maint-{{ $maintenance->id }}">
                        <td class="px-4 py-3">
                            <a href="{{ route('assets.show', $maintenance->asset_id) }}" wire:navigate class="font-medium text-gray-800 hover:text-indigo-600">
                                {{ $maintenance->asset->name }}
                            </a>
                        </td>
                        <td class="px-4 py-3">{{ str($maintenance->type)->headline() }}</td>
                        <td class="px-4 py-3">{{ $maintenance->performed_at->format('M j, Y') }}</td>
                        <td class="px-4 py-3">{{ $maintenance->next_due_at?->format('M j, Y') ?? '—' }}</td>
                        <td class="px-4 py-3">${{ number_format($maintenance->cost, 2) }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$maintenance->status" /></td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="delete({{ $maintenance->id }})" wire:confirm="Delete this record?" class="text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                @empty
                    <x-empty-state colspan="7" message="No maintenance records found." />
                @endforelse
            </tbody>
        </table>
        </div>

        <div class="p-4">{{ $maintenances->links() }}</div>
    </div>

    @if ($showModal)
        <x-simple-modal maxWidth="md">
            <form wire:submit="save" class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Log Maintenance</h2>

                <div class="space-y-4">
                    <div>
                        <x-input-label for="asset_id" value="Asset" />
                        <select id="asset_id" wire:model="asset_id" class="w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">— Select an asset —</option>
                            @foreach ($assets as $asset)
                                <option value="{{ $asset->id }}">{{ $asset->name }} ({{ $asset->asset_tag }})</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('asset_id')" class="mt-1" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="type" value="Type" />
                            <select id="type" wire:model="type" class="w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="repair">Repair</option>
                                <option value="service">Service</option>
                                <option value="inspection">Inspection</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="status" value="Status" />
                            <select id="status" wire:model="status" class="w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="completed">Completed</option>
                                <option value="scheduled">Scheduled</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <x-input-label for="description" value="Description" />
                        <textarea id="description" wire:model="description" rows="2" class="w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="performed_at" value="Performed On" />
                            <x-text-input id="performed_at" type="date" wire:model="performed_at" class="w-full mt-1" />
                        </div>
                        <div>
                            <x-input-label for="cost" value="Cost" />
                            <x-text-input id="cost" type="number" step="0.01" wire:model="cost" class="w-full mt-1" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="next_due_at" value="Next Due (optional)" />
                        <x-text-input id="next_due_at" type="date" wire:model="next_due_at" class="w-full mt-1" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button type="button" wire:click="$set('showModal', false)">Cancel</x-secondary-button>
                    <x-primary-button type="submit">Save</x-primary-button>
                </div>
            </form>
        </x-simple-modal>
    @endif
</div>

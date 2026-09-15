<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Assign Assets</h1>
        <x-primary-button wire:click="create">+ Check In Asset</x-primary-button>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-2 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
        <div class="p-4 border-b flex flex-wrap gap-3 items-center">
            <x-text-input type="text" wire:model.live.debounce.300ms="search" placeholder="Search asset or user..." class="w-full max-w-sm" />

            <select wire:model.live="statusFilter" class="rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="current">Currently checked in</option>
                <option value="returned">Checked out</option>
                <option value="">All</option>
            </select>
        </div>

        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Asset</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Checked In To</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Checked In On</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Checked Out</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($assignments as $assignment)
                    <tr class="hover:bg-gray-50/70 transition-colors" wire:key="assign-{{ $assignment->id }}">
                        <td class="px-4 py-3">
                            <a href="{{ route('assets.show', $assignment->asset_id) }}" wire:navigate class="font-medium text-gray-800 hover:text-indigo-600">
                                {{ $assignment->asset->name }}
                            </a>
                            <div class="text-xs text-gray-400">{{ $assignment->asset->asset_tag }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $assignment->user->name }}</td>
                        <td class="px-4 py-3">{{ $assignment->assigned_at->format('M j, Y') }}</td>
                        <td class="px-4 py-3">{{ $assignment->returned_at?->format('M j, Y') ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            @if (! $assignment->returned_at)
                                <button wire:click="confirmReturn({{ $assignment->id }})" class="text-indigo-600 hover:underline">Check Out</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <x-empty-state colspan="5" message="No check-ins found." />
                @endforelse
            </tbody>
        </table>
        </div>

        <div class="p-4">{{ $assignments->links() }}</div>
    </div>

    @if ($showModal)
        <x-simple-modal maxWidth="md">
            <form wire:submit="save" class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Check In Asset</h2>

                <div class="space-y-4">
                    <div>
                        <x-input-label for="asset_id" value="Asset" />
                        <select id="asset_id" wire:model="asset_id" class="w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">— Select an available asset —</option>
                            @foreach ($availableAssets as $asset)
                                <option value="{{ $asset->id }}">{{ $asset->name }} ({{ $asset->asset_tag }})</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('asset_id')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="user_id" value="Check In To" />
                        <select id="user_id" wire:model="user_id" class="w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">— Select a user —</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('user_id')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="assigned_at" value="Check-In Date" />
                        <x-text-input id="assigned_at" type="date" wire:model="assigned_at" class="w-full mt-1" />
                    </div>
                    <div>
                        <x-input-label for="condition_on_assign" value="Condition" />
                        <x-text-input id="condition_on_assign" wire:model="condition_on_assign" class="w-full mt-1" />
                    </div>
                    <div>
                        <x-input-label for="notes" value="Notes" />
                        <textarea id="notes" wire:model="notes" rows="2" class="w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button type="button" wire:click="$set('showModal', false)">Cancel</x-secondary-button>
                    <x-primary-button type="submit">Check In</x-primary-button>
                </div>
            </form>
        </x-simple-modal>
    @endif

    @if ($showReturnModal)
        <x-simple-modal maxWidth="md">
            <form wire:submit="returnAsset" class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Check Out Asset</h2>

                <div class="space-y-4">
                    <div>
                        <x-input-label for="condition_on_return" value="Condition on Return" />
                        <x-text-input id="condition_on_return" wire:model="condition_on_return" class="w-full mt-1" />
                    </div>
                    <div>
                        <x-input-label for="return_notes" value="Notes" />
                        <textarea id="return_notes" wire:model="return_notes" rows="2" class="w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button type="button" wire:click="$set('showReturnModal', false)">Cancel</x-secondary-button>
                    <x-primary-button type="submit">Confirm Check Out</x-primary-button>
                </div>
            </form>
        </x-simple-modal>
    @endif
</div>

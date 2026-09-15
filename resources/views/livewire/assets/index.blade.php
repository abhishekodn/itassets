<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Assets</h1>
        <x-primary-button wire:click="create">+ Add Asset</x-primary-button>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-2 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
        <div class="p-4 border-b flex flex-wrap gap-3 items-center">
            <x-text-input type="text" wire:model.live.debounce.300ms="search" placeholder="Search name, tag, serial..." class="w-full max-w-sm" />

            <select wire:model.live="statusFilter" class="rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All statuses</option>
                <option value="available">Available</option>
                <option value="assigned">Assigned</option>
                <option value="in_maintenance">In Maintenance</option>
                <option value="retired">Retired</option>
                <option value="disposed">Disposed</option>
            </select>

            <select wire:model.live="categoryFilter" class="rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Asset</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Tag</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Category</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Location</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Book Value</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($assets as $asset)
                    <tr class="hover:bg-gray-50/70 transition-colors" wire:key="asset-{{ $asset->id }}">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if ($asset->image)
                                    <img src="{{ Storage::url($asset->image) }}" class="h-8 w-8 rounded object-cover">
                                @else
                                    <span class="h-8 w-8 rounded bg-gray-100 flex items-center justify-center text-gray-400 text-xs">—</span>
                                @endif
                                <a href="{{ route('assets.show', $asset) }}" wire:navigate class="font-medium text-gray-800 hover:text-indigo-600">{{ $asset->name }}</a>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $asset->asset_tag }}</td>
                        <td class="px-4 py-3">{{ $asset->category?->name }}</td>
                        <td class="px-4 py-3">{{ $asset->location?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <x-status-badge :status="$asset->status" />
                        </td>
                        <td class="px-4 py-3">${{ number_format($asset->current_book_value, 2) }}</td>
                        <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                            <button wire:click="edit({{ $asset->id }})" class="text-indigo-600 hover:underline">Edit</button>
                            <button wire:click="delete({{ $asset->id }})" wire:confirm="Delete this asset?" class="text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                @empty
                    <x-empty-state colspan="7" message="No assets found. Add your first asset to get started." />
                @endforelse
            </tbody>
        </table>
        </div>

        <div class="p-4">{{ $assets->links() }}</div>
    </div>

    @if ($showModal)
        <x-simple-modal maxWidth="2xl">
            <form wire:submit="save" class="p-6 max-h-[80vh] overflow-y-auto">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ $editingId ? 'Edit Asset' : 'Add Asset' }} <span class="text-gray-400 font-normal text-sm">{{ $asset_tag }}</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <x-input-label for="name" value="Name" />
                        <x-text-input id="name" wire:model="name" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="asset_category_id" value="Category" />
                        <select id="asset_category_id" wire:model="asset_category_id" class="w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">— Select —</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('asset_category_id')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="status" value="Status" />
                        <select id="status" wire:model="status" class="w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="available">Available</option>
                            <option value="assigned">Assigned</option>
                            <option value="in_maintenance">In Maintenance</option>
                            <option value="retired">Retired</option>
                            <option value="disposed">Disposed</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="brand" value="Brand" />
                        <x-text-input id="brand" wire:model="brand" class="w-full mt-1" />
                    </div>
                    <div>
                        <x-input-label for="model" value="Model" />
                        <x-text-input id="model" wire:model="model" class="w-full mt-1" />
                    </div>

                    <div>
                        <x-input-label for="serial_number" value="Serial Number" />
                        <x-text-input id="serial_number" wire:model="serial_number" class="w-full mt-1" />
                    </div>
                    <div>
                        <x-input-label for="supplier" value="Supplier" />
                        <x-text-input id="supplier" wire:model="supplier" class="w-full mt-1" />
                    </div>

                    <div>
                        <x-input-label for="purchase_date" value="Purchase Date" />
                        <x-text-input id="purchase_date" type="date" wire:model="purchase_date" class="w-full mt-1" />
                    </div>
                    <div>
                        <x-input-label for="purchase_cost" value="Purchase Cost" />
                        <x-text-input id="purchase_cost" type="number" step="0.01" wire:model="purchase_cost" class="w-full mt-1" />
                    </div>

                    <div>
                        <x-input-label for="warranty_expiry" value="Warranty Expiry" />
                        <x-text-input id="warranty_expiry" type="date" wire:model="warranty_expiry" class="w-full mt-1" />
                    </div>
                    <div></div>

                    <div>
                        <x-input-label for="department_id" value="Department" />
                        <select id="department_id" wire:model="department_id" class="w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">— None —</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="location_id" value="Location" />
                        <select id="location_id" wire:model="location_id" class="w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">— None —</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <x-input-label for="image" value="Photo" />
                        <input type="file" id="image" wire:model="image" class="w-full mt-1 text-sm" accept="image/*">
                        <x-input-error :messages="$errors->get('image')" class="mt-1" />
                        @if ($image)
                            <img src="{{ $image->temporaryUrl() }}" class="h-16 mt-2 rounded">
                        @elseif ($existingImage)
                            <img src="{{ Storage::url($existingImage) }}" class="h-16 mt-2 rounded">
                        @endif
                    </div>

                    <div class="sm:col-span-2">
                        <x-input-label for="notes" value="Notes" />
                        <textarea id="notes" wire:model="notes" rows="2" class="w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
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

<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Asset Categories</h1>
        <x-primary-button wire:click="create">+ Add Category</x-primary-button>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-2 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
        <div class="p-4 border-b">
            <x-text-input type="text" wire:model.live.debounce.300ms="search" placeholder="Search categories..." class="w-full max-w-sm" />
        </div>

        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Name</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Useful Life (years)</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Assets</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($categories as $category)
                    <tr class="hover:bg-gray-50/70 transition-colors" wire:key="cat-{{ $category->id }}">
                        <td class="px-4 py-3">{{ $category->name }}</td>
                        <td class="px-4 py-3">{{ $category->useful_life_years }}</td>
                        <td class="px-4 py-3">{{ $category->assets_count }}</td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <button wire:click="edit({{ $category->id }})" class="text-indigo-600 hover:underline">Edit</button>
                            <button wire:click="delete({{ $category->id }})" wire:confirm="Delete this category?" class="text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                @empty
                    <x-empty-state colspan="4" message="No categories found." />
                @endforelse
            </tbody>
        </table>
        </div>

        <div class="p-4">{{ $categories->links() }}</div>
    </div>

    @if ($showModal)
        <x-simple-modal maxWidth="md">
            <form wire:submit="save" class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ $editingId ? 'Edit Category' : 'Add Category' }}
                </h2>

                <div class="space-y-4">
                    <div>
                        <x-input-label for="name" value="Name" />
                        <x-text-input id="name" wire:model="name" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="useful_life_years" value="Useful Life (years)" />
                        <x-text-input id="useful_life_years" type="number" min="1" wire:model="useful_life_years" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('useful_life_years')" class="mt-1" />
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

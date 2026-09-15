<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Users & Roles</h1>
        <x-primary-button wire:click="create">+ Add User</x-primary-button>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-2 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-2 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
        <div class="p-4 border-b">
            <x-text-input type="text" wire:model.live.debounce.300ms="search" placeholder="Search users..." class="w-full max-w-sm" />
        </div>

        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Name</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Employee Code</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Email</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Role</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Assets</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($users as $user)
                    <tr class="hover:bg-gray-50/70 transition-colors" wire:key="user-{{ $user->id }}">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $user->employee_code ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            <select
                                class="rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                @if ($user->id === auth()->id()) disabled @endif
                                onchange="@this.call('updateRole', {{ $user->id }}, this.value)"
                            >
                                @foreach ($roles as $roleOption)
                                    <option value="{{ $roleOption }}" @selected($user->hasRole($roleOption))>{{ ucfirst($roleOption) }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            @if ($user->active_assets_count > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                                    {{ $user->active_assets_count }} assigned
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">None</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="assignAsset({{ $user->id }})" class="text-indigo-600 hover:underline">Assign Asset</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>

        <div class="p-4">{{ $users->links() }}</div>
    </div>

    @if ($showModal)
        <x-simple-modal maxWidth="md">
            <form wire:submit="save" class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Add User</h2>

                <div class="space-y-4">
                    <div>
                        <x-input-label for="name" value="Name" />
                        <x-text-input id="name" wire:model="name" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="employee_code" value="Employee Code (optional)" />
                        <x-text-input id="employee_code" wire:model="employee_code" class="w-full mt-1" placeholder="e.g. EMP-0042" />
                        <x-input-error :messages="$errors->get('employee_code')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" type="email" wire:model="email" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="password" value="Password" />
                        <x-text-input id="password" type="password" wire:model="password" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="role" value="Role" />
                        <select id="role" wire:model="role" class="w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach ($roles as $roleOption)
                                <option value="{{ $roleOption }}">{{ ucfirst($roleOption) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button type="button" wire:click="$set('showModal', false)">Cancel</x-secondary-button>
                    <x-primary-button type="submit">Create</x-primary-button>
                </div>
            </form>
        </x-simple-modal>
    @endif

    @if ($showAssignModal)
        <x-simple-modal maxWidth="md">
            <form wire:submit="checkOut" class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-1">Assign Asset</h2>
                <p class="text-sm text-gray-500 mb-4">Check in an asset to <span class="font-medium text-gray-700">{{ $assignUserName }}</span> so they can get to work.</p>

                <div class="space-y-4">
                    <div>
                        <x-input-label for="asset_id" value="Asset" />
                        <select id="asset_id" wire:model="asset_id" class="w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">— Select an available asset —</option>
                            @forelse ($availableAssets as $asset)
                                <option value="{{ $asset->id }}">{{ $asset->name }} ({{ $asset->asset_tag }})</option>
                            @empty
                                <option value="" disabled>No available assets</option>
                            @endforelse
                        </select>
                        <x-input-error :messages="$errors->get('asset_id')" class="mt-1" />
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
                        <x-input-label for="assign_notes" value="Notes" />
                        <textarea id="assign_notes" wire:model="assign_notes" rows="2" class="w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button type="button" wire:click="$set('showAssignModal', false)">Skip for now</x-secondary-button>
                    <x-primary-button type="submit">Check In</x-primary-button>
                </div>
            </form>
        </x-simple-modal>
    @endif
</div>

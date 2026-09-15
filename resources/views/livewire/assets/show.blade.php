<div>
    <style>
        @media print {
            body * { visibility: hidden; }
            #print-label, #print-label * { visibility: visible; }
            #print-label { position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; }
        }
    </style>

    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('assets.index') }}" wire:navigate class="text-sm text-indigo-600 hover:underline">&larr; Back to Assets</a>
            <h1 class="text-2xl font-semibold text-gray-800 mt-1">{{ $asset->name }}</h1>
            <div class="text-gray-500 text-sm">{{ $asset->asset_tag }}</div>
        </div>
        <x-status-badge :status="$asset->status" />
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-2 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-5 flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h2 class="font-medium text-gray-800 mb-1">Custody</h2>
                    @if ($asset->currentAssignment)
                        <p class="text-sm text-gray-600">
                            Checked in to <span class="font-medium text-gray-800">{{ $asset->currentAssignment->user->name }}</span>
                            since {{ $asset->currentAssignment->assigned_at->format('M j, Y') }}
                        </p>
                    @else
                        <p class="text-sm text-gray-500">Not currently checked in to anyone.</p>
                    @endif
                </div>

                @if ($asset->status === 'available')
                    <x-primary-button wire:click="openCheckOut">Check In</x-primary-button>
                @elseif ($asset->status === 'assigned')
                    <x-secondary-button wire:click="openCheckIn">Check Out</x-secondary-button>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-5">
                <h2 class="font-medium text-gray-800 mb-4">Details</h2>
                <dl class="grid grid-cols-2 gap-y-3 text-sm">
                    <dt class="text-gray-500">Category</dt>
                    <dd>{{ $asset->category?->name ?? '—' }}</dd>

                    <dt class="text-gray-500">Brand / Model</dt>
                    <dd>{{ $asset->brand }} {{ $asset->model }}</dd>

                    <dt class="text-gray-500">Serial Number</dt>
                    <dd>{{ $asset->serial_number ?? '—' }}</dd>

                    <dt class="text-gray-500">Department</dt>
                    <dd>{{ $asset->department?->name ?? '—' }}</dd>

                    <dt class="text-gray-500">Location</dt>
                    <dd>{{ $asset->location?->name ?? '—' }}</dd>

                    <dt class="text-gray-500">Supplier</dt>
                    <dd>{{ $asset->supplier ?? '—' }}</dd>

                    <dt class="text-gray-500">Purchase Date</dt>
                    <dd>{{ $asset->purchase_date?->format('M j, Y') ?? '—' }}</dd>

                    <dt class="text-gray-500">Purchase Cost</dt>
                    <dd>${{ number_format($asset->purchase_cost, 2) }}</dd>

                    <dt class="text-gray-500">Current Book Value</dt>
                    <dd>${{ number_format($asset->current_book_value, 2) }}</dd>

                    <dt class="text-gray-500">Warranty Expiry</dt>
                    <dd>{{ $asset->warranty_expiry?->format('M j, Y') ?? '—' }}</dd>
                </dl>

                @if ($asset->notes)
                    <div class="mt-4 pt-4 border-t text-sm">
                        <div class="text-gray-500 mb-1">Notes</div>
                        <p class="text-gray-700 whitespace-pre-line">{{ $asset->notes }}</p>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-5">
                <h2 class="font-medium text-gray-800 mb-4">Check-In History</h2>
                <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2 pr-4">User</th>
                            <th class="py-2 pr-4">Assigned</th>
                            <th class="py-2 pr-4">Returned</th>
                            <th class="py-2 pr-4">Assigned By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($asset->assignments as $assignment)
                            <tr>
                                <td class="py-2 pr-4">{{ $assignment->user->name }}</td>
                                <td class="py-2 pr-4">{{ $assignment->assigned_at->format('M j, Y') }}</td>
                                <td class="py-2 pr-4">{{ $assignment->returned_at?->format('M j, Y') ?? '—' }}</td>
                                <td class="py-2 pr-4">{{ $assignment->assignedBy?->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 text-center text-gray-400">No assignment history.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-5">
                <h2 class="font-medium text-gray-800 mb-4">Maintenance History</h2>
                <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2 pr-4">Type</th>
                            <th class="py-2 pr-4">Description</th>
                            <th class="py-2 pr-4">Performed</th>
                            <th class="py-2 pr-4">Cost</th>
                            <th class="py-2 pr-4">Next Due</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($asset->maintenances as $maintenance)
                            <tr>
                                <td class="py-2 pr-4">{{ str($maintenance->type)->headline() }}</td>
                                <td class="py-2 pr-4">{{ $maintenance->description }}</td>
                                <td class="py-2 pr-4">{{ $maintenance->performed_at->format('M j, Y') }}</td>
                                <td class="py-2 pr-4">${{ number_format($maintenance->cost, 2) }}</td>
                                <td class="py-2 pr-4">{{ $maintenance->next_due_at?->format('M j, Y') ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4 text-center text-gray-400">No maintenance history.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            @if ($asset->image)
                <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-5">
                    <img src="{{ Storage::url($asset->image) }}" class="w-full rounded-md object-cover">
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-5 text-center" id="print-label">
                <img src="{{ $this->qrCode }}" alt="QR code" class="mx-auto w-40 h-40">
                <div class="mt-2 font-semibold text-gray-800">{{ $asset->name }}</div>
                <div class="text-gray-500 text-sm">{{ $asset->asset_tag }}</div>
            </div>
            <button onclick="window.print()" class="w-full text-sm text-indigo-600 hover:underline">Print Asset Label</button>
        </div>
    </div>

    @if ($showCheckOutModal)
        <x-simple-modal maxWidth="md">
            <form wire:submit="checkOut" class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Check In — {{ $asset->name }}</h2>

                <div class="space-y-4">
                    <div>
                        <x-input-label for="user_id" value="Check in to" />
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
                        <x-input-label for="checkout_notes" value="Notes" />
                        <textarea id="checkout_notes" wire:model="checkout_notes" rows="2" class="w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button type="button" wire:click="$set('showCheckOutModal', false)">Cancel</x-secondary-button>
                    <x-primary-button type="submit">Check In</x-primary-button>
                </div>
            </form>
        </x-simple-modal>
    @endif

    @if ($showCheckInModal)
        <x-simple-modal maxWidth="md">
            <form wire:submit="checkIn" class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Check Out — {{ $asset->name }}</h2>

                @if ($asset->currentAssignment)
                    <p class="text-sm text-gray-500 mb-4">
                        Currently with <span class="font-medium text-gray-700">{{ $asset->currentAssignment->user->name }}</span>
                    </p>
                @endif

                <div class="space-y-4">
                    <div>
                        <x-input-label for="condition_on_return" value="Condition on Return" />
                        <x-text-input id="condition_on_return" wire:model="condition_on_return" class="w-full mt-1" />
                    </div>
                    <div>
                        <x-input-label for="checkin_notes" value="Notes" />
                        <textarea id="checkin_notes" wire:model="checkin_notes" rows="2" class="w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-secondary-button type="button" wire:click="$set('showCheckInModal', false)">Cancel</x-secondary-button>
                    <x-primary-button type="submit">Confirm Check Out</x-primary-button>
                </div>
            </form>
        </x-simple-modal>
    @endif
</div>

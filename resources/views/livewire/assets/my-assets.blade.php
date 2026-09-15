<div>
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">My Assets</h1>

    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Asset</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Tag</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Category</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Location</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Assigned Since</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($assignments as $assignment)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $assignment->asset->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $assignment->asset->asset_tag }}</td>
                        <td class="px-4 py-3">{{ $assignment->asset->category?->name }}</td>
                        <td class="px-4 py-3">{{ $assignment->asset->location?->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $assignment->assigned_at->format('M j, Y') }}</td>
                    </tr>
                @empty
                    <x-empty-state colspan="5" message="You have no assets currently assigned to you." />
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>

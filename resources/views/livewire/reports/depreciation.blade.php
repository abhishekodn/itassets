<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Depreciation Report</h1>
        <div class="space-x-2">
            <x-secondary-button wire:click="exportExcel">Export Excel</x-secondary-button>
            <x-secondary-button wire:click="exportPdf">Export PDF</x-secondary-button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
        <div class="p-4 border-b flex flex-wrap gap-3 items-center">
            <select wire:model.live="categoryFilter" class="rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="departmentFilter" class="rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All departments</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                @endforeach
            </select>

            <div class="ml-auto text-sm text-gray-600">
                Total Cost: <span class="font-semibold text-gray-800">${{ number_format($totalCost, 2) }}</span>
                &nbsp;·&nbsp;
                Total Book Value: <span class="font-semibold text-gray-800">${{ number_format($totalBookValue, 2) }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Tag</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Name</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Category</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">Purchase Date</th>
                    <th class="px-4 py-3 text-right font-medium text-gray-500">Purchase Cost</th>
                    <th class="px-4 py-3 text-right font-medium text-gray-500">Accum. Depreciation</th>
                    <th class="px-4 py-3 text-right font-medium text-gray-500">Book Value</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($assets as $asset)
                    <tr>
                        <td class="px-4 py-3 text-gray-500">{{ $asset->asset_tag }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $asset->name }}</td>
                        <td class="px-4 py-3">{{ $asset->category?->name }}</td>
                        <td class="px-4 py-3">{{ $asset->purchase_date?->format('M j, Y') ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">${{ number_format($asset->purchase_cost, 2) }}</td>
                        <td class="px-4 py-3 text-right">${{ number_format($asset->accumulated_depreciation, 2) }}</td>
                        <td class="px-4 py-3 text-right font-medium">${{ number_format($asset->current_book_value, 2) }}</td>
                    </tr>
                @empty
                    <x-empty-state colspan="7" message="No assets found." />
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>

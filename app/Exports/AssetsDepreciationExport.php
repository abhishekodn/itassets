<?php

namespace App\Exports;

use App\Models\Asset;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssetsDepreciationExport implements FromCollection, WithHeadings
{
    public function __construct(protected Collection $assets)
    {
    }

    public function collection(): Collection
    {
        return $this->assets->map(fn (Asset $asset) => [
            'Asset Tag' => $asset->asset_tag,
            'Name' => $asset->name,
            'Category' => $asset->category?->name,
            'Purchase Date' => $asset->purchase_date?->format('Y-m-d'),
            'Purchase Cost' => (float) $asset->purchase_cost,
            'Accumulated Depreciation' => $asset->accumulated_depreciation,
            'Current Book Value' => $asset->current_book_value,
        ]);
    }

    public function headings(): array
    {
        return ['Asset Tag', 'Name', 'Category', 'Purchase Date', 'Purchase Cost', 'Accumulated Depreciation', 'Current Book Value'];
    }
}

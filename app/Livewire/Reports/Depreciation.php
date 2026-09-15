<?php

namespace App\Livewire\Reports;

use App\Exports\AssetsDepreciationExport;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Department;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
class Depreciation extends Component
{
    public string $categoryFilter = '';
    public string $departmentFilter = '';

    protected function assetsQuery()
    {
        return Asset::with(['category', 'department'])
            ->when($this->categoryFilter, fn ($q) => $q->where('asset_category_id', $this->categoryFilter))
            ->when($this->departmentFilter, fn ($q) => $q->where('department_id', $this->departmentFilter))
            ->orderBy('name')
            ->get();
    }

    public function exportExcel()
    {
        return Excel::download(new AssetsDepreciationExport($this->assetsQuery()), 'depreciation-report.xlsx');
    }

    public function exportPdf()
    {
        $assets = $this->assetsQuery();
        $pdf = Pdf::loadView('reports.depreciation-pdf', compact('assets'));

        return response()->streamDownload(fn () => print ($pdf->output()), 'depreciation-report.pdf');
    }

    public function render()
    {
        $assets = $this->assetsQuery();

        return view('livewire.reports.depreciation', [
            'assets' => $assets,
            'categories' => AssetCategory::orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
            'totalCost' => $assets->sum('purchase_cost'),
            'totalBookValue' => $assets->sum(fn ($a) => $a->current_book_value),
        ]);
    }
}

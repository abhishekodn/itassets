<?php

namespace App\Livewire;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetMaintenance;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    protected function statusCounts(): array
    {
        return Asset::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }

    protected function recentActivity(): Collection
    {
        $addedAssets = Asset::latest('created_at')->limit(5)->get()->map(fn ($asset) => [
            'type' => 'added',
            'time' => $asset->created_at,
            'title' => "{$asset->name} added to inventory",
            'subtitle' => $asset->asset_tag,
            'url' => route('assets.show', $asset),
        ]);

        $checkedOut = AssetAssignment::with('asset', 'user')->latest('assigned_at')->limit(5)->get()->map(fn ($a) => [
            'type' => 'checkout',
            'time' => $a->assigned_at,
            'title' => "{$a->asset->name} checked in to {$a->user->name}",
            'subtitle' => $a->asset->asset_tag,
            'url' => route('assets.show', $a->asset_id),
        ]);

        $checkedIn = AssetAssignment::with('asset', 'user')->whereNotNull('returned_at')->latest('returned_at')->limit(5)->get()->map(fn ($a) => [
            'type' => 'checkin',
            'time' => $a->returned_at,
            'title' => "{$a->asset->name} checked out from {$a->user->name}",
            'subtitle' => $a->asset->asset_tag,
            'url' => route('assets.show', $a->asset_id),
        ]);

        $maintenance = AssetMaintenance::with('asset')->latest('created_at')->limit(5)->get()->map(fn ($m) => [
            'type' => 'maintenance',
            'time' => $m->created_at,
            'title' => str($m->type)->headline().' logged for '.$m->asset->name,
            'subtitle' => $m->asset->asset_tag,
            'url' => route('assets.show', $m->asset_id),
        ]);

        return $addedAssets->concat($checkedOut)->concat($checkedIn)->concat($maintenance)
            ->sortByDesc('time')
            ->take(8)
            ->values();
    }

    protected function categoryChartConfig(Collection $byCategory): array
    {
        $palette = ['#6366f1', '#0ea5e9', '#10b981', '#f59e0b', '#f43f5e', '#8b5cf6', '#14b8a6'];

        return [
            'type' => 'bar',
            'data' => [
                'labels' => $byCategory->map(fn ($row) => $row->category?->name ?? 'Uncategorized')->values(),
                'datasets' => [[
                    'label' => 'Assets',
                    'data' => $byCategory->pluck('total')->values(),
                    'backgroundColor' => $byCategory->values()->map(fn ($row, $i) => $palette[$i % count($palette)])->all(),
                    'borderRadius' => 6,
                    'maxBarThickness' => 36,
                ]],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => ['legend' => ['display' => false]],
                'scales' => [
                    'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
                ],
            ],
        ];
    }

    protected function statusChartConfig(Collection $statusBreakdown): array
    {
        $colorMap = [
            'available' => '#22c55e',
            'assigned' => '#6366f1',
            'in_maintenance' => '#f59e0b',
            'retired' => '#9ca3af',
            'disposed' => '#f87171',
        ];

        return [
            'type' => 'doughnut',
            'data' => [
                'labels' => $statusBreakdown->pluck('label')->values(),
                'datasets' => [[
                    'data' => $statusBreakdown->pluck('total')->values(),
                    'backgroundColor' => $statusBreakdown->map(fn ($row) => $colorMap[$row['key']] ?? '#9ca3af')->values(),
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                ]],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'cutout' => '65%',
                'plugins' => [
                    'legend' => ['position' => 'bottom', 'labels' => ['boxWidth' => 10, 'padding' => 14]],
                ],
            ],
        ];
    }

    protected function activityTrendConfig(): array
    {
        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i)->startOfMonth());

        $checkIns = $months->map(fn ($month) => AssetAssignment::whereBetween('assigned_at', [$month, $month->copy()->endOfMonth()])->count());
        $checkOuts = $months->map(fn ($month) => AssetAssignment::whereBetween('returned_at', [$month, $month->copy()->endOfMonth()])->count());

        return [
            'type' => 'line',
            'data' => [
                'labels' => $months->map(fn ($month) => $month->format('M Y'))->values(),
                'datasets' => [
                    [
                        'label' => 'Checked In',
                        'data' => $checkIns->values(),
                        'borderColor' => '#6366f1',
                        'backgroundColor' => 'rgba(99, 102, 241, 0.12)',
                        'tension' => 0.35,
                        'fill' => true,
                        'pointRadius' => 3,
                    ],
                    [
                        'label' => 'Checked Out',
                        'data' => $checkOuts->values(),
                        'borderColor' => '#f59e0b',
                        'backgroundColor' => 'rgba(245, 158, 11, 0.12)',
                        'tension' => 0.35,
                        'fill' => true,
                        'pointRadius' => 3,
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => ['position' => 'bottom', 'labels' => ['boxWidth' => 10, 'padding' => 14]],
                ],
                'scales' => [
                    'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
                ],
            ],
        ];
    }

    public function render()
    {
        $assets = Asset::query();
        $statusCounts = $this->statusCounts();
        $totalAssets = array_sum($statusCounts);

        $stats = [
            'total_assets' => $totalAssets,
            'total_value' => (clone $assets)->sum('purchase_cost'),
            'available' => $statusCounts['available'] ?? 0,
            'assigned' => $statusCounts['assigned'] ?? 0,
            'in_maintenance' => $statusCounts['in_maintenance'] ?? 0,
            'retired' => ($statusCounts['retired'] ?? 0) + ($statusCounts['disposed'] ?? 0),
        ];

        $byCategory = Asset::selectRaw('asset_category_id, count(*) as total')
            ->with('category:id,name')
            ->groupBy('asset_category_id')
            ->orderByDesc('total')
            ->get();

        $statusBreakdown = collect([
            ['key' => 'available', 'label' => 'Available', 'total' => $statusCounts['available'] ?? 0],
            ['key' => 'assigned', 'label' => 'Assigned', 'total' => $statusCounts['assigned'] ?? 0],
            ['key' => 'in_maintenance', 'label' => 'In Maintenance', 'total' => $statusCounts['in_maintenance'] ?? 0],
            ['key' => 'retired', 'label' => 'Retired', 'total' => $statusCounts['retired'] ?? 0],
            ['key' => 'disposed', 'label' => 'Disposed', 'total' => $statusCounts['disposed'] ?? 0],
        ])->filter(fn ($row) => $row['total'] > 0)->values();

        $upcomingMaintenance = AssetMaintenance::with('asset')
            ->whereNotNull('next_due_at')
            ->whereDate('next_due_at', '<=', now()->addDays(30))
            ->orderBy('next_due_at')
            ->limit(5)
            ->get();

        $expiringWarranty = Asset::whereNotNull('warranty_expiry')
            ->whereDate('warranty_expiry', '<=', now()->addDays(60))
            ->whereDate('warranty_expiry', '>=', now())
            ->orderBy('warranty_expiry')
            ->limit(5)
            ->get();

        return view('livewire.dashboard', [
            'stats' => $stats,
            'byCategory' => $byCategory,
            'statusBreakdown' => $statusBreakdown,
            'totalAssets' => $totalAssets,
            'upcomingMaintenance' => $upcomingMaintenance,
            'expiringWarranty' => $expiringWarranty,
            'activity' => $this->recentActivity(),
            'categoryChart' => $this->categoryChartConfig($byCategory),
            'statusChart' => $this->statusChartConfig($statusBreakdown),
            'trendChart' => $this->activityTrendConfig(),
        ]);
    }
}

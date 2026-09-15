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
        $maxCategoryTotal = max(1, $byCategory->max('total') ?? 1);

        $statusBreakdown = collect([
            ['key' => 'available', 'label' => 'Available', 'total' => $statusCounts['available'] ?? 0, 'color' => 'bg-green-500'],
            ['key' => 'assigned', 'label' => 'Assigned', 'total' => $statusCounts['assigned'] ?? 0, 'color' => 'bg-indigo-500'],
            ['key' => 'in_maintenance', 'label' => 'In Maintenance', 'total' => $statusCounts['in_maintenance'] ?? 0, 'color' => 'bg-amber-500'],
            ['key' => 'retired', 'label' => 'Retired', 'total' => $statusCounts['retired'] ?? 0, 'color' => 'bg-gray-400'],
            ['key' => 'disposed', 'label' => 'Disposed', 'total' => $statusCounts['disposed'] ?? 0, 'color' => 'bg-red-400'],
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
            'maxCategoryTotal' => $maxCategoryTotal,
            'statusBreakdown' => $statusBreakdown,
            'totalAssets' => $totalAssets,
            'upcomingMaintenance' => $upcomingMaintenance,
            'expiringWarranty' => $expiringWarranty,
            'activity' => $this->recentActivity(),
        ]);
    }
}

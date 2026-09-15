<?php

namespace App\Livewire\Maintenances;

use App\Models\Asset;
use App\Models\AssetMaintenance;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';

    public bool $showModal = false;
    public ?int $editingId = null;

    public ?int $asset_id = null;
    public string $type = 'service';
    public string $description = '';
    public string $cost = '0';
    public string $performed_at = '';
    public ?string $next_due_at = null;
    public string $status = 'completed';

    public function mount(): void
    {
        if (request()->boolean('new')) {
            $this->create();
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->reset(['editingId', 'asset_id', 'description', 'next_due_at']);
        $this->type = 'service';
        $this->cost = '0';
        $this->status = 'completed';
        $this->performed_at = now()->format('Y-m-d');
        $this->showModal = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'asset_id' => 'required|exists:assets,id',
            'type' => 'required|in:repair,service,inspection',
            'description' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'performed_at' => 'required|date',
            'next_due_at' => 'nullable|date',
            'status' => 'required|in:completed,scheduled',
        ]);

        DB::transaction(function () use ($data) {
            $data['performed_by'] = auth()->id();
            AssetMaintenance::create($data);

            if ($data['status'] === 'scheduled') {
                Asset::where('id', $data['asset_id'])->update(['status' => 'in_maintenance']);
            } elseif (Asset::find($data['asset_id'])?->status === 'in_maintenance') {
                Asset::where('id', $data['asset_id'])->update(['status' => 'available']);
            }
        });

        $this->showModal = false;
        session()->flash('success', 'Maintenance record saved.');
    }

    public function delete(int $id): void
    {
        AssetMaintenance::findOrFail($id)->delete();
        session()->flash('success', 'Maintenance record deleted.');
    }

    public function render()
    {
        return view('livewire.maintenances.index', [
            'maintenances' => AssetMaintenance::with('asset')
                ->when($this->search, fn ($q) => $q->whereHas('asset', fn ($a) => $a
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('asset_tag', 'like', "%{$this->search}%")))
                ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
                ->orderByDesc('performed_at')
                ->paginate(10),
            'assets' => Asset::orderBy('name')->get(),
        ]);
    }
}

<?php

namespace App\Livewire\Assignments;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'current';

    public bool $showModal = false;
    public ?int $asset_id = null;
    public ?int $user_id = null;
    public string $assigned_at = '';
    public string $condition_on_assign = 'Good';
    public string $notes = '';

    public bool $showReturnModal = false;
    public ?int $returningId = null;
    public string $condition_on_return = 'Good';
    public string $return_notes = '';

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
        $this->reset(['asset_id', 'user_id', 'notes']);
        $this->condition_on_assign = 'Good';
        $this->assigned_at = now()->format('Y-m-d');
        $this->showModal = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'asset_id' => 'required|exists:assets,id',
            'user_id' => 'required|exists:users,id',
            'assigned_at' => 'required|date',
            'condition_on_assign' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($data) {
            AssetAssignment::create([
                ...$data,
                'assigned_by' => auth()->id(),
            ]);

            Asset::where('id', $data['asset_id'])->update(['status' => 'assigned']);
        });

        $this->showModal = false;
        session()->flash('success', 'Asset checked in.');
    }

    public function confirmReturn(int $id): void
    {
        $this->returningId = $id;
        $this->condition_on_return = 'Good';
        $this->return_notes = '';
        $this->showReturnModal = true;
    }

    public function returnAsset(): void
    {
        $this->validate([
            'condition_on_return' => 'nullable|string|max:255',
        ]);

        $assignment = AssetAssignment::findOrFail($this->returningId);

        DB::transaction(function () use ($assignment) {
            $assignment->update([
                'returned_at' => now(),
                'condition_on_return' => $this->condition_on_return,
                'notes' => trim($assignment->notes."\n".$this->return_notes),
            ]);

            $assignment->asset()->update(['status' => 'available']);
        });

        $this->showReturnModal = false;
        session()->flash('success', 'Asset checked out.');
    }

    public function render()
    {
        return view('livewire.assignments.index', [
            'assignments' => AssetAssignment::with(['asset', 'user'])
                ->when($this->search, fn ($q) => $q->whereHas('asset', fn ($a) => $a
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('asset_tag', 'like', "%{$this->search}%"))
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->search}%")))
                ->when($this->statusFilter === 'current', fn ($q) => $q->whereNull('returned_at'))
                ->when($this->statusFilter === 'returned', fn ($q) => $q->whereNotNull('returned_at'))
                ->orderByDesc('assigned_at')
                ->paginate(10),
            'availableAssets' => Asset::where('status', 'available')->orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
        ]);
    }
}

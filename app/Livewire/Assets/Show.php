<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\User;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public Asset $asset;

    public bool $showCheckOutModal = false;
    public ?int $user_id = null;
    public string $assigned_at = '';
    public string $condition_on_assign = 'Good';
    public string $checkout_notes = '';

    public bool $showCheckInModal = false;
    public string $condition_on_return = 'Good';
    public string $checkin_notes = '';

    public function mount(Asset $asset): void
    {
        $this->asset = $asset;
    }

    #[Computed]
    public function qrCode(): string
    {
        $qrCode = new QrCode(data: route('assets.show', $this->asset));
        $writer = new PngWriter();

        return $writer->write($qrCode)->getDataUri();
    }

    public function openCheckOut(): void
    {
        $this->reset(['user_id', 'checkout_notes']);
        $this->condition_on_assign = 'Good';
        $this->assigned_at = now()->format('Y-m-d');
        $this->showCheckOutModal = true;
    }

    public function checkOut(): void
    {
        $data = $this->validate([
            'user_id' => 'required|exists:users,id',
            'assigned_at' => 'required|date',
            'condition_on_assign' => 'nullable|string|max:255',
            'checkout_notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($data) {
            AssetAssignment::create([
                'asset_id' => $this->asset->id,
                'user_id' => $data['user_id'],
                'assigned_by' => auth()->id(),
                'assigned_at' => $data['assigned_at'],
                'condition_on_assign' => $data['condition_on_assign'],
                'notes' => $data['checkout_notes'],
            ]);

            $this->asset->update(['status' => 'assigned']);
        });

        $this->showCheckOutModal = false;
        $this->asset->refresh();
        session()->flash('success', 'Asset checked in.');
    }

    public function openCheckIn(): void
    {
        $this->condition_on_return = 'Good';
        $this->checkin_notes = '';
        $this->showCheckInModal = true;
    }

    public function checkIn(): void
    {
        $this->validate([
            'condition_on_return' => 'nullable|string|max:255',
        ]);

        $assignment = $this->asset->currentAssignment;

        if ($assignment) {
            DB::transaction(function () use ($assignment) {
                $assignment->update([
                    'returned_at' => now(),
                    'condition_on_return' => $this->condition_on_return,
                    'notes' => trim($assignment->notes."\n".$this->checkin_notes),
                ]);

                $this->asset->update(['status' => 'available']);
            });
        }

        $this->showCheckInModal = false;
        $this->asset->refresh();
        session()->flash('success', 'Asset checked out.');
    }

    public function render()
    {
        $this->asset->load([
            'category', 'location', 'department',
            'currentAssignment.user',
            'assignments' => fn ($q) => $q->with('user', 'assignedBy')->orderByDesc('assigned_at'),
            'maintenances' => fn ($q) => $q->orderByDesc('performed_at'),
        ]);

        return view('livewire.assets.show', [
            'users' => User::orderBy('name')->get(),
        ]);
    }
}

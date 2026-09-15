<?php

namespace App\Livewire\Locations;

use App\Models\Department;
use App\Models\Location;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $address = '';
    public ?int $department_id = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->reset(['editingId', 'name', 'address', 'department_id']);
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $location = Location::findOrFail($id);
        $this->editingId = $location->id;
        $this->name = $location->name;
        $this->address = (string) $location->address;
        $this->department_id = $location->department_id;
        $this->showModal = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        Location::updateOrCreate(['id' => $this->editingId], $data);

        $this->showModal = false;
        session()->flash('success', 'Location saved.');
    }

    public function delete(int $id): void
    {
        Location::findOrFail($id)->delete();
        session()->flash('success', 'Location deleted.');
    }

    public function render()
    {
        return view('livewire.locations.index', [
            'locations' => Location::with('department')->withCount('assets')
                ->where('name', 'like', "%{$this->search}%")
                ->orderBy('name')
                ->paginate(10),
            'departments' => Department::orderBy('name')->get(),
        ]);
    }
}

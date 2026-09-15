<?php

namespace App\Livewire\Departments;

use App\Models\Department;
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
    public string $code = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->reset(['editingId', 'name', 'code']);
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $department = Department::findOrFail($id);
        $this->editingId = $department->id;
        $this->name = $department->name;
        $this->code = $department->code;
        $this->showModal = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:departments,code,'.$this->editingId,
        ]);

        Department::updateOrCreate(['id' => $this->editingId], $data);

        $this->showModal = false;
        session()->flash('success', 'Department saved.');
    }

    public function delete(int $id): void
    {
        Department::findOrFail($id)->delete();
        session()->flash('success', 'Department deleted.');
    }

    public function render()
    {
        return view('livewire.departments.index', [
            'departments' => Department::withCount('assets')
                ->where(fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('code', 'like', "%{$this->search}%"))
                ->orderBy('name')
                ->paginate(10),
        ]);
    }
}

<?php

namespace App\Livewire\Categories;

use App\Models\AssetCategory;
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
    public int $useful_life_years = 3;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->reset(['editingId', 'name']);
        $this->useful_life_years = 3;
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $category = AssetCategory::findOrFail($id);
        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->useful_life_years = $category->useful_life_years;
        $this->showModal = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => 'required|string|max:255|unique:asset_categories,name,'.$this->editingId,
            'useful_life_years' => 'required|integer|min:1|max:50',
        ]);

        AssetCategory::updateOrCreate(['id' => $this->editingId], $data);

        $this->showModal = false;
        session()->flash('success', 'Category saved.');
    }

    public function delete(int $id): void
    {
        AssetCategory::findOrFail($id)->delete();
        session()->flash('success', 'Category deleted.');
    }

    public function render()
    {
        return view('livewire.categories.index', [
            'categories' => AssetCategory::withCount('assets')
                ->where('name', 'like', "%{$this->search}%")
                ->orderBy('name')
                ->paginate(10),
        ]);
    }
}

<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Department;
use App\Models\Location;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public string $statusFilter = '';
    public string $categoryFilter = '';

    public bool $showModal = false;
    public ?int $editingId = null;

    public string $asset_tag = '';
    public string $name = '';
    public ?int $asset_category_id = null;
    public string $brand = '';
    public string $model = '';
    public string $serial_number = '';
    public ?string $purchase_date = null;
    public string $purchase_cost = '0';
    public string $supplier = '';
    public ?string $warranty_expiry = null;
    public ?int $location_id = null;
    public ?int $department_id = null;
    public string $status = 'available';
    public string $notes = '';
    public $image;
    public ?string $existingImage = null;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'asset_category_id' => 'required|exists:asset_categories,id',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'warranty_expiry' => 'nullable|date',
            'location_id' => 'nullable|exists:locations,id',
            'department_id' => 'nullable|exists:departments,id',
            'status' => 'required|in:available,assigned,in_maintenance,retired,disposed',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ];
    }

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

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->reset([
            'editingId', 'name', 'brand', 'model', 'serial_number', 'purchase_date',
            'supplier', 'warranty_expiry', 'location_id', 'department_id', 'notes',
            'image', 'existingImage', 'asset_category_id',
        ]);
        $this->purchase_cost = '0';
        $this->status = 'available';
        $this->asset_tag = $this->nextAssetTag();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $asset = Asset::findOrFail($id);
        $this->editingId = $asset->id;
        $this->asset_tag = $asset->asset_tag;
        $this->name = $asset->name;
        $this->asset_category_id = $asset->asset_category_id;
        $this->brand = (string) $asset->brand;
        $this->model = (string) $asset->model;
        $this->serial_number = (string) $asset->serial_number;
        $this->purchase_date = $asset->purchase_date?->format('Y-m-d');
        $this->purchase_cost = (string) $asset->purchase_cost;
        $this->supplier = (string) $asset->supplier;
        $this->warranty_expiry = $asset->warranty_expiry?->format('Y-m-d');
        $this->location_id = $asset->location_id;
        $this->department_id = $asset->department_id;
        $this->status = $asset->status;
        $this->notes = (string) $asset->notes;
        $this->existingImage = $asset->image;
        $this->image = null;
        $this->showModal = true;
    }

    protected function nextAssetTag(): string
    {
        $lastId = (int) (Asset::max('id') ?? 0);

        do {
            $lastId++;
            $tag = 'AST-'.str_pad((string) $lastId, 5, '0', STR_PAD_LEFT);
        } while (Asset::where('asset_tag', $tag)->exists());

        return $tag;
    }

    public function save(): void
    {
        $data = $this->validate();
        $data['asset_tag'] = $this->asset_tag;

        if ($this->image) {
            $data['image'] = $this->image->store('assets', 'public');

            if ($this->existingImage) {
                Storage::disk('public')->delete($this->existingImage);
            }
        } else {
            $data['image'] = $this->existingImage;
        }

        Asset::updateOrCreate(['id' => $this->editingId], $data);

        $this->showModal = false;
        session()->flash('success', 'Asset saved.');
    }

    public function delete(int $id): void
    {
        $asset = Asset::findOrFail($id);

        if ($asset->image) {
            Storage::disk('public')->delete($asset->image);
        }

        $asset->delete();
        session()->flash('success', 'Asset deleted.');
    }

    public function render()
    {
        return view('livewire.assets.index', [
            'assets' => Asset::with(['category', 'location', 'department'])
                ->when($this->search, fn ($q) => $q->where(fn ($q2) => $q2
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('asset_tag', 'like', "%{$this->search}%")
                    ->orWhere('serial_number', 'like', "%{$this->search}%")))
                ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
                ->when($this->categoryFilter, fn ($q) => $q->where('asset_category_id', $this->categoryFilter))
                ->orderByDesc('id')
                ->paginate(10),
            'categories' => AssetCategory::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
        ]);
    }
}

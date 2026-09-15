<?php

namespace App\Livewire\Users;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;

    public string $name = '';
    public string $employee_code = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'staff';

    public bool $showAssignModal = false;
    public ?int $assignUserId = null;
    public string $assignUserName = '';
    public ?int $asset_id = null;
    public string $assigned_at = '';
    public string $condition_on_assign = 'Good';
    public string $assign_notes = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->reset(['name', 'employee_code', 'email', 'password']);
        $this->role = 'staff';
        $this->showModal = true;
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'employee_code' => 'nullable|string|max:50|unique:users,employee_code',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', Password::defaults()],
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'employee_code' => $data['employee_code'] ?: null,
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
        $user->assignRole($data['role']);

        $this->showModal = false;
        session()->flash('success', 'User created.');

        $this->assignAsset($user->id);
    }

    public function updateRole(int $userId, string $role): void
    {
        if ($userId === auth()->id()) {
            session()->flash('error', 'You cannot change your own role.');

            return;
        }

        $user = User::findOrFail($userId);
        $user->syncRoles([$role]);
        session()->flash('success', 'Role updated for '.$user->name.'.');
    }

    public function assignAsset(int $userId): void
    {
        $user = User::findOrFail($userId);

        $this->reset(['asset_id', 'assign_notes']);
        $this->assignUserId = $user->id;
        $this->assignUserName = $user->name;
        $this->condition_on_assign = 'Good';
        $this->assigned_at = now()->format('Y-m-d');
        $this->showAssignModal = true;
    }

    public function checkOut(): void
    {
        $data = $this->validate([
            'asset_id' => 'required|exists:assets,id',
            'assigned_at' => 'required|date',
            'condition_on_assign' => 'nullable|string|max:255',
            'assign_notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($data) {
            AssetAssignment::create([
                'asset_id' => $data['asset_id'],
                'user_id' => $this->assignUserId,
                'assigned_by' => auth()->id(),
                'assigned_at' => $data['assigned_at'],
                'condition_on_assign' => $data['condition_on_assign'],
                'notes' => $data['assign_notes'],
            ]);

            Asset::where('id', $data['asset_id'])->update(['status' => 'assigned']);
        });

        $this->showAssignModal = false;
        session()->flash('success', 'Asset checked in to '.$this->assignUserName.'.');
    }

    public function render()
    {
        return view('livewire.users.index', [
            'users' => User::with('roles')
                ->withCount(['assetAssignments as active_assets_count' => fn ($q) => $q->whereNull('returned_at')])
                ->where(fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('employee_code', 'like', "%{$this->search}%"))
                ->orderBy('name')
                ->paginate(10),
            'roles' => Role::orderBy('name')->pluck('name'),
            'availableAssets' => Asset::where('status', 'available')->orderBy('name')->get(),
        ]);
    }
}

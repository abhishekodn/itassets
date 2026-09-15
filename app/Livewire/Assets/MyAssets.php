<?php

namespace App\Livewire\Assets;

use App\Models\AssetAssignment;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class MyAssets extends Component
{
    public function render()
    {
        $assignments = AssetAssignment::with('asset.category', 'asset.location')
            ->where('user_id', auth()->id())
            ->whereNull('returned_at')
            ->orderByDesc('assigned_at')
            ->get();

        return view('livewire.assets.my-assets', compact('assignments'));
    }
}

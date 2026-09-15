<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_tag', 'name', 'asset_category_id', 'brand', 'model', 'serial_number',
        'purchase_date', 'purchase_cost', 'supplier', 'warranty_expiry',
        'location_id', 'department_id', 'status', 'image', 'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'purchase_cost' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function currentAssignment(): HasOne
    {
        return $this->hasOne(AssetAssignment::class)->whereNull('returned_at')->latest('assigned_at');
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(AssetMaintenance::class);
    }

    /**
     * Straight-line depreciated book value as of today, floored at 0.
     */
    public function getCurrentBookValueAttribute(): float
    {
        if (! $this->purchase_date || (float) $this->purchase_cost <= 0) {
            return (float) $this->purchase_cost;
        }

        $usefulLifeMonths = max(1, ($this->category?->useful_life_years ?? 3) * 12);
        $ageInMonths = $this->purchase_date->diffInMonths(now());
        $monthlyDepreciation = $this->purchase_cost / $usefulLifeMonths;
        $depreciated = $monthlyDepreciation * min($ageInMonths, $usefulLifeMonths);

        return round(max((float) $this->purchase_cost - $depreciated, 0), 2);
    }

    public function getAccumulatedDepreciationAttribute(): float
    {
        return round((float) $this->purchase_cost - $this->current_book_value, 2);
    }
}

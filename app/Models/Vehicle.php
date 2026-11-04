<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'brand',
        'model',
        'year',
        'plate_number',
        'vehicle_type',
        'transmission',
        'fuel_type',
        'seats',
        'daily_price',
        'security_deposit',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'seats' => 'integer',
            'daily_price' => 'decimal:2',
            'security_deposit' => 'decimal:2',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(VehicleImage::class);
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }
}

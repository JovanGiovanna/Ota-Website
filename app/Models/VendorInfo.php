<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

class VendorInfo extends Model
{
    use HasApiTokens, HasFactory, HasUuids;

    protected $table = 'vendor_info';

    protected $fillable = [
        'id_vendor',
        'id_city',
        'name_corporate',
        'phone',
        'address',
        'description',
        'coordinate_latitude',
        'coordinate_longitude',
        'landmark_description',
        'is_verified',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'id_vendor');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'id_city');
    }

    public function province()
    {
        return $this->hasOneThrough(
            Province::class,
            City::class,
            'id',         // Foreign key di City
            'id',         // Foreign key di Province
            'id_city',    // Local key di VendorInfo
            'id_province' // Local key di City
        );
    }
}

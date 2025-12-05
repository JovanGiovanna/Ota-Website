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
        'total_revenue',
    ];

    /**
     * Atribut yang harus di-cast ke tipe data asli.
     * @var array<string, string>
     */
    protected $casts = [
        'total_revenue' => 'decimal:2',
        'coordinate_latitude' => 'decimal:8',
        'coordinate_longitude' => 'decimal:8',
        'is_verified' => 'boolean',
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

    public function packages()
    {
        return $this->hasMany(Package::class, 'id_vendor_info');
    }

    /**
     * Get the average rating for the vendor info.
     */
    public function averageRating()
    {
        // Get all packages for this vendor info and calculate average rating
        $packages = $this->packages()->with('reviews')->get();
        $totalRating = 0;
        $totalReviews = 0;

        foreach ($packages as $package) {
            $rating = $package->averageRating();
            $reviewCount = $package->reviews->count();
            $totalRating += $rating * $reviewCount;
            $totalReviews += $reviewCount;
        }

        return $totalReviews > 0 ? $totalRating / $totalReviews : 0;
    }
}

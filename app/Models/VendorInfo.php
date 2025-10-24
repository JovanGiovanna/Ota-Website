<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

class VendorInfo extends Model
{
    use HasApiTokens,HasFactory, HasUuids;

    /**
     * Nama tabel yang terkait dengan Model.
     * @var string
     */
    protected $table = 'vendor_info';

    /**
     * Kolom yang dapat diisi secara massal.
     * @var array<int, string>
     */
    protected $fillable = [
        'id_vendor',
        'id_city',
        'name_corporate',
        'phone',
        'address',
        'description',
        'desc',
        'coordinate_latitude',
        'coordinate_longitude',
        'landmark_description',
        'latitude',
        'longitude',
        'landmark',
        'business_type',
        'license_number',
        'tax_id',
        'is_verified',
    ];

    /**
     * Relasi ke Model Vendor (Satu info dimiliki oleh satu Vendor).
     * @return BelongsTo
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'id_vendor');
    }

    /**
     * Relasi ke Model City (Satu info berada di satu Kota).
     * Asumsi Anda memiliki Model City.
     * @return BelongsTo
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'id_city');
    }

    /**
     * Relasi ke Model Province melalui City.
     * @return BelongsTo
     */
    public function province()
    {
        return $this->city->belongsTo(Province::class, 'id_province');
    }
}

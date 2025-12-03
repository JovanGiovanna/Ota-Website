<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 
use Illuminate\Support\Str;

class Package extends Model
{
    use HasFactory, SoftDeletes; // Menggunakan SoftDeletes

    protected $table = 'packages';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Kolom-kolom yang dapat diisi secara massal (mass assignable).
     * Telah Disesuaikan dengan kolom Migrasi: nta, pax_paid, discount_percentage, products_data, addons_data.
     * Dihapus: price_real, price_publish, id_vendor_info (karena tidak ada di migrasi).
     * @var array<int, string>
     */
    protected $fillable = [
        'name_package',
        'slug',
        'description',
        'location',
        'phone',
        'images',
        'nta',
        'pax_paid',
        'upsale',
        'discount_type',
        'discount_value',
        'discount_amount',
        'discount_expires_at',
        'start_publish',
        'end_publish',
        'is_active',
        'products_data',
        'addons_data',
    ];

    /**
     * Kolom-kolom yang harus di-cast ke tipe data asli.
     * @var array<string, string>
     */
    protected $casts = [
        'nta' => 'decimal:2',
        'pax_paid' => 'decimal:2',
        'upsale' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_expires_at' => 'datetime',
        'start_publish' => 'datetime',
        'end_publish' => 'datetime',
        'is_active' => 'boolean',
        'products_data' => 'array',
        'addons_data' => 'array',
        'images' => 'array',
        'deleted_at' => 'datetime',
    ];
    
    /**
     * Event boot untuk membuat UUID saat model baru dibuat.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // --- Relasi (Relasi yang tidak memiliki foreign key di Migrasi dinonaktifkan/dihapus) ---

    // ❌ Relasi 'vendorInfo' dinonaktifkan karena 'id_vendor_info' TIDAK ada di Migrasi
    public function vendorInfo()
    {
        return $this->belongsTo(\App\Models\VendorInfo::class, 'id_vendor_info');
    }

    // ❌ Relasi 'type' dinonaktifkan karena 'id_type' TIDAK ada di Migrasi
    public function type()
    {
        return $this->belongsTo(\App\Models\Type::class, 'id_type');
    }

    /**
     * Get the reviews for the package. (Asumsi ini masih relevan dengan tabel lain)
     */
    public function reviews()
    {
        // Pastikan model Review sudah diimport atau menggunakan FQCN
        return $this->hasMany(\App\Models\Review::class);
    }

    /**
     * Get the average rating for the package.
     */
    public function averageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Get the wishlists for the package.
     */
    public function wishlists()
    {
        return $this->morphMany(\App\Models\Wishlist::class, 'wishable');
    }

    // app/Models/Package.php
public function bookPackageAddons()
{
    return $this->hasMany(\App\Models\BookPackageAddon::class, 'id_package', 'id');
}

}
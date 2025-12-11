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
     * Telah Disesuaikan dengan kolom Migrasi: nta, pax_paid, discount_percentage, products_data, addons_data, id_vendor_info.
     * @var array<int, string>
     */
    protected $fillable = [
        'id_vendor_info',
        'name_package',
        'slug',
        'description',
        'location',
        'phone',
        'images',
        'nta',
        'pax_paid',
        'upsell',
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
        'upsell' => 'decimal:2',
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

    // --- Relasi ---

    /**
     * Get the vendor info associated with the package.
     */
    public function vendorInfo()
    {
        return $this->belongsTo(\App\Models\VendorInfo::class, 'id_vendor_info');
    }

    /**
     * Get the vendor associated with the package through vendor info.
     */
    public function vendor()
    {
        return $this->hasOneThrough(
            \App\Models\Vendor::class,
            \App\Models\VendorInfo::class,
            'id', // Foreign key on VendorInfo table
            'id', // Foreign key on Vendor table
            'id_vendor_info', // Local key on Package table
            'id_vendor' // Local key on VendorInfo table
        );
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

    /**
     * Get the products associated with the package (many-to-many through package_products).
     */
    public function products()
    {
        return $this->belongsToMany(\App\Models\Product::class, 'package_products', 'id_package', 'id_product');
    }

    /**
     * Get the addons associated with the package (through BookPackageAddon).
     */
    public function addons()
    {
        return $this->hasManyThrough(
            \App\Models\Addon::class,
            \App\Models\BookPackageAddon::class,
            'id_package',
            'id',
            'id',
            'id_addons'
        );
    }    /**
    * Hitung total harga (NTA + Upsell) SEBELUM diskon.
     */
    public function getTotalPriceBeforeDiscountAttribute()
    {
        $nta = (float) ($this->nta ?? 0);
        $upsell = (float) ($this->upsell ?? 0);

        return round($nta + $upsell, 2);
    }

    /**
     * Hitung harga akhir SETELAH diskon (jika ada dan belum kadaluarsa).
     */
    public function getFinalPriceAttribute()
    {
        $totalPrice = $this->totalPriceBeforeDiscount;
        $discountType = $this->discount_type;
        $discountValue = (float) $this->discount_value;
        $expiry = $this->discount_expires_at;

        if ($expiry && $expiry->isPast()) {
            return $totalPrice;
        }

        $discountAmount = 0.00;

        if ($discountType === 'percentage' && $discountValue > 0) {
            $discountAmount = $totalPrice * ($discountValue / 100);
        } elseif ($discountType === 'fixed' && $discountValue > 0) {
            $discountAmount = $discountValue;
        }

        $finalPrice = $totalPrice - $discountAmount;

        return round(max(0, $finalPrice), 2);
    }

    /**
     * Get available stock for this package based on products and addons.
     * Returns the minimum stock available from all products/addons in the package.
     * Considers the pax (capacity per stock) for each item.
     */
    public function getAvailableStockAttribute()
    {
        $minStock = PHP_INT_MAX;
        $hasAnyItem = false;
        
        // Check products stock
        $productsData = is_array($this->products_data) ? $this->products_data : [];
        foreach ($productsData as $productData) {
            $productId = $productData['id'] ?? null;
            if (!$productId) continue;
            
            $product = Product::find($productId);
            if (!$product) continue;
            
            $currentStock = $product->jumlah ?? 0;
            
            // Skip items with NULL stock (considered unlimited)
            if ($currentStock === null) continue;
            
            $hasAnyItem = true;
            $packagesAvailable = $currentStock;
            
            $minStock = min($minStock, $packagesAvailable);
        }
        
        // Check addons stock
        $addonsData = is_array($this->addons_data) ? $this->addons_data : [];
        foreach ($addonsData as $addonData) {
            $addonId = $addonData['id'] ?? null;
            if (!$addonId) continue;
            
            $addon = \App\Models\Addon::find($addonId);
            if (!$addon) continue;
            
            $currentStock = $addon->jumlah ?? null;
            
            // Skip items with NULL stock (considered unlimited)
            if ($currentStock === null) continue;
            
            $hasAnyItem = true;
            $packagesAvailable = $currentStock;
            
            $minStock = min($minStock, $packagesAvailable);
        }
        
        // If no products or addons with stock tracking, return unlimited (999)
        if (!$hasAnyItem || $minStock === PHP_INT_MAX) {
            return 999;
        }
        
        return max(0, $minStock);
    }

    /**
     * Check if package is available (has stock).
     */
    public function getIsAvailableAttribute()
    {
        return $this->availableStock > 0;
    }

}

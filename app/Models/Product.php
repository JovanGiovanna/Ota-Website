<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; 
use Illuminate\Database\Eloquent\SoftDeletes; 
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'products';

    /**
     * Atribut yang dapat diisi (mass assignable).
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'images',
        'description',
        'address',
        'location',
        'phone',
        'basic_price',
        'nta',
        'upsale',
        'discount_type',
        'discount_value',
        'discount_amount',
        'discount_expires_at', 
        'id_category',
        'id_vendor',
        'pax',
        'jumlah',
        'status',
    ];

    /**
     * Tentukan atribut yang harus di-cast ke tipe data asli.
     * @var array<string, string>
     */
    protected $casts = [
        'basic_price'  => 'decimal:2',
        'nta'          => 'decimal:2',
        'upsale'       => 'decimal:2',
        'discount_value'      => 'decimal:2',
        'discount_amount'     => 'decimal:2',
        'discount_expires_at' => 'datetime',
        'pax'          => 'integer',
        'jumlah'       => 'integer',
        'address'      => 'string',
        'status'       => 'string',
        'images'       => 'array',
        'deleted_at'   => 'datetime',
    ];

   // --- Relasi ---

    /**
     * Mendapatkan kategori yang dimiliki produk ini.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'id_category');
    }

    /**
     * Mendapatkan vendor yang menjual produk ini.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'id_vendor');
    }

    /**
     * Mendapatkan super admin yang menjual produk ini.
     */
    public function superAdmin(): BelongsTo
    {
        return $this->belongsTo(SuperAdmin::class, 'id_super_admin');
    }

    /**
     * Mendapatkan reviews untuk produk ini.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Mendapatkan rata-rata rating produk.
     */
    public function averageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Get the wishlists for the product.
     */
    public function wishlists()
    {
        return $this->morphMany(\App\Models\Wishlist::class, 'wishable');
    }
    
    // ------------------------------------------------------------------
    // --- Accessor/Mutator (Menggunakan sintaks Laravel 9/10/11) ---
    // ------------------------------------------------------------------
    
    /**
     * Hitung total harga (NTA + Pajak) SEBELUM diskon.
     * Digunakan seperti $product->total_price_before_discount
     */
    protected function totalPriceBeforeDiscount(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $nta = (float) ($attributes['nta'] ?? 0);
                $upsale = (float) ($attributes['upsale'] ?? 0);

                // Perhitungan: Total = NTA + Upsale
                return round($nta + $upsale, 2);
            },
        );
    }

    /**
     * Hitung harga akhir SETELAH diskon (jika ada dan belum kadaluarsa).
     * Digunakan seperti $product->final_price
     */
    protected function finalPrice(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $totalPrice = $this->totalPriceBeforeDiscount; 
                $discountType = $attributes['discount_type'];
                $discountValue = (float) $attributes['discount_value'];
                $expiry = $attributes['discount_expires_at'];
                
                if ($expiry && strtotime($expiry) < time()) {
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
            },
        );
    }

    /**
     * Hitung jumlah diskon berdasarkan tipe diskon.
     * Digunakan seperti $product->calculated_discount_amount
     */
    protected function calculatedDiscountAmount(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $basicPrice = (float) $attributes['basic_price'];
                $upsale = (float) ($attributes['upsale'] ?? 0);
                $discountType = $attributes['discount_type'];
                $discountValue = (float) $attributes['discount_value'];
                $expiry = $attributes['discount_expires_at'];
                
                // Cek kadaluarsa diskon
                if ($expiry && strtotime($expiry) < time()) {
                    return 0.00;
                }

                // Hitung harga sebelum diskon: use NTA + Upsale if provided, otherwise fallback to basic_price
                $priceBeforDiscount = $basicPrice + $upsale;

                $discountAmount = 0.00;

                if ($discountType === 'percentage' && $discountValue > 0) {
                    // Diskon persentase dari harga sebelum diskon
                    $discountAmount = $priceBeforDiscount * ($discountValue / 100);
                } elseif ($discountType === 'fixed' && $discountValue > 0) {
                    // Diskon tetap
                    $discountAmount = $discountValue;
                }
                
                // Pastikan jumlah diskon tidak menjadi negatif
                return round(max(0, $discountAmount), 2);
            },
        );
    }

public function bookProductAddons()
{
    return $this->hasMany(\App\Models\BookProductAddon::class, 'id_product', 'id');
}
}
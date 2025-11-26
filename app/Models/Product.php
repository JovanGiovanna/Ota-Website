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
        'basic_price',
        'nta',
        'tax_rate',
        'discount_type',
        'discount_value',
        'discount_expires_at', 
        'id_category',
        'id_vendor',
        'pax',
        'jumlah',
        'max_adults',
        'max_children',
        'status',
    ];

    /**
     * Tentukan atribut yang harus di-cast ke tipe data asli.
     * @var array<string, string>
     */
    protected $casts = [
        'basic_price'  => 'decimal:2',
        'nta'          => 'decimal:2',
        'tax_rate'     => 'decimal:2',
        'discount_value'      => 'decimal:2',
        'discount_expires_at' => 'datetime',
        'pax'          => 'integer',
        'max_adults'   => 'integer',
        'max_children' => 'integer',
        'jumlah'       => 'integer',
        'status'       => 'string',
        'images'       => 'array',
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
                $nta = $attributes['nta'];
                $taxRate = $attributes['tax_rate'];
                
                // Perhitungan: Total = NTA + (NTA * (Tax Rate / 100))
                $taxAmount = $nta * ($taxRate / 100);
                
                return round($nta + $taxAmount, 2);
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
                $totalPrice = $this->totalPriceBeforeDiscount; // Ambil total harga sebelum diskon
                $discountType = $attributes['discount_type'];
                $discountValue = (float) $attributes['discount_value'];
                $expiry = $attributes['discount_expires_at'];
                
                // Cek kadaluarsa diskon
                if ($expiry && strtotime($expiry) < time()) {
                    return $totalPrice; // Diskon kadaluarsa, kembalikan harga penuh
                }

                $discountAmount = 0.00;

                if ($discountType === 'percentage' && $discountValue > 0) {
                    $discountAmount = $totalPrice * ($discountValue / 100);
                } elseif ($discountType === 'fixed' && $discountValue > 0) {
                    $discountAmount = $discountValue;
                }
                
                $finalPrice = $totalPrice - $discountAmount;
                
                // Pastikan harga tidak menjadi negatif
                return round(max(0, $finalPrice), 2);
            },
        );
    }

public function bookProductAddons()
{
    return $this->hasMany(\App\Models\BookProductAddon::class, 'id_product', 'id');
}
}
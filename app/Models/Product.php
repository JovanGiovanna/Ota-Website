<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; 
use Illuminate\Database\Eloquent\SoftDeletes; 
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    
    // --- Accessor/Mutator (Opsional tapi Direkomendasikan) ---
    
    /**
     * Hitung total harga (NTA + Pajak).
     * Ini adalah Accessor, digunakan seperti $product->total_price
     */
    public function getTotalPriceAttribute(): float
    {
        $nta = $this->attributes['nta'];
        $taxRate = $this->attributes['tax_rate'];
        
        // Perhitungan: Total = NTA + (NTA * (Tax Rate / 100))
        $taxAmount = $nta * ($taxRate / 100);
        
        return round($nta + $taxAmount, 2);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // Untuk Primary Key UUID
use Illuminate\Database\Eloquent\SoftDeletes; // Untuk Soft Deletes
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'products';

    /**
     * Atribut yang dapat diisi (mass assignable).
     * Kolom 'pax' sudah ditambahkan.
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'images',
        'description',
        'price',
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
     * Kolom 'pax' sudah ditambahkan.
     * @var array<string, string>
     */
    protected $casts = [
        'price'        => 'decimal:2',
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
}
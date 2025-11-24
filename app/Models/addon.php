<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Addon extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'addons';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id_vendor',
        'addons',
        'desc',
        'status',
        'basic_price', 
        'nta',         
        'tax_rate',
        'discount_type',
        'discount_value',
        'discount_expires_at',    
        'pax',
        'publish',
        'images', 
    ];

    protected $casts = [
        'basic_price' => 'decimal:2', 
        'nta' => 'decimal:2',         
        'tax_rate' => 'decimal:2',
        'discount_value' => 'decimal:2', 
        'discount_expires_at' => 'datetime',    
        'pax' => 'integer',
        'publish' => 'boolean',
        'deleted_at' => 'datetime',
        'images' => 'array',
    ];
    
  protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid();
            }
        });
    }

    // --- ACCESSOR BARU UNTUK MENGHITUNG HARGA PUBLIK SETELAH DISKON ---

    public function getFinalPriceAttribute(): float
    {
        $price = $this->basic_price; 
        
        // 1. Cek Diskon
        if ($this->discount_type && $this->discount_value > 0) {
            // Cek apakah diskon masih berlaku (jika tanggal kadaluarsa tidak null)
            if (!$this->discount_expires_at || $this->discount_expires_at->isFuture()) {
                
                if ($this->discount_type === 'percentage') {
                    // Hitung harga setelah diskon persentase
                    $price -= ($price * ($this->discount_value / 100));
                } elseif ($this->discount_type === 'fixed') {
                    // Hitung harga setelah diskon harga tetap
                    $price -= $this->discount_value;
                }
            }
        }

        // Pastikan harga tidak negatif
        return max(0, round($price, 2));
    }

    // --- RELATIONS ---

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'id_vendor');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function averageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Get the wishlists for the addon.
     */
    public function wishlists()
    {
        return $this->morphMany(\App\Models\Wishlist::class, 'wishable');
    }
}
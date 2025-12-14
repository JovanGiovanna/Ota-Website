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
        'upsell',
        'discount_type',
        'discount_value',
        'discount_amount',
        'discount_expires_at',
        'total_price_before_discount',
        'final_price',
        'pax',
        'location',
        'address',
        'phone',
        'images',
        'jumlah',
        'refund_policy',
    ];

    protected $casts = [
        'basic_price' => 'decimal:2', 
        'nta' => 'decimal:2',         
        'upsell' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_price_before_discount' => 'decimal:2',
        'final_price' => 'decimal:2',
        'discount_expires_at' => 'datetime',    
        'pax' => 'integer',
        'jumlah' => 'integer',
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
        $nta = $this->nta ?? $this->basic_price;
        $upsell = $this->upsell ?? 0;

        $priceBeforeDiscount = (float) $nta + (float) $upsell;

        // 1. Cek Diskon
        $discountAmount = 0;
        if ($this->discount_type && $this->discount_value > 0) {
            if (!$this->discount_expires_at || $this->discount_expires_at->isFuture()) {
                if ($this->discount_type === 'percentage') {
                    $discountAmount = $priceBeforeDiscount * ($this->discount_value / 100);
                } elseif ($this->discount_type === 'fixed') {
                    $discountAmount = $this->discount_value;
                }
            }
        }

        $final = $priceBeforeDiscount - $discountAmount;
        return max(0, round($final, 2));
    }

    /**
     * Hitung jumlah diskon berdasarkan tipe diskon.
     * Perhitungan: basic_price + (basic_price * tax_rate / 100) - discount
     */
    public function getCalculatedDiscountAmountAttribute(): float
    {
        $basicPrice = $this->basic_price;
        $upsell = $this->upsell ?? 0;
        $discountType = $this->discount_type;
        $discountValue = $this->discount_value ?? 0;
        $expiry = $this->discount_expires_at;

        // Cek kadaluarsa diskon
        if ($expiry && $expiry->isPast()) {
            return 0.00;
        }

        // Hitung harga sebelum diskon: basic_price + upsell
        $priceBeforeDiscount = $basicPrice + $upsell;

        $discountAmount = 0.00;

        if ($discountType === 'percentage' && $discountValue > 0) {
            // Diskon persentase dari harga sebelum diskon
            $discountAmount = $priceBeforeDiscount * ($discountValue / 100);
        } elseif ($discountType === 'fixed' && $discountValue > 0) {
            // Diskon tetap
            $discountAmount = $discountValue;
        }

        // Pastikan jumlah diskon tidak menjadi negatif
        return max(0, round($discountAmount, 2));
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
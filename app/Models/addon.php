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
        'pax',
        'publish',
        'images', 
    ];

    protected $casts = [
        'basic_price' => 'decimal:2', 
        'nta' => 'decimal:2',         
        'tax_rate' => 'decimal:2',    
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
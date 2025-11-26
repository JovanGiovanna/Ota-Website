<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookProduct extends Model
{
    use HasFactory;

    protected $table = 'book_products';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id_book', 'id_product', 'amount', 'total_price', 'booking_code'];

    protected $casts = [
        'total_price' => 'decimal:2',
        'amount' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    public function booking(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Booking::class, 'id_book');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Product::class, 'id_product');
    }

    public function bookProductAddons(): HasMany
    {
        return $this->hasMany(\App\Models\BookProductAddon::class, 'id_book', 'id');
    }
}

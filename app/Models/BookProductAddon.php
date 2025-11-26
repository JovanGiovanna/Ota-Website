<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BookProductAddon extends Model
{
    use HasFactory;

    protected $table = 'book_product_addons';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'id_book', 'id_product', 'id_addons', 'quantity', 'price'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function bookProduct()
    {
        return $this->belongsTo(BookProduct::class, 'id_book');
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'id_product');
    }

    public function addon()
    {
        return $this->belongsTo(\App\Models\Addon::class, 'id_addons');
    }
}

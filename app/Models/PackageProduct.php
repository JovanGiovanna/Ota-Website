<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageProduct extends Model
{
    use HasFactory;

    // Nama tabel yang direpresentasikan oleh model ini
    protected $table = 'package_products';

    // Kolom yang dapat diisi secara massal (mass assignable)
    protected $fillable = [
        'id_package',
        'id_product',
    ];

    // Menunjukkan bahwa primary key adalah UUID dan tidak auto-incrementing
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Boot the model.
     * Mengatur pembuatan UUID secara otomatis sebelum model dibuat.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }

    // --- Relasi ---

    /**
     * Relasi many-to-one ke model Package.
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'id_package');
    }

    /**
     * Relasi many-to-one ke model Product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'id_product');
    }
}
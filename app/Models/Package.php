<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $table = 'packages';

    protected $fillable = [
        'id',
        'name_package',
        'slug',
        'description',
        'image',
        'price_publish',
        'start_publish',
        'end_publish',
        'is_active',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    protected $casts = [
        'price_publish' => 'decimal:2',
        'start_publish' => 'datetime',
        'end_publish' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'package_products', 'id_package', 'id_product');
    }
}

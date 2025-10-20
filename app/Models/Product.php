<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'id',
        'name',
        'image',
        'description',
        'price',
        'id_category',
        'id_vendor',
        'jumlah',
        'max_adults',
        'max_children',
        'status',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'id_vendor');
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_products', 'id_product', 'id_package');
    }
}

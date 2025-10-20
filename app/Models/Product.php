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

    protected $fillable = [
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

    protected $casts = [
        'price' => 'decimal:2',
        'max_adults' => 'integer',
        'max_children' => 'integer',
        'jumlah' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'id_category');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'id_vendor');
    }
}

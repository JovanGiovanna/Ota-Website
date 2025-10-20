<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'city';

    protected $fillable = [
        'id_province',
        'name',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'id_province');
    }

    public function vendorInfos(): HasMany
    {
        return $this->hasMany(VendorInfo::class, 'id_city');
    }
}

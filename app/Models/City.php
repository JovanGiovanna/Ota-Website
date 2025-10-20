<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $table = 'city';

    protected $fillable = [
        'id',
        'id_province',
        'name',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    public function province()
    {
        return $this->belongsTo(Province::class, 'id_province');
    }
}

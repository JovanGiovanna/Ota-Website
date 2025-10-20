<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $table = 'province';

    protected $fillable = [
        'id',
        'name',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    public function cities()
    {
        return $this->hasMany(City::class, 'id_province');
    }
}

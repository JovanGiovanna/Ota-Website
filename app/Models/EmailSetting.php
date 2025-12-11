<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'label',
        'email',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}

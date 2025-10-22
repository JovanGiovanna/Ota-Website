<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Type extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'types';

    /**
     * Tipe kunci primer (UUID).
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Menunjukkan apakah ID otomatis bertambah.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Kolom-kolom yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'status',
    ];

    /**
     * Kolom-kolom yang harus di-cast ke tipe data asli.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'boolean',
    ];
    
    /**
     * Event boot untuk membuat UUID saat model baru dibuat.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            // Mengisi kunci primer (ID) dengan UUID jika belum diatur
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
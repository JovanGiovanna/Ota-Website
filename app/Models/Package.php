<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Package extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'packages';

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
        'name_package',
        'slug',
        'description',
        'image',
        'price_publish',
        'start_publish',
        'end_publish',
        'is_active',
    ];

    /**
     * Kolom-kolom yang harus di-cast ke tipe data asli.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price_publish' => 'decimal:2',
        'start_publish' => 'datetime',
        'end_publish' => 'datetime',
        'is_active' => 'boolean',
    ];
    
    /**
     * Event boot untuk membuat UUID saat model baru dibuat.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Menggunakan kolom 'slug' untuk binding rute model implisit.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
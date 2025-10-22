<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Addon extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'addons';

    /**
     * Kunci utama adalah UUID, bukan integer auto-increment.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Nonaktifkan auto-incrementing untuk primary key.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_vendor',
        'addons',
        'desc',
        'status',
        'price',
        'publish',
        'image_url',
    ];

    /**
     * Atribut yang harus di-casting.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'publish' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    // --- Booting Model ---
    
    /**
     * Metode boot model. Digunakan untuk membuat UUID sebelum model disimpan.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Pastikan ID diset sebagai UUID jika belum ada
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid();
            }
        });
    }

    // --- Relasi ---

    /**
     * Mendapatkan vendor yang memiliki addon ini.
     * Relasi BelongsTo ke model 'Vendor' dengan foreign key 'id_vendor'.
     */
    public function vendor(): BelongsTo
    {
        // Asumsi model untuk tabel 'vendor' adalah 'Vendor'
        return $this->belongsTo(Vendor::class, 'id_vendor');
    }
}
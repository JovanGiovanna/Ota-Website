<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BookPackageAddon extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'book_package_addons';

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
        'id_book',
        'id_addons',
    ];

    /**
     * Atribut yang harus di-casting.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'string',
        'id_book' => 'string',
        'id_addons' => 'string',
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
     * Mendapatkan booking yang terkait dengan book package addon ini.
     * Relasi BelongsTo ke model 'Booking' dengan foreign key 'id_book'.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'id_book');
    }

    /**
     * Mendapatkan addon yang terkait dengan book package addon ini.
     * Relasi BelongsTo ke model 'Addon' dengan foreign key 'id_addons'.
     */
    public function addon(): BelongsTo
    {
        return $this->belongsTo(Addon::class, 'id_addons');
    }
}

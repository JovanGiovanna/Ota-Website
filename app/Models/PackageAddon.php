<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageAddon extends Model
{
    use HasFactory, HasUuids;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'package_addon';

    /**
     * Kolom yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_package',
        'id_addons',
        'note',
        // Tambahkan kolom lain jika ada
    ];

    /**
     * Tipe data untuk kolom 'id' (UUID).
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Tunjukkan bahwa primary key bukanlah integer yang bertambah otomatis.
     *
     * @var bool
     */
    public $incrementing = false;

    // --- Definisi Relasi ---

    /**
     * Mendapatkan package yang terkait dengan entri ini.
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'id_package', 'id');
    }

    /**
     * Mendapatkan addon yang terkait dengan entri ini.
     */
    public function addon(): BelongsTo
    {
        return $this->belongsTo(Addon::class, 'id_addons', 'id');
    }
}
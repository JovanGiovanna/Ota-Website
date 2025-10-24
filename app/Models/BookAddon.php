<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BookAddon extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'book_addons';

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
        'id_user',
        'id_addon',
        'checkin_appointment_start',
        'checkout_appointment_end',
        'amount',
        'total_price',
        'booker_name',
        'booker_email',
        'booker_telp',
        'booking_code',
        'status',
        'notes',
    ];

    /**
     * Atribut yang harus di-casting.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'checkin_appointment_start' => 'datetime',
        'checkout_appointment_end' => 'datetime',
        'total_price' => 'decimal:2',
        'amount' => 'integer',
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
     * Mendapatkan user yang booking addon ini.
     * Relasi BelongsTo ke model 'User' dengan foreign key 'id_user'.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Mendapatkan addon yang dibooking.
     * Relasi BelongsTo ke model 'Addon' dengan foreign key 'id_addon'.
     */
    public function addon()
    {
        return $this->belongsTo(Addon::class, 'id_addon');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Untuk boot UUID

class BookProduct extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'book_products';

    /**
     * Menunjukkan bahwa primary key bersifat UUID dan tidak auto-increment.
     *
     * @var string
     */
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'id_user',
        'id_product',
        'checkin_appointment_start_datetime',
        'checkout_appointment_end_datetime',
        'amount',
        'booker_name',
        'booker_email',
        'booker_telp',
    ];

    /**
     * Casting tipe data untuk atribut.
     *
     * @var array
     */
    protected $casts = [
        'checkin_appointment_start_datetime' => 'datetime',
        'checkout_appointment_end_datetime' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Menetapkan UUID secara otomatis saat membuat record baru.
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
     * Definisi relasi: Pemesanan ini milik seorang User.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Definisi relasi: Pemesanan ini terkait dengan satu Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }
}
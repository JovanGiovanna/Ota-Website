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
        'id_book',
        'id_product',
        'amount',
        'total_price',
    ];

    /**
     * Casting tipe data untuk atribut.
     *
     * @var array
     */
    protected $casts = [
        'id_book' => 'string',
        'id_product' => 'string',
        'amount' => 'integer',
        'total_price' => 'decimal:2',
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
     * Definisi relasi: Pemesanan ini terkait dengan satu Booking.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'id_book');
    }

    /**
     * Definisi relasi: Pemesanan ini terkait dengan satu Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fasilitas extends Model
{
    use HasFactory;

    protected $table = 'fasilitas';

    protected $fillable = [
        'name',
        'image',
        'description',
        'price',
        'id_category',
        'jumlah',
        'status',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'id_category');
    }

    public function detailfasilitas(): HasMany
    {
        return $this->hasMany(detail_fasilitas::class, 'fasilitas_id', 'id');
    }
    
    public function bookingfasilitas(): HasMany
    {
        return $this->hasMany(detail_fasilitas::class, 'fasilitas_id', 'id');
    }

    /**
     * Helper untuk menghitung jumlah booking fasilitas yang aktif.
     * Status 'diproses' dan 'checkin' berada di tabel 'BookingFasilitas' (melalui relasi di detail_fasilitas).
     * @return int
     */
    public function getActiveBookingsCount(): int
    {
        return $this->bookingFasilitas()
            ->whereHas('bookingfasilitas', function ($query) { 
                $query->whereIn('status', ['diproses', 'checkin']);
            })
            ->count();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Package;
use App\Models\Addon;
use App\Models\Product;
use App\Models\BookPackage;
use App\Models\BookPackageAddon;
use App\Models\BookProduct;
use App\Models\BookProductAddon;
use App\Models\BookAddon;
use App\Models\Review;

class Booking extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $casts = [
        'id' => 'string',
        'checkin_appointment_start' => 'datetime',
        'checkout_appointment_end' => 'datetime',
        'total_price' => 'decimal:2',
    ];

    protected $fillable = [
        'id_user',
        'booker_name',
        'booker_email',
        'booker_telp',
        'booking_code',
        'checkin_appointment_start',
        'checkout_appointment_end',
        'duration_days',
        'amount',
        'total_price',
        'status',
        'note'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    // --- RELATIONS ---

    // User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Packages booked (BookPackage)
    public function packages(): HasMany
    {
        return $this->hasMany(BookPackage::class, 'id_book', 'id');
    }

    // Products booked (BookProduct)
    public function products(): HasMany
    {
        return $this->hasMany(BookProduct::class, 'id_book', 'id');
    }

    // Standalone Addons booked (BookAddon)
    public function addons(): HasMany
    {
        return $this->hasMany(BookAddon::class, 'id_book', 'id');
    }

    // Package Addons booked (BookPackageAddon)
    public function bookPackageAddons(): HasMany
    {
        return $this->hasMany(BookPackageAddon::class, 'id_book', 'id');
    }

    // Reviews
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // Detail Booking (if used)
    public function detailBookings(): HasMany
    {
        return $this->hasMany(Detail_Booking::class);
    }

    // Scope Active Bookings
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed', 'checked_in']);
    }

    // Hitung durasi booking dalam hari
    public function calculateDuration(): int
    {
        if (!$this->checkin_appointment_start || !$this->checkout_appointment_end) return 0;
        return Carbon::parse($this->checkout_appointment_end)
                     ->diffInDays(Carbon::parse($this->checkin_appointment_start));
    }
}

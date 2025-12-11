<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BookPackage extends Model
{
    use HasFactory;

    protected $table = 'book_packages';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'id_book',
        'id_user',
        'id_package',
        'booker_name',
        'booker_email',
        'booker_telp',
        'booking_code',
        'checkin_appointment_start',
        'checkout_appointment_end',
        'quantity',
        'total_price',
        'status',
        'notes',
        'stock_applied',
        'revenue_applied',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'stock_applied' => 'boolean',
        'revenue_applied' => 'boolean',
    ];

    /**
     * Auto-generate UUID saat creating
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // --- RELATIONS ---

    /**
     * Booking master
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'id_book');
    }

    /**
     * User yang melakukan booking
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Package yang dibooking
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'id_package');
    }

/**
 * Addon untuk package ini
 */
public function bookPackageAddons(): HasMany
{
    return $this->hasMany(BookPackageAddon::class, 'id_book', 'id_book');
}

}

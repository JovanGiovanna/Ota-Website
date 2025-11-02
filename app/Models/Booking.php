<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
// 💡 Pastikan Anda mengimpor Model-model yang berelasi
use App\Models\User;
use App\Models\Detail_Booking; // 💡 Perbaikan: Gunakan Detail_Booking dengan underscore sesuai nama file
use App\Models\Addon;

class Booking extends Model
{
    use HasFactory;

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

    // --- CASTS ---
    protected $casts = [
        'id' => 'string',
        'checkin_appointment_start' => 'datetime',
        'checkout_appointment_end' => 'datetime',
        'total_price' => 'decimal:2',
    ];

    // --- FILLABLE ---
    protected $fillable = [
        'id_user',
        'id_package', // Nullable for flexible bookings
        'booker_name',
        'booker_email',
        'booker_telp',
        'checkin_appointment_start',
        'checkout_appointment_end',
        'duration_days',
        'amount',
        'total_price',
        'status',
        'note',
    ];
    

    protected static function boot()
{
    parent::boot();

    // Auto-generate UUID setiap kali membuat Booking baru
    static::creating(function ($model) {
        if (empty($model->id)) {
            $model->id = (string) \Illuminate\Support\Str::uuid();
        }
    });
}

    // --- RELATIONS ---

    /**
     * Relasi ke User (Pemesan).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Relasi ke Package (paket yang dipesan).
     * @return BelongsTo
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'id_package');
    }
    
    /**
     * Relasi ke Addons (Many-to-Many through BookPackageAddon).
     * @return BelongsToMany
     */
    public function addons(): BelongsToMany
    {
        return $this->belongsToMany(Addon::class, 'book_package_addons', 'id_book', 'id_addons')
                    ->withPivot('id_package') // Mengambil kolom id_package dari tabel pivot
                    ->withTimestamps();
    }

    /**
     * Relasi ke Packages (Many-to-Many through BookPackageAddon).
     * @return BelongsToMany
     */
    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'book_package_addons', 'id_book', 'id_package')
                    ->withPivot('id_addons') // Mengambil kolom id_addons dari tabel pivot
                    ->withTimestamps();
    }

    /**
     * Relasi ke Products (Many-to-Many through BookProduct).
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'book_products', 'id_book', 'id_product')
                    ->withPivot('amount', 'total_price') // Mengambil kolom amount dan total_price dari tabel pivot
                    ->withTimestamps();
    }
    
    // --- UTILITIES ---

    /**
     * Menghitung durasi booking secara manual (hari).
     * @return int
     */
    public function calculateDuration(): int
    {
        if (!$this->checkin_appointment_start || !$this->checkout_appointment_end) {
            return 0;
        }

        if ($this->checkin_appointment_start instanceof Carbon && $this->checkout_appointment_end instanceof Carbon) {
             return $this->checkout_appointment_end->diffInDays($this->checkin_appointment_start);
        }

        return Carbon::parse($this->checkout_appointment_end)->diffInDays(Carbon::parse($this->checkin_appointment_start));
    }

    /**
     * Relasi ke Detail_Booking (HasMany).
     * @return HasMany
     */
    public function detailBookings(): HasMany
    {
        return $this->hasMany(Detail_Booking::class);
    }

    /**
     * Scope query untuk mengambil booking yang masih aktif.
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed', 'checked_in']);
    }

    /**
     * Get the reviews for the booking.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}

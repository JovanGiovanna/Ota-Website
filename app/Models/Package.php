<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 
use Illuminate\Support\Str;

class Package extends Model
{
    use HasFactory, SoftDeletes; // Menggunakan SoftDeletes

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'packages';

    /**
     * Tipe kunci primer (UUID).
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Menunjukkan apakah ID otomatis bertambah.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Kolom-kolom yang dapat diisi secara massal (mass assignable).
     * Menghapus id_product, id_addons, dan pax tunggal. Menambahkan kolom JSON.
     * @var array<int, string>
     */
    protected $fillable = [
        'name_package',
        'slug',
        'description',
        'image',
        'price_real',
        'price_publish',
        'start_publish',
        'end_publish',
        'is_active',
        'products_data', 
        'addons_data',
    ];

    /**
     * Kolom-kolom yang harus di-cast ke tipe data asli.
     * @var array<string, string>
     */
    protected $casts = [
        'price_publish' => 'decimal:2',
        'price_real' => 'decimal:2', 
        'start_publish' => 'datetime',
        'end_publish' => 'datetime',
        'is_active' => 'boolean',
        'products_data' => 'array',
        'addons_data' => 'array',
    ];
    
    /**
     * Event boot untuk membuat UUID saat model baru dibuat.
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

    // --- Relasi (Relasi product() dan addon() dihapus karena sudah diganti data JSON) ---

    /**
     * Get the vendor info for the package.
     */
    public function vendorInfo()
    {
        return $this->belongsTo(\App\Models\VendorInfo::class, 'id_vendor_info');
    }

    /**
     * Get the type for the package.
     */
    public function type()
    {
        return $this->belongsTo(\App\Models\Type::class, 'id_type');
    }

    /**
     * Get the reviews for the package.
     */
    public function reviews()
    {
        // Pastikan model Review sudah diimport atau menggunakan FQCN
        return $this->hasMany(\App\Models\Review::class);
    }

    /**
     * Get the average rating for the package.
     */
    public function averageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }
}
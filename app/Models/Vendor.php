<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Vendor extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    /**
     * Nama tabel di database.
     * @var string
     */
    protected $table = 'vendor';

    /**
     * Kolom yang dapat diisi secara massal (mass assignable).
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    /**
     * Kolom yang harus disembunyikan saat serialisasi (misalnya saat merespons API).
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Kolom yang harus di-cast ke tipe data tertentu.
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Digunakan untuk hashing password secara otomatis (Laravel 10+)
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke VendorInfo.
     */
    public function vendorInfo()
    {
        return $this->hasOne(VendorInfo::class, 'id_vendor');
    }
}